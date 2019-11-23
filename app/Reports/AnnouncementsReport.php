<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Section;

class AnnouncementsReport extends AbstractWordDocumentReport
{
    protected const BOLD = ['bold' => true];
    protected const UNDERLINE = ['underline' => Font::UNDERLINE_SINGLE];
    protected const BOLD_UNDERLINE = ['bold' => true, 'underline' => Font::UNDERLINE_SINGLE];
    protected const INDENT = 'Bekanntgaben';
    protected const NO_INDENT = 'Bekanntgaben ohne Einrückung';

    public $title = 'Bekanntgaben';
    public $group = 'Veröffentlichungen';
    public $description = 'Bekanntgaben für einen Gottesdienst';

    /** @var \PhpOffice\PhpWord\Element\Section */
    protected $section;

    public function setup()
    {
        $cities = Auth::user()->cities;
        return $this->renderSetupView(compact('cities'));
    }

    public function configure(Request $request) {
        $request->validate(['city' => 'required|int']);
        $city = City::findOrFail($request->get('city'));
        $request->session()->put('city', $city->id);
        return redirect()->route('report.step', ['report' => $this->getKey(), 'step' => 'configure2']);
    }


    public function configure2(Request $request)
    {
        $city = City::findOrFail($request->session()->get('city'));
        $services = Service::with(['day', 'location'])
            ->regularForCity($city)
            ->select('services.*')
            ->join('days', 'services.day_id', 'days.id')
            ->whereHas('day', function ($query) {
                $query->where('date', '>=', Carbon::now());
            })->orderBy('days.date')
            ->orderBy('time')
            ->get();

        return $this->renderView('configure', compact('cities', 'services'));
    }

    public function input(Request $request)
    {
        $request->validate(['service' => 'required|int']);
        $service = Service::findOrFail($request->get('service'));
        $request->session()->put('service', $service->id);
        return redirect()->route('report.step', ['report' => $this->getKey(), 'step' => 'postInput']);
    }


    public function postInput(Request $request)
    {
        $service = Service::findOrFail($request->session()->get('service'));

        $lastDaysWithServices = Day::whereHas('services', function ($query) use ($service) {
            $query->regularForCity($service->city);
        })->where('date', '<', $service->day->date)
            ->orderBy('date', 'DESC')->limit(10)->get();


        // add up all offerings for the day
        $offerings = [];
        foreach ($lastDaysWithServices as $day) {
            $dayServices = Service::where('day_id', $day->id)
                ->where('city_id', $service->city->id)
                ->get();
            $amount = 0;
            foreach ($dayServices as $dayService) {
                //$amount += (float)filter_var(strtr($dayService->offering_amount, [',' => '.', ' ' => '', '€' => '']), FILTER_SANITIZE_NUMBER_FLOAT);
                $amount += (float)strtr($dayService->offering_amount, [' ' => '', '€' => '']);
            }
            $offerings[$day->id] = trim(money_format('%=*^#0.2n', $amount));
        }

        return $this->renderView('input', compact('service', 'lastDaysWithServices', 'offerings'));
    }

