<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Calendars\SyncEngines;

use App\CalendarConnection;
use App\CalendarConnectionEntry;
use App\Calendars\AbstractCalendar;
use App\Calendars\AbstractCalendarItem;
use App\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class AbstractSyncEngine
 * @package App\Calendars\SyncEngines
 */
abstract class AbstractSyncEngine
{
    public static $prefix = '';
    public const AUTO_WARNING = '<p><small>Bitte beachte: Dies ist ein automatisch vom Pfarrplaner erzeugter Eintrag. Änderungen können jederzeit ohne Vorwarnungen vom Pfarrplaner überschrieben werden.</small></p>';

    /** @var CalendarConnection */
    protected $calendarConnection = null;

    /** @var AbstractCalendar */
    protected $calendar = null;

    public function __construct(CalendarConnection $calendarConnection)
    {
        $this->setCalendarConnection($calendarConnection);
    }

    /**
     * @return string
     */
    public static function getPrefix(): string
    {
        return self::$prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return CalendarConnection
     */
    public function getCalendarConnection(): ?CalendarConnection
    {
        return $this->calendarConnection;
    }

    /**
     * @param CalendarConnection $calendarConnection
     */
    public function setCalendarConnection(?CalendarConnection $calendarConnection): void
    {
        $this->calendarConnection = $calendarConnection;
    }

    /**
     * @return AbstractCalendar
     */
    public function getCalendar(): ?AbstractCalendar
    {
        return $this->calendar;
    }

    /**
     * @param AbstractCalendar $calendar
     */
    public function setCalendar(?AbstractCalendar $calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * Sync a collection of services
     * @param Collection $services
     */
    public function syncServices(Collection $services)
    {
        /** @var Service $service */
        foreach ($services as $service) {
            $this->syncSingleService($service);
        }
    }

    /**
     * Sync a single service
     * @param Service $service
     */
    public function syncSingleService(Service $service)
    {
        $record = [
            'startDate' => $service->dateTime->copy()->format('Y-m-d H:i:s'),
            'endDate' => $service->dateTime->copy()->addHour(1)->format('Y-m-d H:i:s'),
            'title' => $service->titleTextWithParticipants(),
            'location' => $service->locationText(),
            'description' => '<p><a href="'
                . route('services.edit', $service->id) . '">'
                . 'Im Pfarrplaner ansehen'
                . '</a></p><p></p>'
                . self::AUTO_WARNING,
        ];

        $item = null;
        if ($entry = CalendarConnectionEntry::where('service_id', $service->id)->where(
            'calendar_connection_id',
            $this->calendarConnection->id
        )->first()) {
            Log::debug('Found entry reference #' . $entry->id . ' --> external calendar event #' . $entry->foreign_id);
            $item = $this->calendar->find($entry->foreign_id);
        }

        /** @var AbstractCalendarItem $item */
        if ($item) {
            Log::debug('Updating existing external calendar event #' . $item->getID(), [$item]);
            $item->update($record);
        } else {
            // obsolete entry? (there's no associated item on the server)
            if ($entry) {
                Log::error(
                    'Existing external calendar event #' . $entry->foreign_id . ' not found, deleting reference'
                );
                $entry->delete();
            }
            Log::debug(
                'Creating new calendar item for service #' . $service->id . ' on CalendarConnection #' . $this->calendarConnection->id
            );
            $item = $this->calendar->create($record);
            if ($item) {
                $entry = CalendarConnectionEntry::create(
                    [
                        'calendar_connection_id' => $this->calendarConnection->id,
                        'service_id' => $service->id,
                        'foreign_id' => $item->getID(),
                    ]
                );
                Log::debug('Calendar item created with #' . $item->getID() . ', local reference entry #' . $entry->id);
            } else {
                Log::error(
                    'Failed to create calendar item for service #' . $service->id
                    . ' on CalendarConnection #' . $this->calendarConnection->id,
                    [$record]
                );
            }
        }

        // sync alternate (prep) dates:
        if ($this->calendarConnection->include_alternate) {
            foreach ([$service->weddings, $service->funerals, $service->baptisms] as $collection) {
                if (count($collection)) {
                    foreach ($collection as $collectionItem) {
                        $result = $collectionItem->getPreparationEvent();
                        if ($result) {
                            foreach ($result as $prepKey => $prepEvent) {
                                $this->syncSingleAlternateEvent($prepKey, $prepEvent);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Sync a single alterante event
     * @param $key Event key
     * @param $record Event data
     */
    public function syncSingleAlternateEvent($key, $record)
    {
        if ($entry = CalendarConnectionEntry::where('calendar_connection_id', $this->calendarConnection->id)
            ->where('alternate_key', $key)->first()
        ) {
            $item = $this->calendar->find($entry->foreign_id);
        } else {
            $item = null;
        }
        if ($item) {
            $item->update($record);
            Log::debug(
                'Updated record #' . $item->getID(
                ) . ' for alternate event #' . $key . ' on CalendarConnection ' . $this->calendarConnection->id
            );
        } else {
            if ($entry) {
                Log::error(
                    'Existing external calendar event #' . $entry->foreign_id . ' not found, deleting reference'
                );
                $entry->delete();
            }

            $item = $this->calendar->create($record);
            if ($item) {
                $entry = CalendarConnectionEntry::create(
                    [
                        'calendar_connection_id' => $this->calendarConnection->id,
                        'service_id' => null,
                        'alternate_key' => $key,
                        'foreign_id' => $item->getID(),
                    ]
                );
                Log::debug(
                    'Created record #' . $item->getID(
                    ) . ' for alternate event #' . $key . ' on CalendarConnection ' . $this->calendarConnection->id
                );
            } else {
                Log::error(
                    'Failed to create record for alternate event #' . $key . ' on CalendarConnection ' . $this->calendarConnection->id
                );
            }
        }
    }

    /**
     * Delete a single event by its connection entry
     * @param CalendarConnectionEntry $entry
     */
    public function deleteSingleEntry(CalendarConnectionEntry $entry)
    {
        if ($entry->service) {
            $this->deleteSingleService($entry->service);
        } else {
            $this->deleteSingleAlternateEvent($entry->alternate_key);
        }
    }

    /**
     * Remove a single service from the external calendar
     * @param Service $service
     */
    public function deleteSingleService(Service $service)
    {
        Log::debug(
            'Trying to delete service #' . $service->id . ' on CalendarConnection #' . $this->calendarConnection->id
        );
        if ($entry = CalendarConnectionEntry::where('service_id', $service->id)->where(
            'calendar_connection_id',
            $this->calendarConnection->id
        )->first()) {
            Log::debug(
                'Found entry reference #' . $entry->id . ' --> external calendar event #' . $entry->foreign_id
            );
            /** @var AbstractCalendarItem $item */
            $item = $this->calendar->find($entry->foreign_id);
            if ($item) {
                Log::debug('Deleting existing external calendar event #' . $entry->foreign_id);
                $item->delete();
                $entry->delete();
            } else {
                Log::error(
                    'Existing external calendar event #' . $entry->foreign_id . ' not found, deleting reference'
                );
                $entry->delete();
            }
        } else {
            Log::error('No corresponding entry reference found.');
        }

        // delete alternate (prep) dates:
        if ($this->calendarConnection->include_alternate) {
            foreach ([$service->weddings, $service->funerals, $service->baptisms] as $collection) {
                if (count($collection)) {
                    foreach ($collection as $collectionItem) {
                        $result = $collectionItem->getPreparationEvent();
                        if ($result) {
                            foreach ($result as $prepKey => $prepEvent) {
                                $this->deleteSingleAlternateEvent($prepKey);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * Remove a single alternate event by its key
     * @param $key
     */
    public function deleteSingleAlternateEvent($key)
    {
        if ($entry = CalendarConnectionEntry::where('calendar_connection_id', $this->calendarConnection->id)
            ->where('alternate_key', $key)->first()) {
            $this->calendar->delete($entry->foreign_id);
        }
    }
}
