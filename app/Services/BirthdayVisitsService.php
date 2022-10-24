<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Services;

use App\CalendarConnection;
use App\Calendars\Exchange\ExchangeCalendar;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BirthdayVisitsService
{

    /** @var string temp folder path */
    protected $tempFolder;

    /** @var string|null  last error message */
    protected $error = null;

    /** @var string $sourceFile */
    protected $sourceFile;

    /** @var array Setup data */
    protected $setup = [];

    /**
     * Constructor
     *
     * Set up temporary folder
     */
    public function __construct($sourceFile, $setup)
    {
        $this->sourceFile = $sourceFile;
        $this->setup = $setup;
        $this->tempFolder = tempnam(sys_get_temp_dir(), 'pfarrplaner');
        unlink($this->tempFolder);
        mkdir($this->tempFolder);
    }

    /**
     * Destructor
     *
     * Release temporary folder
     */
    public function __destruct()
    {
        array_map('unlink', glob($this->tempFolder . '/*'));
        rmdir($this->tempFolder);
    }

    /**
     * Get the path of the excel source file
     *
     * @return bool|string Path to excel file or false if error
     */
    public function getExcelFile()
    {
        // check mime type
        if (mime_content_type($this->sourceFile) == 'application/vnd.ms-excel') return $this->sourceFile;
        if (mime_content_type($this->sourceFile) != 'application/zip') return $this->setError('Die Eingabedatei muss das Format .zip oder .xls haben.');

        // unzip .xls file
        $zip = new \ZipArchive;
        $zipResource = $zip->open($this->sourceFile);
        if (!$zipResource) {
            return $this->setError('ZIP-Datei kann nicht geöffnet werden.');
        }
        if ($this->setup['password']) {
            $zip->setPassword($this->setup['password']);
        }
        $zip->extractTo($this->tempFolder);
        $zip->close();
        return glob($this->tempFolder . '/*.xls')[0];
    }

    /**
     * Get list of birthdays from excel file
     *
     * @return array
     */
    public function getBirthdays()
    {
        $spreadsheet = IOFactory::load($this->getExcelFile());
        $birthdays = [];
        $rawData = $spreadsheet->getActiveSheet()->toArray();
        $headers = array_shift($rawData);
        foreach ($rawData as $row) {
            $record = [];
            foreach ($row as $id => $column) {
                $record[$headers[$id]] = $column;
            }

            $record['date'] = Carbon::createFromFormat('d.m.Y', $record['Geburtsdatum'])->setYear(Carbon::now()->year);
            if ($record['date'] < Carbon::now()) $record['date']->addYear(1);
            $record['original_date'] = $record['date']->copy();
            $record['weekday'] = $record['date']->dayOfWeekIso;

            // debug:
            $birthdays[] = $record;
        }
        return $birthdays;
    }

    /**
     * @return array
     */
    public function exportBirthdaysToCalendar()
    {
        $birthdays = $this->getBirthdays();
        $events = [];

        /** @var ExchangeCalendar $calendar */
        $calendar = CalendarConnection::findOrFail($this->setup['calendar'])->getSyncEngine()->getCalendar();

        foreach ($birthdays as $birthday) {
            $event = $this->autoScheduleBirthdayVisit($birthday);
            $calendar->create($event);
            $event['startDate']->setTimezone('Europe/Berlin');
            $event['endDate']->setTimezone('Europe/Berlin');
            $events[] = $event;
        }

        return $events;
    }

    /**
     * @param Carbon $date
     * @return Carbon
     */
    protected function findNextAvailableDay(Carbon $date): Carbon {
        while(!$this->setup['permitted'][$date->dayOfWeekIso]) {
            $date->addDay(1);
        }
        return $date;
    }

    /**
     * @param $birthday
     * @return array
     */
    public function autoScheduleBirthdayVisit($birthday)
    {
        /** @var ExchangeCalendar $calendar */
        $calendar = CalendarConnection::findOrFail($this->setup['calendar'])->getSyncEngine()->getCalendar();

        /** @var Carbon $date */
        $date = $birthday['date'];

        // find next suitable weekday
        if ($this->setup['forceNext']) $date->addDay(1);
        $date = $this->findNextAvailableDay($date);

        $events = [];
        foreach ($calendar->getAllEventsForRange($date->copy()->setTime(0,0,0), $date->copy()->setTime(23,59,59)) as $event) {
            if ((!$event->IsAllDayEvent) || ($event->LegacyFreeBusyStatus != 'Free')) $events[] = $event;
        }

        // setup first slot
        $slotStart = $date->copy()->setTime(substr($this->setup['earliest'], 0, 2), substr($this->setup['earliest'], 3, 2), 0);
        $slotEnd = $slotStart->copy()->addMinutes($this->setup['duration']+$this->setup['trailing']);

        $slotFound = false;
        while (!$slotFound) {
            $slotFound = $this->checkSlot($slotStart, $slotEnd, $events);
            if (!$slotFound) {
                $slotStart->addMinute(1);
                if ($slotStart > $slotStart->copy()->setTime(substr($this->setup['latest'], 0, 2), substr($this->setup['latest'], 3, 2))) {
                    // rollover to next available day
                    do {
                        $slotStart->addDay(1);
                    } while(!$this->setup['permitted'][$slotStart->dayOfWeekIso]);
                    $slotStart->setTime(substr($this->setup['earliest'], 0, 2), substr($this->setup['earliest'], 3, 2), 0);
                }
                $slotEnd = $slotStart->copy()->addMinutes($this->setup['duration']+$this->setup['trailing']);
            }
        }

        return [
            'startDate' => $slotStart->copy(),
            'endDate' => $slotStart->copy()->addMinutes($this->setup['duration']),
            'title' => 'Geburtstagsbesuch bei '.$birthday['Vorname'].' '.$birthday['Nachname'].' ('.$birthday['Alter']
                .' am '.$birthday['original_date']->isoFormat('dd, DD.MM.').')',
            'location' => $birthday['Straße'].' '.$birthday['HNr.'].', '.$birthday['PLZ'].' '.$birthday['Wohnort'],
            'description' => 'Dieser Geburtstagsbesuch wurde automatisch mit dem Pfarrplaner geplant. Du kannst ihn in deinem Kalender jederzeit an eine passendere Stelle verschieben.',
        ];

    }

    protected function insertMiddayBreak(Carbon $slot, $events): array {
        $start = Carbon::parse($slot->format('Y-m-d').' '.$this->setup['pauseFrom'], 'Europe/Berlin')->setTimezone('UTC')->addMinutes($this->setup['trailing'])->toIso8601ZuluString();
        $end = Carbon::parse($slot->format('Y-m-d').' '.$this->setup['pauseTo'], 'Europe/Berlin')->setTimezone('UTC')->subMinutes($this->setup['preceding'])->toIso8601ZuluString();

        $event = new \stdClass();
        $event->Start = $start;
        $event->End = $end;

        $events[] = $event;

        return $events;
    }

    protected function checkSlot(Carbon $start, Carbon $end, $events) {
        $blocking = [];

        $events = $this->insertMiddayBreak($start, $events);

        // check against all events
        foreach ($events as $event) {
            $eventStart = Carbon::parse($event->Start, 'UTC')->setTimezone('Europe/Berlin');
            $eventEnd = Carbon::parse($event->End, 'UTC')->setTimezone('Europe/Berlin')->addMinutes($this->setup['preceding']-1);

            // checks
            if (($eventStart < $start) && ($eventEnd > $end)) $blocking[] = $event;
            if (($eventStart >= $start) && ($eventStart <= $end)) $blocking[] = $event;
            if (($eventEnd >= $start) && ($eventEnd <= $end)) $blocking[] = $event;
        }

        return (count($blocking) == 0);
    }

    /**
     * @return string
     */
    public function getTempFolder()
    {
        return $this->tempFolder;
    }

    /**
     * @param string $tempFolder
     */
    public function setTempFolder($tempFolder): void
    {
        $this->tempFolder = $tempFolder;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     * @return bool
     */
    public function setError($error): bool
    {
        $this->error = $error;
        return false;
    }

    /**
     * @return string
     */
    public function getSourceFile(): string
    {
        return $this->sourceFile;
    }

    /**
     * @param string $sourceFile
     */
    public function setSourceFile(string $sourceFile): void
    {
        $this->sourceFile = $sourceFile;
    }

    /**
     * @return array
     */
    public function getSetup(): array
    {
        return $this->setup;
    }

    /**
     * @param array $setup
     */
    public function setSetup(array $setup): void
    {
        $this->setup = $setup;
    }


}