    public function render(Request $request)
    {
        $request->validate([
            'offerings' => 'required|string',
            'lastService' => 'required|date|date_format:d.m.Y',
        ]);

        $service = Service::findOrFail($request->session()->pull('service'));
        $city = City::findOrFail($request->session()->pull('city'));

        $lastService = $request->get('lastService');
        $offerings = $request->get('offerings');
        $offeringText = $request->get('offering_text') ?: '';

        $lastWeek = Carbon::createFromTimeString($service->day->date->format('Y-m-d') . ' 0:00:00 last Sunday');
        $nextWeek = Carbon::createFromTimeString($service->day->date->format('Y-m-d') . ' 0:00:00 next Sunday');

        $funerals = Funeral::where('announcement', $service->day->date->format('Y-m-d'))
            ->whereHas('service', function ($query) use ($service) {
                $query->where('city_id', $service->city->id);
            })
            ->get();

        $weddings = Wedding::with('service')
            ->whereHas('service', function ($query) use ($service, $nextWeek) {
                $query->whereHas('day', function ($query2) use ($service, $nextWeek) {
                    $query2->where('date', '>=', $service->day->date);
                    $query2->where('date', '<=', $nextWeek);
                    $query2->where('city_id', $service->city->id);
                });
            })->get();

        $baptisms = Baptism::with('service')
            ->whereHas('service', function ($query) use ($service, $nextWeek) {
                $query->whereHas('day', function ($query2) use ($service, $nextWeek) {
                    $query2->where('date', '>=', $service->day->date);
                    $query2->where('date', '<=', $nextWeek);
                    $query2->where('city_id', $service->city->id);
                });
            })->get();


        $services = Service::with(['day', 'location'])
            ->whereHas('day', function ($query) use ($service, $nextWeek) {
                $query->where('date', '>=', $service->day->date);
                $query->where('date', '<=', $nextWeek);
                $query->where('city_id', $service->city->id);
                $query->where('id', '!=', $service->id);
            })
            ->get();

        $events = [];

        if ($request->get('mix_outlook', false)) {
            $calendar = new EventCalendarImport($city->public_events_calendar_url);
            $events = $calendar->mix($events, $service->day->date, $nextWeek, true);
        }

        $events = Service::mix($events, $services, $service->day->date, $nextWeek);

        if ($request->get('mix_op', false)) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $service->day->date, $nextWeek);
        }

        $this->section = $this->wordDocument->addSection([
            'orientation' => 'portrait',
            'pageSizeH' => Converter::cmToTwip(21),
            'pageSizeW' => Converter::cmToTwip(14.85),
            'marginTop' => Converter::cmToTwip(0.75),
            'marginBottom' => Converter::cmToTwip(0.25),
            'marginLeft' => Converter::cmToTwip(2),
            'marginRight' => Converter::cmToTwip(0.8),
        ]);

        $this->wordDocument->addParagraphStyle(
            self::INDENT,
            array(
                'indentation' => [
                    'left' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
                    'hanging' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
                ],
                'tabs' => [
                    new \PhpOffice\PhpWord\Style\Tab('left', \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3)),
                ],
                'spaceAfter' => 0,
            )
        );

        $this->wordDocument->addParagraphStyle(
            self::NO_INDENT,
            array(
                'tabs' => [
                    new \PhpOffice\PhpWord\Style\Tab('left', \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3)),
                ],
                'spaceAfter' => 0,
            )
        );

        $textRun = $this->section->addTextRun('Bekanntgaben');
        $textRun->addText('Bekanntgaben für ' . $service->day->date->formatLocalized('%A, %d. %B %Y'),
            ['bold' => true]);


        $ctr = 0;
        $offeringsDone = false;
        $lastDay = $nextWeek->format('Ymd');
        foreach ($events as $eventsArray) {
            foreach ($eventsArray as $event) {
                $eventStart = is_array($event) ? $event['start'] : $event->day->date;


                $dateFormat = $ctr ? '%A, %d. %B' : '%A, %d. %B %Y';

                $done = false;

                if (is_array($event)) {
                    if ($service->day->date->format('Ymd') == $eventStart->format('Ymd')) {
                        if ($event['allDay']) {
                            $textRun = $this->section->addTextRun('Bekanntgaben ohne Einrückung');
                            $textRun->addText($event['title']);
                            $done = true;
                        }
                    }
                }

                if (!$offeringsDone) {
                    $this->renderParagraph(self::NO_INDENT, [
                        ['**************************************************************************', []],
                    ]);

                    $this->renderParagraph(self::NO_INDENT, [
                        [
                            'Herzlichen Dank für das Opfer der Gottesdienste vom '
                            . $lastService
                            . ' in Höhe von ' . $offerings . ' Euro.',
                            []
                        ]
                    ]);

                    $textRun = $this->renderParagraph(self::NO_INDENT, [
                        [
                            'Das heutige Opfer ist für folgenden Zweck bestimmt: ' . $service->offering_goal,
                            []
                        ]
                    ], 1);

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
                            $this->renderParagraph(self::NO_INDENT, [
                                [
                                    'Vorschau',
                                    self::BOLD_UNDERLINE,
                                ]
                            ], 1);
                        }

                        $this->renderParagraph(self::NO_INDENT, [
                            [
                                ($service->day->date->format('Ymd') == $eventStart->format('Ymd')) ?
                                    'Heute' : strftime($dateFormat, $eventStart->getTimestamp()),
                                self::BOLD_UNDERLINE,
                            ]
                        ]);
                    }

                    if (is_array($event)) {
                        $textRun = $this->renderParagraph(self::INDENT, [
                            [
                                (isset($event['allDay']) && $event['allDay']) ? '' : strftime('%H.%M Uhr', $eventStart->getTimestamp()) . "\t",
                                []
                            ],
                            [
                                trim($event['title'] . ' (' . $event['place'] . ')'),
                                []
                            ]
                        ]);
                    } else {
                        $description = $event->descriptionText();
                        $description = $description ? ' mit ' . $description : '';
                        // take care of ampersands
                        $description = preg_replace('/&(?![A-Za-z0-9#]{1,7};)/','&amp;',$description);
                        $textRun = $this->renderParagraph(self::INDENT, [
                            [
                                $event->timeText(true, '.') . "\t",
                                []
                            ],
                            [
                                trim('Gottesdienst' . $description . ' (' . $event->locationText() . ')'),
                                []
                            ]
                        ]);
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

            $weddingArray = [];
            foreach ($baptisms as $baptism) {
                $weddingArray[$baptism->service->trueDate()->format('YmdHis')][] = $baptism;
            }
            ksort($weddingArray);

            foreach ($weddingArray as $baptisms) {
                $baptism = array_first($baptisms);
                if ($baptism->service->id != $service->id) {
                    $textRun = $this->renderParagraph();
                    if ($baptism->service->trueDate() == $service->trueDate()) {
                        $this->renderParagraph(self::NO_INDENT, [
                            [
                                'Im Gottesdienst heute ' . $baptism->service->atText() . ' ' . (count($baptisms) > 1 ? 'werden' : 'wird') . ' getauft:',
                                []
                            ]
                        ]);
                    } else {
                        $this->renderParagraph(self::NO_INDENT, [
                            [
                                'Im Gottesdienst am ' . $baptism->service->day->date->format('d.m.Y') . ' ' . $baptism->service->atText() . ' ' . (count($baptisms) > 1 ? 'werden' : 'wird') . ' getauft:',
                                []
                            ]
                        ]);
                    }
                    foreach ($baptisms as $baptism) {
                        $this->renderParagraph(self::NO_INDENT, [
                            [$this->renderName($baptism->candidate_name) . ', ' . $baptism->candidate_address, []]
                        ]);
                    }
                }
            }
            $this->renderParagraph();
            $this->renderLiteral('*Christus hat der Kirche den Auftrag gegeben:
Gehet hin und machet zu Jüngern alle Völker
und taufet sie auf den Namen des Vaters und
des Sohnes und des Heiligen Geistes.');

        }


        if (count($weddings)) {
            $this->renderParagraph(self::NO_INDENT, [['Trauungen', self::BOLD_UNDERLINE]]);

            $weddingArray = [];
            foreach ($weddings as $wedding) {
                $weddingArray[$wedding->service->trueDate()->format('YmdHis')][] = $wedding;
            }
            ksort($weddingArray);

            foreach ($weddingArray as $weddings) {
                $wedding = array_first($weddings);
                if ($wedding->service->id != $service->id) {
                    $textRun = $this->renderParagraph();
                    if ($wedding->service->trueDate() == $service->trueDate()) {
                        $this->renderParagraph(self::NO_INDENT, [
                            ['Im Gottesdienst heute ' . $wedding->service->atText() . ' werden kirchlich getraut:', []]
                        ]);
                    } else {
                        $this->renderParagraph(self::NO_INDENT, [
                            [
                                'Im Gottesdienst am ' . $wedding->service->day->date->format('d.m.Y') . ' ' . $wedding->service->atText() . ' werden kirchlich getraut:',
                                []
                            ]
                        ]);
                    }
                    foreach ($weddings as $wedding) {
                        $this->renderParagraph(self::NO_INDENT, [
                            [
                                $this->renderName($wedding->spouse1_name) . ' &amp; ' . $this->renderName($wedding->spouse2_name),
                                []
                            ]
                        ]);
                    }
                }
            }
            $this->renderParagraph();
            $textRun = $this->renderLiteral('*Vater im Himmel,
wir bitten für dieses Hochzeitspaar.
Begleite sie auf ihrem gemeinsamen Weg.
Lass sie deine Liebe erfahren
und stärke ihre Liebe zueinander
in guten und in schweren Tagen.');
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
                $this->renderParagraph(self::NO_INDENT, [
                    ['Aus unserer Gemeinde '.StringTool::pluralString(count($funeralArray['past']), 'ist', 'sind').' verstorben und '
                        .StringTool::pluralString(count($funeralArray['past']), 'wurde', 'wurden').' kirchlich bestattet:', []]
                ]);
                foreach ($funeralArray['past'] as $funeral) {
                    $this->renderParagraph(self::NO_INDENT, [
                        [
                            $this->renderName($funeral->buried_name) . ', ' . $funeral->buried_address
                            .($funeral->age() ? ', '.$funeral->age().' Jahre' : '').'.', []
                        ]
                    ]);
                }
                if (count($funeralArray['future'])) $this->renderParagraph();
            }

            if (count($funeralArray['future'])) {
                ksort($funeralArray['future']);
                $this->renderParagraph(self::NO_INDENT, [
                    ['Aus unserer Gemeinde '.StringTool::pluralString(count($funeralArray['future']), 'ist', 'sind').' verstorben:', []]
                ]);
                foreach ($funeralArray['future'] as $funeral) {
                    $mode = $funeral->type;
                    if ($mode == 'Erdbestattung') $mode = 'Bestattung';
                    $this->renderParagraph(self::NO_INDENT, [
                        [
                            $this->renderName($funeral->buried_name) . ', '
                            . $funeral->buried_address
                            .($funeral->age() ? ', '.$funeral->age().' Jahre' : '')
                            .'. Die '.$mode.' findet am '.$funeral->service->day->date->formatLocalized('%A, %d. %B')
                            .' um '.$funeral->service->timeText(true, '.')
                            .' '.$funeral->service->atText().' statt.',
                            []
                        ]
                    ]);
                }

            }


            $this->renderParagraph();
            $textRun = $this->renderLiteral('Wir nehmen teil an der Trauer der Angehörigen und befehlen die Toten, die Trauernden und uns der Güte Gottes an.');
            $textRun = $this->renderLiteral('_Wir bekennen gemeinsam:');
            $textRun = $this->renderLiteral('Unser keiner lebt sich selber, und keiner stirbt sich selber.');
            $textRun = $this->renderLiteral('Leben wir, so leben wir dem Herrn;
sterben wir, so sterben wir dem Herrn.
Darum: Wir leben oder sterben, so sind wir des Herrn.');
            $textRun = $this->renderLiteral('*Denn dazu ist Christus gestorben und wieder lebendig geworden, dass er über Tote und Lebende Herr sei.
Amen.');
        }


        $filename = $service->day->date->format('Y_m_d') . ' Bekanntgaben';
        $this->sendToBrowser($filename);
    }


    protected function renderName($s)
    {
        if (false !== strpos($s, ',')) {
            $t = explode(',', $s);
            $s = trim($t[1]) . ' ' . trim($t[0]);
        }
        return $s;
    }

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
            $paragraph = trim(strtr($paragraph, [
                "\r" => '',
                "\n" => '<w:br />'
            ]));
            $this->renderParagraph(self::NO_INDENT, [[$paragraph, $format]], 1);
        }


    }
}