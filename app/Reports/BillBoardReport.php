<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Reports;

use App\City;
use App\Day;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Tab;

/**
 * Class BillBoardReport
 * @package App\Reports
 */
class BillBoardReport extends AbstractWordDocumentReport
{
    /**
     *
     */
    protected const BOLD = ['bold' => true];
    /**
     *
     */
    protected const UNDERLINE = ['underline' => Font::UNDERLINE_SINGLE];
    /**
     *
     */
    protected const BOLD_UNDERLINE = ['bold' => true, 'underline' => Font::UNDERLINE_SINGLE];
    protected const DEFAULT = 'Kirchzettel';
    /**
     *
     */
    protected const HEADING1 = 'Kirchzettel Überschrift 1';
    /**
     *
     */
    protected const HEADING2 = 'Kirchzettel Überschrift 2';

    /**
     * @var string
     */
    public $title = 'Kirchzettel';
    /**
     * @var string
     */
    public $group = 'Veröffentlichungen';
    /**
     * @var string
     */
    public $description = 'Kirchzettel zum Aushang im Schaukasten';

    /** @var Section */
    protected $section;

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return $this->renderSetupView(compact('cities'));
    }

    /**
     * @param Request $request
     * @return string|void
     * @throws Exception
     */
    public function render(Request $request)
    {
        $request->validate(
            [
                'city' => 'required|int',
                'start' => 'required|date|date_format:d.m.Y',
            ]
        );

        $start = Carbon::createFromFormat('d.m.Y H:i:s', $request->get('start') . ' 0:00:00');
        $city = City::findOrFail($request->get('city'));

        $end = $start->copy()->addDays(8)->subSecond(1);

        $services = Service::with(['day', 'location'])
            ->regularForCity($city)
            ->dateRange($start, $end)
            ->get();

        $events = [];

        if ($request->get('mix_outlook', false)) {
            $calendar = new EventCalendarImport($city->public_events_calendar_url);
            $events = $calendar->mix($events, $start, $end->copy()->subDay(1), true);
        }

        $events = Service::mix($events, $services, $start, $end);

        if ($request->get('mix_op', false)) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $start, $end);
        }

        $this->section = $this->wordDocument->addSection(
            [
                'orientation' => 'portrait',
                'marginTop' => Converter::cmToTwip(0.75),
                'marginBottom' => Converter::cmToTwip(0.25),
                'marginLeft' => Converter::cmToTwip(1.59),
                'marginRight' => Converter::cmToTwip(0.25),
            ]
        );

        $this->wordDocument->addParagraphStyle(
            self::DEFAULT,
            array(
                'indentation' => [
                    'left' => Converter::cmToTwip(5.5),
                    'hanging' => Converter::cmToTwip(5.5),
                ],
                'tabs' => [
                    new Tab('left', Converter::cmToTwip(5.5)),
                    new Tab('right', Converter::cmToTwip(18)),
                ],
                'spaceAfter' => 0,
            )
        );

        $this->wordDocument->addParagraphStyle(
            self::HEADING1,
            array(
                'indentation' => [
                    'left' => Converter::cmToTwip(5.5),
                    'firstLine' => Converter::cmToTwip(1.25),
                ],
                'spaceAfter' => 0,
            )
        );

        $this->wordDocument->addParagraphStyle(
            self::HEADING2,
            array(
                'indentation' => [
                    'left' => Converter::cmToTwip(7.5),
                ],
                'spaceAfter' => 0,
            )
        );

        // headings
        $this->renderParagraph(self::HEADING1, [['Evang. Kirchengemeinde', ['size' => 27]]]);
        $this->renderParagraph(self::HEADING2, [["   \t   " . $city->name, ['size' => 27]]]);
        $this->renderParagraph();


        $lastDay = '';
        $ctr = 0;
        foreach ($events as $theseEvents) {
            foreach ($theseEvents as $event) {
                $dateFormat = $ctr ? '%A, %d. %B' : '%A, %d. %B %Y';

                /** @var Carbon $eventStart */
                $eventStart = is_array($event) ? $event['start'] : $event->day->date;
                $done = false;

                // header for the day
                if ($lastDay != $eventStart->format('Ymd')) {
                    $this->renderParagraph();

                    if ($end->format('Ymd') == $eventStart->format('Ymd')) {
                        $this->renderParagraph(self::DEFAULT, [['Vorschau', self::BOLD_UNDERLINE]]);
                    }

                    $day = Day::where('date', $eventStart->copy()->setTime(0, 0, 0))->first();
                    if ($day) {
                        $dayTitle = $day->name;
                    } elseif (is_array($event) && (isset($event['allDay']) && $event['allDay'])) {
                        $dayTitle = $event['title'];
                    } else {
                        $dayTitle = '';
                    }

                    $this->renderParagraph(
                        self::DEFAULT,
                        [
                            [$eventStart->formatLocalized($dateFormat), self::BOLD_UNDERLINE],
                            [($dayTitle ? "\t" : ''), []],
                            [($dayTitle ? $dayTitle : ''), self::BOLD_UNDERLINE],
                        ]
                    );

                    $done = (isset($event['allDay']) && $event['allDay']);
                }

                if (!$done) {
                    $this->renderSingleEvent($event);
                }

                $lastDay = $eventStart->format('Ymd');
                $ctr++;
            }
        }


        $filename = $start->format('Y_m_d') . ' Kirchzettel';
        $this->sendToBrowser($filename);
    }

    /**
     * @param string $template
     * @param array $blocks
     * @param int $emptyParagraphsAfter
     * @param null $existingTextRun
     * @return TextRun|null
     */
    protected function renderParagraph(
        $template = '',
        array $blocks = [],
        $emptyParagraphsAfter = 0,
        $existingTextRun = null
    ) {
        $textRun = $existingTextRun ?: $this->section->addTextRun($template);
        foreach ($blocks as $block) {
            $textRun->addText($block[0], $block[1]);
        }
        for ($i = 0; $i < $emptyParagraphsAfter; $i++) {
            $textRun = $this->section->addTextRun($template);
        }
        return $textRun;
    }

    /**
     * @param $event
     */
    protected function renderSingleEvent($event)
    {
        if (is_array($event)) {
            $this->renderParagraph(
                self::DEFAULT,
                [
                    [$event['start']->formatLocalized('%H.%M Uhr') . "\t", []],
                    [$event['title'], self::BOLD],
                    [' (' . $event['place'] . ')', []],
                    [(isset($event['P']) ? "\t" . $event['P'] : ''), self::BOLD]
                ]
            );
        } else {
            $description = $event->descriptionText();
            if ($description) {
                if (substr($description, 0, 4) != 'mit ') {
                    $description = 'mit ' . $description;
                }
                if (strlen($description) > 25) {
                    $description = str_replace('; ', ';<w:br />', $description);
                }
                // take care of ampersands
                $description = preg_replace('/&(?![A-Za-z0-9#]{1,7};)/', '&amp;', $description);
                $description = ' ' . trim($description);
            }

            $this->renderParagraph(
                self::DEFAULT,
                [
                    [
                        Carbon::createFromFormat(
                            'Y-m-d H:i',
                            $event->day->date->format('Y-m-d') . ' ' . $event->time
                        )->formatLocalized('%H.%M Uhr') . "\t",
                        []
                    ],
                    [($event->title ?: 'Gottesdienst') . $description, self::BOLD],
                    [' (' . $event->locationText() . ')', []],
                    ["\t" . $event->participantsText('P'), self::BOLD]
                ]
            );

            if ($event->offering_goal) {
                $this->renderParagraph(
                    self::DEFAULT,
                    [
                        ["\t" . 'Opfer: ' . $event->offering_goal, []]
                    ]
                );
            }
        }
    }

    /**
     * @param $s
     * @return string
     */
    protected function renderName($s)
    {
        if (false !== strpos($s, ',')) {
            $t = explode(',', $s);
            $s = trim($t[1]) . ' ' . trim($t[0]);
        }
        return $s;
    }

    /**
     * @param $text
     */
    protected function renderLiteral($text)
    {
        if (!is_array($text)) {
            $text = [$text];
        }
        foreach ($text as $paragraph) {
            switch (substr($paragraph, 0, 1)) {
                case '*':
                    $format = self::BOLD;
                    $paragraph = substr($paragraph, 1);
                    break;
                case '_':
                    $format = self::UNDERLINE;
                    $paragraph = substr($paragraph, 1);
                    break;
                default:
                    $format = [];
            }
            $paragraph = trim(
                strtr(
                    $paragraph,
                    [
                        "\r" => '',
                        "\n" => '<w:br />'
                    ]
                )
            );
            $this->renderParagraph(self::NO_INDENT, [[$paragraph, $format]], 1);
        }
    }
}
