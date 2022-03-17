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

use App\Baptism;
use App\City;
use App\Day;
use App\Funeral;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Service;
use App\Tools\StringTool;
use App\Wedding;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Inertia\Inertia;
use NumberFormatter;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Tab;

/**
 * Class AnnouncementsReport
 * @package App\Reports
 */
class AnnouncementsReport extends AbstractWordDocumentReport
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
    /**
     *
     */
    protected const INDENT = 'Bekanntgaben';
    /**
     *
     */
    protected const NO_INDENT = 'Bekanntgaben ohne Einrückung';

    /**
     * @var string
     */
    public $title = 'Bekanntgaben';
    /**
     * @var string
     */
    public $group = 'Veröffentlichungen';
    /**
     * @var string
     */
    public $description = 'Bekanntgaben für einen Gottesdienst';

    /** @var Section */
    protected $section;

    protected $inertia = true;

    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return Inertia::render('Report/Announcements/Setup', compact('cities'));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function services(Request $request)
    {
        $data = $request->validate(['city' => 'required|int']);
        $city = City::findOrFail($data['city']);
        $serviceList = Service::with(['location'])
            ->regularForCity($city)
            ->notHidden()
            ->startingFrom(Carbon::now()->subHours(8))
            ->ordered()
            ->get();

        $services = [];
        foreach ($serviceList as $service) {
            $services[] = [
                'id' => $service->id,
                'name' => $service->date->format('d.m.Y') . ' ' . $service->timeText() . ', ' . $service->locationText
            ];
        }

        return response()->json([
                                    'services' => $services,
                                    'mixOutlook' => (bool)$city->public_events_calendar_url,
                                    'mixOP' => (bool)$city->op_customer_token,
                                ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lastServiceDays(Request $request)
    {
        $data = $request->validate([
            'city' => 'required|int|exists:cities,id',
            'service' => 'required|int|exists:services,id'
                                   ]);

        $city = City::findOrFail($data['city']);
        $service = Service::findOrFail($data['service']);

        $days = Service::select(DB::raw('DISTINCT DATE(date) AS day'))
            ->endingAt($service->date)
            ->regularForCity($city)
            ->orderBy('day', 'DESC')
            ->limit(10)
            ->get()
            ->pluck('day');

        $lastServiceDays = [];
        foreach ($days as $day) {
            $lastServiceDays[] = ['id' => $day, 'name' => Carbon::parse($day)->formatLocalized('%A, %d. %B %Y')];
        }

        return response()->json($lastServiceDays);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function offerings(Request $request) {
        $data = $request->validate([
            'city' => 'required|int|exists:cities,id',
            'day' => 'required|date'
                                   ]);
        $services = Service::whereDate('date', Carbon::parse($data['day']))
            ->where('city_id', $data['city'])
            ->get();

        $amount = 0;
        foreach ($services as $service) {
            $amount += (float)strtr($service->offering_amount, [' ' => '', '€' => '']);
        }

        return response()->json($amount);
    }


    public function auto(Request $request)
    {
        if (!$request->has('service')) {
            abort(404);
        }
        $service = Service::findOrFail($request->get('service'));
        $lastService = Service::where('offering_amount', '!=', '')
            ->endingAt($service->dateTime)
            ->orderedDesc()
            ->first();
        $this->renderReport([
                                'lastService' => $lastService->date->format('d.m.Y'),
                                'offerings' => $lastService->offering_amount,
                                'offering_text' => $service->offering_text,
                                'service' => $service,
                                'mix_outlook' => $service->city->public_events_calendar_url ? true : false,
                                'mix_op' => $service->city->op_customer_token ? true : false,
                            ]);
    }

    public function renderReport($data)
    {
        $service = Service::findOrFail($data['service']);
        $city = City::findOrFail($data['city']);

        $lastService = $data['lastService'];
        $offerings = $data['offerings'];
        $offeringText = $data['offering_text'] ?? '';

        $lastWeek = Carbon::createFromTimeString($service->date->format('Y-m-d') . ' 0:00:00 last Sunday');
        $nextWeek = Carbon::createFromTimeString($service->date->format('Y-m-d') . ' 0:00:00 next Sunday')->setTime(
            23,
            59,
            59
        );

        $funerals = Funeral::where('announcement', $service->date->format('Y-m-d'))
            ->whereHas(
                'service',
                function ($query) use ($service) {
                    $query->inCity($service->city);
                }
            )
            ->get();

        $weddings = Wedding::with('service')
            ->whereHas(
                'service',
                function ($query) use ($service, $nextWeek) {
                    $query->notHidden()->whereHas(
                        'day',
                        function ($query2) use ($service, $nextWeek) {
                            $query2->where('date', '>=', $service->date);
                            $query2->where('date', '<=', $nextWeek);
                            $query2->where('city_id', $service->city->id);
                        }
                    );
                }
            )->get();

        $baptisms = Baptism::with('service')
            ->whereHas(
                'service',
                function ($query) use ($service, $nextWeek) {
                    $query->notHidden()
                        ->whereHas(
                            'day',
                            function ($query2) use ($service, $nextWeek) {
                                $query2->where('date', '>=', $service->date);
                                $query2->where('date', '<=', $nextWeek);
                                $query2->where('city_id', $service->city->id);
                            }
                        );
                }
            )->get();


        $services = Service::with(['location'])
            ->notHidden()
            ->between($service->date, $nextWeek)
            ->inCity($service->city)
            ->where('id', '!=', $service->id)
            ->ordered()
            ->get();

        $events = [];

        if ($data['mix_outlook'] ?? false) {
            $calendar = new EventCalendarImport($city->public_events_calendar_url);
            $events = $calendar->mix($events, $service->date, $nextWeek, true);
        }

        $events = Service::mix($events, $services, $service->date, $nextWeek);

        if ($data['mix_op'] ?? false) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $service->date, $nextWeek);
        }

        $this->section = $this->wordDocument->addSection(
            [
                'orientation' => 'portrait',
                'pageSizeH' => Converter::cmToTwip(21),
                'pageSizeW' => Converter::cmToTwip(14.85),
                'marginTop' => Converter::cmToTwip(0.75),
                'marginBottom' => Converter::cmToTwip(0.25),
                'marginLeft' => Converter::cmToTwip(2),
                'marginRight' => Converter::cmToTwip(0.8),
            ]
        );

        $this->wordDocument->addParagraphStyle(
            self::INDENT,
            array(
                'indentation' => [
                    'left' => Converter::cmToTwip(3),
                    'hanging' => Converter::cmToTwip(3),
                ],
                'tabs' => [
                    new Tab('left', Converter::cmToTwip(3)),
                ],
                'spaceAfter' => 0,
            )
        );

        $this->wordDocument->addParagraphStyle(
            self::NO_INDENT,
            array(
                'tabs' => [
                    new Tab('left', Converter::cmToTwip(3)),
                ],
                'spaceAfter' => 0,
            )
        );

        $textRun = $this->section->addTextRun('Bekanntgaben');
        $textRun->addText(
            'Bekanntgaben für ' . $service->date->formatLocalized('%A, %d. %B %Y'),
            ['bold' => true]
        );


        $ctr = 0;
        $offeringsDone = false;
        $lastDay = $nextWeek->format('Ymd');
        foreach ($events as $eventsArray) {
            foreach ($eventsArray as $event) {
                $eventStart = is_array($event) ? $event['start'] : $event->date;


                $dateFormat = $ctr ? '%A, %d. %B' : '%A, %d. %B %Y';

                $done = false;

                if (is_array($event)) {
                    if ($service->date->format('Ymd') == $eventStart->format('Ymd')) {
                        if (isset($event['allDay']) && ($event['allDay'])) {
                            $textRun = $this->section->addTextRun('Bekanntgaben ohne Einrückung');
                            $textRun->addText($event['title']);
                            $done = true;
                        }
                    }
                }

                if (!$offeringsDone) {
                    $this->renderParagraph(
                        self::NO_INDENT,
                        [
                            ['**************************************************************************', []],
                        ]
                    );

                    $this->renderParagraph(
                        self::NO_INDENT,
                        [
                            [
                                'Herzlichen Dank für das Opfer der Gottesdienste vom '
                                . $lastService
                                . ' in Höhe von ' . $offerings . ' Euro.',
                                []
                            ]
                        ]
                    );

                    $textRun = $this->renderParagraph(
                        self::NO_INDENT,
                        [
                            [
                                'Das heutige Opfer ist für folgenden Zweck bestimmt: ' . $service->offering_goal,
                                []
                            ]
                        ],
                        1
                    );

                    if ($offeringText) {
                        $this->renderLiteral($offeringText);
                        $this->renderParagraph();
                    }

                    $offeringsDone = true;
                }

                if (!$done) {
                    if ($lastDay != $eventStart->format('Ymd')) {
                        $this->renderParagraph();
                        if ($nextWeek->format('Ymd') == $eventStart->format('Ymd')) {
                            $this->renderParagraph(
                                self::NO_INDENT,
                                [
                                    [
                                        'Vorschau',
                                        self::BOLD_UNDERLINE,
                                    ]
                                ],
                                1
                            );
                        }

                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [
                                    ($service->date->format('Ymd') == $eventStart->format('Ymd')) ?
                                        'Heute' : strftime($dateFormat, $eventStart->getTimestamp()),
                                    self::BOLD_UNDERLINE,
                                ]
                            ]
                        );
                    }

                    if (is_array($event)) {
                        $textRun = $this->renderParagraph(
                            self::INDENT,
                            [
                                [
                                    (isset($event['allDay']) && $event['allDay']) ? '' : strftime(
                                            '%H.%M Uhr',
                                            $eventStart->getTimestamp()
                                        ) . "\t",
                                    []
                                ],
                                [
                                    trim(
                                        StringTool::sanitizeXMLString(
                                            $event['title']
                                        ) . ' (' . StringTool::sanitizeXMLString($event['place']) . ')'
                                    ),
                                    []
                                ]
                            ]
                        );
                    } else {
                        $description = $event->descriptionText();
                        $description = $description ? ' mit ' . $description : '';
                        // take care of ampersands
                        $description = preg_replace('/&(?![A-Za-z0-9#]{1,7};)/', '&amp;', $description);
                        $textRun = $this->renderParagraph(
                            self::INDENT,
                            [
                                [
                                    $event->timeText(true, '.') . "\t",
                                    []
                                ],
                                [
                                    trim(
                                        ($event->title ?: 'Gottesdienst') . $description . ' (' . $event->locationText(
                                        ) . ')'
                                    ),
                                    []
                                ]
                            ]
                        );

                        // add children's church
                        if ($event->cc) {
                            $this->renderParagraph(
                                self::INDENT,
                                [
                                    [
                                        Carbon::createFromFormat(
                                            'Y-m-d H:i',
                                            $event->date->format('Y-m-d') . ' ' . ($event->cc_alt_time ?? $event->time)
                                        )->formatLocalized('%H.%M Uhr') . "\t",
                                        []
                                    ],
                                    ['Kinderkirche', []],
                                    [' (' . ($event->cc_location ?? $event->locationText()) . ')', []],
                                ]
                            );
                        }
                    }


                    if ((isset($event['allDay']) && $event['allDay'])) {
                        $textRun = $this->renderParagraph();
                    }

                    $lastDay = $eventStart->format('Ymd');
                }

                $ctr++;
            }
        }

        $textRun = $this->renderParagraph();

        // Baptisms
        if (count($baptisms)) {
            $this->renderParagraph(self::NO_INDENT, [['Taufen', self::BOLD_UNDERLINE]]);

            $baptismArray = [];
            foreach ($baptisms as $baptism) {
                $baptismArray[$baptism->service->trueDate()->format('YmdHis')][] = $baptism;
            }
            ksort($baptismArray);

            foreach ($baptismArray as $baptisms) {
                $baptism = $baptisms[array_key_first($baptisms)];
                if ($baptism->service->id != $service->id) {
                    $textRun = $this->renderParagraph();
                    if ($baptism->service->trueDate() == $service->trueDate()) {
                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [
                                    'Im Gottesdienst heute ' . $baptism->service->atText() . ' ' . (count(
                                        $baptisms
                                    ) > 1 ? 'werden' : 'wird') . ' getauft:',
                                    []
                                ]
                            ]
                        );
                    } else {
                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [
                                    'Im Gottesdienst am ' . $baptism->service->date->format(
                                        'd.m.Y'
                                    ) . ' ' . $baptism->service->atText() . ' ' . (count(
                                        $baptisms
                                    ) > 1 ? 'werden' : 'wird') . ' getauft:',
                                    []
                                ]
                            ]
                        );
                    }
                    foreach ($baptisms as $baptism) {
                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [$this->renderName($baptism->candidate_name) . ', ' . $baptism->candidate_address, []]
                            ]
                        );
                    }
                }
            }
            $this->renderParagraph();
            $this->renderLiteral(
                '*Christus hat der Kirche den Auftrag gegeben:
Gehet hin und machet zu Jüngern alle Völker
und taufet sie auf den Namen des Vaters und
des Sohnes und des Heiligen Geistes.'
            );
        }


        if (count($weddings)) {
            $this->renderParagraph(self::NO_INDENT, [['Trauungen', self::BOLD_UNDERLINE]]);

            $weddingArray = [];
            foreach ($weddings as $wedding) {
                $weddingArray[$wedding->service->trueDate()->format('YmdHis')][] = $wedding;
            }
            ksort($weddingArray);

            foreach ($weddingArray as $weddings) {
                $wedding = $weddings[array_key_first($weddings)];
                if ($wedding->service->id != $service->id) {
                    $textRun = $this->renderParagraph();
                    if ($wedding->service->trueDate() == $service->trueDate()) {
                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [
                                    'Im Gottesdienst heute ' . $wedding->service->atText(
                                    ) . ' werden kirchlich getraut:',
                                    []
                                ]
                            ]
                        );
                    } else {
                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [
                                    'Im Gottesdienst am ' . $wedding->service->date->format(
                                        'd.m.Y'
                                    ) . ' ' . $wedding->service->atText() . ' werden kirchlich getraut:',
                                    []
                                ]
                            ]
                        );
                    }
                    foreach ($weddings as $wedding) {
                        $this->renderParagraph(
                            self::NO_INDENT,
                            [
                                [
                                    $this->renderName($wedding->spouse1_name) . ' &amp; ' . $this->renderName(
                                        $wedding->spouse2_name
                                    ),
                                    []
                                ]
                            ]
                        );
                    }
                }
            }
            $this->renderParagraph();
            $textRun = $this->renderLiteral(
                '*Vater im Himmel,
wir bitten für dieses Hochzeitspaar.
Begleite sie auf ihrem gemeinsamen Weg.
Lass sie deine Liebe erfahren
und stärke ihre Liebe zueinander
in guten und in schweren Tagen.'
            );
        }

        if (count($funerals)) {
            $this->renderParagraph(self::NO_INDENT, [['Bestattungen', self::BOLD_UNDERLINE]]);

            $funeralArray = ['past' => [], 'future' => []];
            foreach ($funerals as $funeral) {
                $key = ($funeral->service->trueDate() < $service->trueDate()) ? 'past' : 'future';
                $funeralArray[$key][] = $funeral;
            }

            if (count($funeralArray['past'])) {
                ksort($funeralArray['past']);
                $this->renderParagraph(
                    self::NO_INDENT,
                    [
                        [
                            'Aus unserer Gemeinde ' . StringTool::pluralString(
                                count($funeralArray['past']),
                                'ist',
                                'sind'
                            ) . ' verstorben und '
                            . StringTool::pluralString(
                                count($funeralArray['past']),
                                'wurde',
                                'wurden'
                            ) . ' kirchlich bestattet:',
                            []
                        ]
                    ]
                );
                foreach ($funeralArray['past'] as $funeral) {
                    $this->renderParagraph(
                        self::NO_INDENT,
                        [
                            [
                                $this->renderName($funeral->buried_name) . ', ' . $funeral->buried_address
                                . ($funeral->age() ? ', ' . $funeral->age() . ' Jahre' : '') . '.',
                                []
                            ]
                        ]
                    );
                }
                if (count($funeralArray['future'])) {
                    $this->renderParagraph();
                }
            }

            if (count($funeralArray['future'])) {
                ksort($funeralArray['future']);
                $this->renderParagraph(
                    self::NO_INDENT,
                    [
                        [
                            'Aus unserer Gemeinde ' . StringTool::pluralString(
                                count($funeralArray['future']),
                                'ist',
                                'sind'
                            ) . ' verstorben:',
                            []
                        ]
                    ]
                );
                foreach ($funeralArray['future'] as $funeral) {
                    $mode = $funeral->type;
                    if ($mode == 'Erdbestattung') {
                        $mode = 'Bestattung';
                    }
                    $this->renderParagraph(
                        self::NO_INDENT,
                        [
                            [
                                $this->renderName($funeral->buried_name) . ', '
                                . $funeral->buried_address
                                . ($funeral->age() ? ', ' . $funeral->age() . ' Jahre' : '')
                                . '. Die ' . $mode . ' findet am ' . $funeral->service->date->formatLocalized(
                                    '%A, %d. %B'
                                )
                                . ' um ' . $funeral->service->timeText(true, '.')
                                . ' ' . $funeral->service->atText() . ' statt.',
                                []
                            ]
                        ]
                    );
                }
            }


            $this->renderParagraph();
            $textRun = $this->renderLiteral(
                'Wir nehmen teil an der Trauer der Angehörigen und befehlen die Toten, die Trauernden und uns der Güte Gottes an.'
            );
            $textRun = $this->renderLiteral('_Wir bekennen gemeinsam:');
            $textRun = $this->renderLiteral('Unser keiner lebt sich selber, und keiner stirbt sich selber.');
            $textRun = $this->renderLiteral(
                'Leben wir, so leben wir dem Herrn;
sterben wir, so sterben wir dem Herrn.
Darum: Wir leben oder sterben, so sind wir des Herrn.'
            );
            $textRun = $this->renderLiteral(
                '*Denn dazu ist Christus gestorben und wieder lebendig geworden, dass er über Tote und Lebende Herr sei.
Amen.'
            );
        }

        if ($service->announcements) {
            $this->renderParagraph();
            $textRun = $this->renderLiteral($service->announcements);
        }


        $filename = $service->date->format('Y_m_d') . ' Bekanntgaben';
        $this->sendToBrowser($filename);
    }

    /**
     * @param Request $request
     * @return string|void
     * @throws Exception
     */
    public function render(Request $request)
    {
        return $this->renderReport(
            $request->validate(
                [
                    'city' => 'required|int|exists:cities,id',
                    'service' => 'required|int|exists:services,id',
                    'offerings' => 'required|string',
                    'lastService' => 'required|date',
                    'mix_op' => 'nullable|checkbox',
                    'mix_outlook' => 'nullable|checkbox',
                    'offering_text' => 'nullable|string',
                ]
            )
        );
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
}
