<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Reports;

use App\City;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Liturgy;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Tab;

/**
 * Class EventListReport
 * @package App\Reports
 */
class EventListReport extends AbstractWordDocumentReport
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
    protected const DEFAULT = 'Gottesdienste';
    /**
     *
     */
    protected const LIST2 = 'Termine';

    /**
     * @var string
     */
    public $title = 'Terminliste';
    /**
     * @var string
     */
    public $group = 'Veröffentlichungen';
    /**
     * @var string
     */
    public $description = 'Terminliste als Worddatei';

    /** @var Section */
    protected $section;

    protected $inertia = true;

    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return Inertia::render('Report/EventList/Setup', compact('cities'));
    }

    /**
     * @param Request $request
     * @return string|void
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required|int',
                'start' => 'required|date',
                'end' => 'required|date',
                'mixOutlook' => 'nullable|bool',
                'mixOP' => 'nullable|bool',
            ]
        );

        $start = Carbon::parse( $data['start']);
        $end = Carbon::parse( $data['end']);
        $city = City::findOrFail($request->get('city'));


        $services = Service::with(['location'])
            ->regularForCity($city)
            ->notHidden()
            ->between($start, $end)
            ->ordered()
            ->get();

        $events = [];

        if ($data['mixOutlook'] ?? false) {
            $calendar = new EventCalendarImport($city->public_events_calendar_url);
            $events = $calendar->mix($events, $start, $end->copy()->subDay(1), true);
        }

        $events = Service::mix($events, $services, $start, $end);

        if ($data['mixOP'] ?? false) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $start, $end);
        }

        $this->wordDocument->setDefaultFontName('Arial Narrow');
        $this->wordDocument->setDefaultFontSize(11);

        $this->section = $this->wordDocument->addSection(
            [
                'orientation' => 'portrait',
                'marginTop' => Converter::cmToTwip(0.63),
                'marginBottom' => Converter::cmToTwip(0.5),
                'marginLeft' => Converter::cmToTwip(0.59),
                'marginRight' => Converter::cmToTwip(0.61),
            ]
        );

        $this->wordDocument->addParagraphStyle(
            self::DEFAULT,
            array(
                'indentation' => [
                    'left' => Converter::cmToTwip(1.3),
                    'hanging' => Converter::cmToTwip(0),
                ],
                'tabs' => [
                    new Tab('left', Converter::cmToTwip(1.3)),
                    new Tab('right', Converter::cmToTwip(4.76)),
                    new Tab('left', Converter::cmToTwip(5.5)),
                    new Tab('left', Converter::cmToTwip(15.0)),
                ],
                'spaceAfter' => 0,
            )
        );

        $this->wordDocument->addParagraphStyle(
            self::LIST2,
            array(
                'indentation' => [
                    'left' => Converter::cmToTwip(1.3),
                    'hanging' => Converter::cmToTwip(0),
                ],
                'tabs' => [
                    new Tab('left', Converter::cmToTwip(1.3)),
                    new Tab('left', Converter::cmToTwip(2)),
                    new Tab('right', Converter::cmToTwip(4.44)),
                    new Tab('left', Converter::cmToTwip(5.4)),
                ],
                'spaceAfter' => 0,
            )
        );

        $this->renderParagraph(
            self::DEFAULT,
            [
                ['Gottesdienste', self::BOLD],
            ],
            1
        );


        // render services
        foreach ($events as $theseEvents) {
            foreach ($theseEvents as $event) {
                if (is_object(($event))) {
                    /* @var Service $event */
                    // only services this time

                    $liturgy = Liturgy::getDayInfo($event->day);
                    $pericope = (isset($liturgy['perikope']) ? $liturgy['litTextsPerikope' . $liturgy['perikope']] : '');

                    // remove optional verses
                    if ($pericope) {
                        $pericope = preg_replace('/\([\d–]*\)/', '+', $pericope);
                        if (substr($pericope, -1) == '+') {
                            $pericope = substr($pericope, 0, -1);
                        }
                        $pericope = str_replace(',+', ',', $pericope);
                    }

                    $run = $this->renderParagraph(
                        self::DEFAULT,
                        [
                            [$event->date->format('d.m.Y') . "\t", []],
                            [$event->timeText(true) . "\t", []],
                        ],
                        0
                    );

                    if ($event->day->description) {
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                [$event->day->description . ' – ', []],
                            ],
                            0,
                            $run
                        );
                    } elseif (isset($liturgy['title'])) {
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                [$liturgy['title'] . ' – ', []],
                            ],
                            0,
                            $run
                        );
                    }

                    $title = $event->title ?: 'Gottesdienst';
                    if ($event->eucharist) {
                        $title = 'Abendmahlsgottesdienst';
                    }
                    if ($event->baptism) {
                        $title = 'Taufgottesdienst';
                    }

                    if ($p = $event->participantsText('P', true)) {
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                [$title . ' mit ' . $p, []],
                            ],
                            0,
                            $run
                        );
                    }

                    $this->renderParagraph(
                        self::DEFAULT,
                        [
                            ["\t" . $pericope, []],
                        ],
                        0,
                        $run
                    );


                    if ($description = $event->description) {
                        if (strlen($description) > 25) {
                            $description = str_replace('; ', ';<w:br />', $description);
                        }
                        // take care of ampersands
                        $description = trim(preg_replace('/&(?![A-Za-z0-9#]{1,7};)/', '&amp;', $description));
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                ["<w:br />\t\t" . $description, []],
                            ],
                            0,
                            $run
                        );
                    } elseif (isset($liturgy['title'])) {
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                ["<w:br />\t\t" . $liturgy['title'], []],
                            ],
                            0,
                            $run
                        );
                    }

                    if ($event->offering_goal) {
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                ["<w:br />\t\tOpfer: " . $event->offering_goal, []],
                            ],
                            0,
                            $run
                        );
                    }

                    if ($event->cc) {
                        $this->renderParagraph(
                            self::DEFAULT,
                            [
                                [
                                    "<w:br />\t\tKinderkirche: " . $event->ccTimeText(
                                        false
                                    ) . ', ' . $event->ccLocationText(),
                                    []
                                ],
                            ],
                            0,
                            $run
                        );
                    }

                    $this->renderParagraph(
                        self::DEFAULT,
                        [
                        ],
                        1,
                        $run
                    );
                }
            }
        }

        $this->renderParagraph();
        $this->renderParagraph();
        $this->renderParagraph();
        $this->renderParagraph(
            self::DEFAULT,
            [
                ['Termine', self::BOLD],
            ],
            1
        );


        // render other events
        foreach ($events as $theseEvents) {
            foreach ($theseEvents as $event) {
                if (!is_object(($event))) {
                    $run = $this->renderParagraph(
                        self::LIST2,
                        [
                            [$event['start']->formatLocalized('%a.'), []],
                            ["\t" . $event['start']->format('d.m.'), []],
                            ["\t" . $event['start']->format('H:i') . ' Uhr', []],
                            ["\t" . $event['title'], []],
                        ]
                    );
                }
            }
        }


        $filename = $start->format('Y_m_d') . ' Terminliste';
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
