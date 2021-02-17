<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

namespace App\Liturgy\Replacement;


use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Service;
use App\Tools\StringTool;
use Carbon\Carbon;

class BekanntgabenReplacer extends AbstractReplacer
{
    protected function getReplacementText(): string
    {
        $service = $this->service;
        $lastWeek = Carbon::createFromTimeString($service->day->date->format('Y-m-d') . ' 0:00:00 last Sunday');
        $nextWeek = Carbon::createFromTimeString($service->day->date->format('Y-m-d') . ' 0:00:00 next Sunday');
        $lastServices = Service::select('*')
            ->join('days', 'days.id', 'services.day_id')
            ->whereHas(
                'day',
                function ($query) use ($service) {
                    $query->where('date', '<=', $service->day->date->format('Y-m-d'));
                }
            )
            ->where('offering_amount', '!=', '')
            ->orderByDesc('days.date')
            ->limit(10)
            ->get();

        $lastServiceDate = null;
        $offeringAmount = 0;
        foreach ($lastServices as $lastService) {
            if ($lastServiceDate === null) {
                $lastServiceDate = $lastService->day->date;
            }
            if ($lastServiceDate != $lastService->day->date) {
                continue;
            }
            $x = trim(strtr((string)$lastService->offering_amount, [',' => '.', '€' => '']));
            if (is_numeric($x)) {
                $offeringAmount += (float)$x;
            }
        }

        $services = Service::with(['day', 'location'])
            ->notHidden()
            ->whereHas(
                'day',
                function ($query) use ($service, $nextWeek) {
                    $query->where('date', '>=', $service->day->date);
                    $query->where('date', '<=', $nextWeek);
                    $query->where('city_id', $service->city->id);
                    $query->where('id', '!=', $service->id);
                }
            )
            ->get();

        $events = [];

        $calendar = new EventCalendarImport($service->city->public_events_calendar_url);
        $events = $calendar->mix($events, $service->day->date, $nextWeek, true);

        $events = Service::mix($events, $services, $service->day->date, $nextWeek);

        $op = new OPEventsImport($service->city);
        $events = $op->mix($events, $service->day->date, $nextWeek);


        $fmt = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);


        $text = 'Herzlichen Dank für das Opfer der Gottesdienste vom '
            . $lastServiceDate->format('d.m.Y') . ' in Höhe von '
            . $fmt->formatCurrency($offeringAmount, 'EUR') . '.'
            . PHP_EOL . PHP_EOL;

        $text .= 'Das heutige Opfer ist für folgenden Zweck bestimmt: ' . $service->offering_goal . PHP_EOL ;

        $ctr = 0;
        $lastDay = $nextWeek->format('Ymd');
        foreach ($events as $eventsArray) {
            foreach ($eventsArray as $event) {
                $eventStart = is_array($event) ? $event['start'] : $event->day->date;
                $dateFormat = $ctr ? '%A, %d. %B' : '%A, %d. %B %Y';
                $done = false;

                if (is_array($event)) {
                    if ($service->day->date->format('Ymd') == $eventStart->format('Ymd')) {
                        if ($event['allDay']) {
                            $text .= $this->renderParagraph($event['title']);
                            $done = true;
                        }
                    }
                }

                if (!$done) {
                    if ($lastDay != $eventStart->format('Ymd')) {
                        $text .= $this->renderParagraph();
                        if ($nextWeek->format('Ymd') == $eventStart->format('Ymd')) {
                            $text .= $this->renderParagraph('Vorschau');
                        }

                        $text .= $this->renderParagraph(
                            ($service->day->date->format('Ymd') == $eventStart->format('Ymd')) ?
                                'Heute' : strftime($dateFormat, $eventStart->getTimestamp())
                        );
                    }

                    if (is_array($event)) {
                        $text .= $this->renderParagraph(
                            (isset($event['allDay']) && $event['allDay']) ? '' : strftime(
                                    '%H.%M Uhr',
                                    $eventStart->getTimestamp()
                                ) . "\t" . trim(
                                    StringTool::sanitizeXMLString(
                                        $event['title']
                                    ) . ' (' . StringTool::sanitizeXMLString($event['place']) . ')'
                                )
                        );
                    } else {
                        $description = $event->descriptionText();
                        $description = $description ? ' mit ' . $description : '';
                        // take care of ampersands
                        $description = preg_replace('/&(?![A-Za-z0-9#]{1,7};)/', '&amp;', $description);
                        $text .= $this->renderParagraph(
                            $event->timeText(true, '.') . "\t" . trim(
                                ($event->title ?: 'Gottesdienst') . $description . ' (' . $event->locationText() . ')'
                            )
                        );

                        // add children's church
                        if ($event->cc) {
                            $text .= $this->renderParagraph(
                                Carbon::createFromFormat(
                                    'Y-m-d H:i',
                                    $event->day->date->format(
                                        'Y-m-d'
                                    ) . ' ' . ($event->cc_alt_time ?? $event->time)
                                )->formatLocalized(
                                    '%H.%M Uhr'
                                ) . "\t" . 'Kinderkirche' . ' (' . ($event->cc_location ?? $event->locationText()) . ')'
                            );
                        }
                    }


                    if ((isset($event['allDay']) && $event['allDay'])) {
                        $text .= $this->renderParagraph();
                    }

                    $lastDay = $eventStart->format('Ymd');
                }

                $ctr++;
            }
        }

        return $text;
    }

}
