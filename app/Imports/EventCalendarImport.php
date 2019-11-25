<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 01.11.2019
 * Time: 11:58
 */

namespace App\Imports;


use Carbon\Carbon;

class EventCalendarImport
{

    protected $url = '';

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getMixedEvents() {

    }

    /**
     * Mix an array of events into another array of events
     * @param $events
     * @param Carbon $weekStart
     * @param Carbon $weekEnd
     * @param bool $removeServices
     * @return array
     */
    public function mix($events, Carbon $weekStart, Carbon $weekEnd, $removeServices = false): array {
        $theseEvents = $this->getEvents($weekStart, $weekEnd, $removeServices);
        foreach ($theseEvents as $dateCode => $subEvents) {
            foreach ($subEvents as $event) {
                $events[$dateCode][] = $event;
            }
        }

        ksort($events);
        return $events;
    }

    public function getEvents(Carbon $weekStart, Carbon $weekEnd, $removeServices = false): array
    {
        $events = json_decode(file_get_contents($this->url), true);

        $filteredEvents = [];
        foreach ($events as $key => $event) {
            $event['record_type'] = 'Outlook_Event';
            $event['start'] = $this->sanitizeTimeString($event['start']);
            $event['end'] = $this->sanitizeTimeString($event['end']);

            // sanitize title
            $lines = explode("\r\n", $event['title']);
            $event['title'] = $this->sanitizeTitle($lines[0], $event);
            if (isset($lines[1])) {
                if (substr(trim($lines[1]), 0, 4) == 'Ort:') {
                    $event['place'] = $this->sanitizeLocation($lines[1]);
                }
            }

            if (!isset($event['place'])) $event['place'] = '';


            if (($event['start'] >= $weekStart) and ($event['start'] <= $weekEnd)) {
                if (!($event['allDay'] && ($event['place'] == ''))) {
                    if ((!$removeServices) || ((strtoupper(substr($event['title'], 0, 2)) != 'GD')
                            && (strtoupper(substr($event['title'], 0, 13)) != 'GOTTESDIENST ')
                        && (strtoupper($event['title']) != 'GOTTESDIENST'))) {
                        $filteredEvents[$event['start']->format('YmdHis')][] = $event;
                    }
                }
            }

        }
        ksort($filteredEvents);
        return $filteredEvents;
    }

    /**
     * Wandelt die Zeitangabe in ein DateTime-Objekt um und korrigiert die Zeitzone
     * @param string $time
     * @return DateTime
     */
    protected function sanitizeTimeString(string $time): \DateTime
    {
        return Carbon::createFromTimeString(substr($time, 0, -1), new \DateTimeZone('Europe/Berlin'));
    }

    /**
     * Formatiert den Titel einer Veranstaltung
     * @param string $title
     * @param array $event
     * @return string
     */
    protected function sanitizeTitle(string $title, array &$event): string
    {
        $title = trim($title);
        //$title = strtr($title, $this->config['events']['sanitize']['title']);
        foreach (['P', 'O', 'M', 'L'] as $function) {
            $regex = '/' . $function . ':\s?(\w*)/';
            preg_match($regex, $title, $tmp);
            if (count($tmp)) {
                $event[$function] = $tmp[1];
                $title = str_replace($tmp[0], '', $title);
            }
        }

        while (false !== strpos($title, '  ')) {
            $title = str_replace('  ', ' ', $title);
        }

        return $title;
    }


    /**
     * Formatiert die Ortsangabe einer Veranstaltung
     * @param string $location
     * @return string
     */
    protected function sanitizeLocation(string $location): string
    {
        $location = trim(str_replace('Ort:', '', $location));
        return $location;
    }

}