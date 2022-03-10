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

use App\Day;
use App\FileFormats\IDML;
use App\Liturgy;
use App\Location;
use App\Service;
use App\ServiceGroup;
use App\Tools\StringTool;
use Carbon\Carbon;
use Debugbar;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpWord\Exception\Exception;
use Throwable;


/**
 * Class BulletinBLReport
 * @package App\Reports
 */
class BulletinBLReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $icon = 'fa fa-file';
    /**
     * @var string
     */
    public $title = 'Gemeindebrief (BL)';
    /**
     * @var string
     */
    public $group = 'Listen';
    /**
     * @var string
     */
    public $description = 'Gottesdiensttabelle für den Gemeindebrief';

    /**
     * @var string[]
     */
    public $formats = ['Balingen'];

    /**
     * @var float[]
     */
    protected $cols = [
        [21.6102362202077, -489.32677165354335, 7948.082677164984, -350.9017990755136, 7978.082677164984],
        [203.94291338581854, -522.6036220472524, 7948.082677164984, -384.17842519685036, 7978.082677164984],
        [352.9980314960625, -522.6036220472524, 7948.082677164984, -384.17842519685036, 7978.082677164984],
        [520.3214917499763, -489.32677165354335, 7948.082677164984, -350.9017990755136, 7978.082677164984],
        [697.137161041411, -518.8541317049825, 7948.082677164984, -384.1784251968842, 7978.082677164984],
    ];

    /**
     * @var array
     */
    protected $tagWidth = [
        'abendmahl' => 2.399,
        'fahrdienst' => 4.276,
        'familien-gd' => 6.894,
        'infos-im-gemeindebrief' => 3.5,
        'kiki' => 10.991,
        'kirchenkaffee' => 6,
        'mit-posaunenchor' => 6.8,
        'musik-im-gd' => 3.014,
        'taufe' => 3.315,
    ];

    /**
     * @var float[]
     */
    protected $tagBaseCoords = [
        'abendmahl' => [-488.45233093682400, -596.13678866970800],
        'fahrdienst' => [-308.42721887539200, -45.45599600745520],
        'familien-gd' => [-188.20131811815900, -202.74274028199700],
        'infos-im-gemeindebrief' => [-535.5472370081530, -568.67671715498000],
        'kiki' => [-328.1407371703400, -74.60820972459380],
        'kirchenkaffee' => [-542.5998114214260, 202.65573939264900],
        'mit-posaunenchor' => [-425.5479722136610, -129.02780354377400],
        'musik-im-gd' => [-188.5429086087690, -205.03833543686800],
        'taufe' => [-353.4474062907450, -359.5165395946640],
    ];

    /** @var IDML */
    protected $idml;

    /**
     * Returns true, if active user has write rights for "Balingen" and thus activates the report
     * @return bool
     */
    public function isActive(): bool
    {
        return Auth::user()->writableCities->pluck('name')->contains('Balingen');
    }


    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderView('setup', ['maxDate' => $maxDate, 'cities' => $cities, 'formats' => $this->formats]);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function configure(Request $request)
    {
        $request->validate(
            [
                'includeCities' => 'required',
                'start' => 'required|date|date_format:d.m.Y',
                'end' => 'required|date|date_format:d.m.Y',
            ]
        );

        $includeCities = $request->get('includeCities');
        $start = $request->get('start');
        $end = $request->get('end');

        $locations = Location::with('alternateLocation')->whereIn('city_id', $includeCities)->get();
        $presets = explode(',', Auth::user()->getSetting('report.bulletinbl.locationpresets', '-1,-1,-1,-1'));


        return $this->renderView('configure', compact('start', 'end', 'includeCities', 'locations', 'presets'));
    }


    public function empty(Request $request)
    {
        $locationIds = $request->get('locations');
        Auth::user()->setSetting('report.bulletinbl.locationpresets', join(',', $locationIds));
        $locations = Location::whereIn('id', $locationIds)->get();


        $start = $request->get('start');
        $end = $request->get('end');
        $includeCities = $request->get('includeCities');

        $empty = [];

        $dates = Service::select(DB::raw('DISTINCT DATE(date) AS day'))
        ->between(
            Carbon::createFromFormat('d.m.Y H:i:s', $request->get('start') . ' 0:00:00'),
            Carbon::createFromFormat('d.m.Y H:i:s', $request->get('end') . ' 23:59:59')
        )->notHidden()
            ->whereDoesntHave('funerals')
            ->orderBy('day', 'ASC')
            ->get()
            ->pluck('day');

        foreach ($dates as $day) {
            foreach ($locations as $location) {
                $service = Service::where('location_id', $location->id)
                    ->notHidden()
                    ->whereDate('date', $day)
                    ->get();
                if (!count($service)) {
                    // check if there is a replacement
                    $replacement = '';
                    if (null !== $location->alternateLocation) {
                        $service = Service::where('location_id', $location->alternateLocation->id)
                            ->notHidden()
                            ->whereDate('date', $day)
                            ->get();
                        if (count($service)) {
                            $replacement = 'Gottesdienst ' . $service->first()->timeText(
                                    true,
                                    '.',
                                    true
                                ) . "\r\n" . $location->alternateLocation->general_location_name;
                        }
                    }

                    $empty[$day][$location->id] = $replacement;
                }
            }
        }


        $empty = $empty;
        $dayList = $dates->reject(function ($item) use ($empty) {
            if (!isset($empty[$item])) return false;
            if (count($empty[$item]) != 4) return false;
            unset($empty[$item]);
            return true;
        });

        return $this->renderView(
            'empty',
            compact(
                'start',
                'end',
                'includeCities',
                'locations',
                'empty',
                'dayList',
                'locationIds'
            )
        );
    }

    /**
     * @param Request $request
     * @return string|void
     * @throws Exception
     * @throws Throwable
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'includeCities' => 'required',
                'start' => 'required|date|date_format:d.m.Y',
                'end' => 'required|date|date_format:d.m.Y',
                'locations' => 'required',
                'dayList' => 'required',
            ]
        );

        $start = $request->get('start');
        $end = $request->get('end');
        $empty = $request->get('empty');


        $serviceList = Service::whereRaw('DATE(date) IN (\''.join("','",$data['dayList'])."'".')')
            ->whereIn('location_id', $data['locations'])
            ->notHidden()
            ->whereDoesntHave('funerals')
            ->whereDoesntHave('serviceGroups')
            ->ordered()
            ->get()
            ->groupBy(['location_id', 'key_date']);

        $includeCities = $request->get('includeCities');
        $dayIds = $days = $request->get('dayList');

        $locationIds = $request->get('locations');
        $locations = Location::whereIn('id', $locationIds)
            ->orderByRaw('FIELD (id, ' . implode(', ', $locationIds) . ') ASC')
            ->get();



        $specialDays = Day::with('cities')
            ->whereHas(
                'cities',
                function ($query) use ($includeCities) {
                    $query->whereIn('city_id', $includeCities);
                }
            )
            ->where('date', '>=', Carbon::createFromFormat('d.m.Y H:i:s', $start . ' 0:00:00'))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y H:i:s', $end . ' 23:59:59'))
            ->orderBy('date', 'ASC')
            ->get();

        $specialServices = [];
        foreach (ServiceGroup::all() as $group) {
            $svc = Service::between(
                Carbon::createFromFormat('d.m.Y', $data['start']),
                Carbon::createFromFormat('d.m.Y', $data['end']),
            )
                ->whereIn('city_id', $data['includeCities'])
                ->notHidden()
                ->whereDoesntHave('funerals')
                ->whereHas('serviceGroups')
                ->ordered()
                ->get();
            if (count($svc)) $specialServices2[$group->name]['services'] = $svc;
        }


        // check for same times as locations
        foreach ($specialServices as $group => $data) {
            $services = $data['services'];
            $firstService = array_first($services);
            $time = $firstService->timeText(true, '.', true);
            $sameTime = true;
            $location = $firstService->locationText();
            $sameLocation = true;

            /** @var Service $service */
            foreach ($services as $service) {
                $sameTime = $sameTime && ($time == $service->timeText(true, '.', true));
                $sameLocation = $sameLocation && ($location == $service->locationText());
            }

            $specialServices[$group]['options'] = compact('sameLocation', 'sameTime', 'location', 'time', 'group');
        }

        $empty = $request->get('empty') ?: [];

        // Debugbar needs to be off or it will interfere with rendering!
        Debugbar::disable();

        $this->idml = new IDML(base_path('assets/idml/ev3.idml'), 'ev3');
        $spreadCode = '';

        // render rectangles for table header
        $y = -8235.27722395432;
        for ($col = 0; $col < 5; $col++) {
            $spreadCode .= view(
                'reports.bulletinbl.idml.rectangle',
                [
                    'col' => $this->cols[$col],
                    'y' => $y,
                    'uid' => $this->idml->getUID('th_rect_' . $col . '_'),
                    'fillColor' => 'Color/lila',
                ]
            );
        }

        $spreadCode .= view('reports.bulletinbl.idml.tableheader')->render();

        $headerStories = [
            'u3a5d0',
            'u3a4a2',
            'u3a5b8',
            'u3ab22',
            'u3a98a',
            'u3a9a2'
        ];

        $locationsTemp = clone $locations;
        $ctr = 0;
        foreach ($headerStories as $uid) {
            if ($ctr == 0) {
                $text = 'Datum';
            } elseif ($ctr == 5) {
                $text = 'Besondere Gottesdienste';
            } else {
                $location = $locationsTemp->splice(0, 1)->first();
                $text = $location->name;
                if ($location->default_time) {
                    $text .= (strlen($text) < 28) ? $this->idml->BR() : ' - ';
                    $text .= StringTool::timeString($location->default_time, true, '.');
                }
            }
            $this->idml->renderToFile(
                'Stories/Story_' . $uid . '.xml',
                view('reports.bulletinbl.idml.tableheaderstory', compact('text', 'uid'))
            );
            $ctr++;
        }


        $y = -8193.8727735606200;

        $stories = [];
        $rowCtr = 0;
        foreach ($days as $day) {
            $rowCtr++;
            $liturgy = Liturgy::getDayInfo($day);

            if ($rowCtr % 2 == 0) {
                // even rows: render rectangles
                for ($col = 0; $col < 5; $col++) {
                    $spreadCode .= view(
                        'reports.bulletinbl.idml.rectangle',
                        [
                            'col' => $this->cols[$col],
                            'y' => $y,
                            'uid' => $this->idml->getUID('rect'),
                            'fillColor' => 'Tint/lila 11%25',
                        ]
                    );
                }
            }


            // build the story for the day title
            $this->storyFrame(
                $stories,
                $storyId,
                $spreadCode,
                $y,
                0,
                Carbon::parse($day)->format('d.m.Y') . $this->idml->BR() . ($liturgy['title'] ?? '')
            );


            $col = 0;
            foreach ($locations as $location) {
                $col++;
                $text = '';
                $service = null;
                if (isset($serviceList[$location->id][$day]) && count(
                        $serviceList[$location->id][$day]
                    )) {
                    $service = $serviceList[$location->id][$day]->first();
                    $text = $service->participantsText('P', false, false, '|')
                        . ((is_object($service->location)) && (StringTool::timeString(
                                $location->default_time,
                                true,
                                '.'
                            ) != $service->timeText(true, '.')) ? ' ' . StringTool::timeString(
                                $location->default_time,
                                true,
                                '.'
                            ) : '')
                        . $this->idml->BR()
                        . $service->description;
                } elseif (isset($empty[$location->id][$day])) {
                    $text = strtr($empty[$location->id][$day], ["\n" => $this->idml->BR(), "\r" => '']);
                }
                $this->storyFrame($stories, $storyId, $spreadCode, $y, $col, $text);

                if (!is_null($service)) {
                    $tags = $service->tags->pluck('code')->toArray();
                    if ($service->eucharist) {
                        $tags[] = 'abendmahl';
                    }
                    if ($service->baptism || (count($service->baptisms) > 0)) {
                        $tags[] = 'taufe';
                    }
                    if ($service->cc) {
                        $tags[] = 'kiki';
                    }
                    $spreadCode .= $this->setTags($col, $rowCtr, $tags);
                }
            }

            $y += 34.4685039370106;

            if ($rowCtr > 1) {
                for ($col = 0; $col < 5; $col++) {
                    $spreadCode .= view(
                        'reports.bulletinbl.idml.line',
                        [
                            'col' => $this->cols[$col],
                            'y' => $y - 5.807207480315,
                            'uid' => $this->idml->getUID('line')
                        ]
                    );
                }
            }
        }

        // include symbols table
        $spreadCode .= view('reports.bulletinbl.idml.symbolstable')->render();

        // render spread file
        $this->idml->renderToFile(
            'Spreads/Spread_u35ee1.xml',
            view('reports.bulletinbl.idml.spread', ['spreadObjects' => $spreadCode])->render()
        );
        //$this->idml->forceDownload('Spreads/Spread_u35ee1.xml', 'text/xml');

        $this->idml->renderToFile(
            'Stories/Story_u3a9b9.xml',
            view('reports.bulletinbl.idml.specialstory', compact('specialServices'))
        );

        // render designmap
        $this->idml->renderToFile('designmap.xml', view('reports.bulletinbl.idml.designmap', compact('stories')));


        $this->idml->build();
    }


    /**
     * @param $stories
     * @param $storyId
     * @param $spreadCode
     * @param $y
     * @param $col
     * @param $text
     * @param string $view
     * @return string
     * @throws Throwable
     */
    protected function storyFrame(
        &$stories,
        &$storyId,
        &$spreadCode,
        $y,
        $col,
        $text,
        $view = 'reports.bulletinbl.idml.story'
    ) {
        $storyId = $this->idml->getUID('story');
        $stories[] = $storyId;
        $storyCode = view($view, ['uid' => $storyId, 'text' => $text]);
        $this->idml->renderToFile('Stories/Story_' . $storyId . '.xml', $storyCode);

        // build a textframe for this story
        $spreadCode .= view(
            'reports.bulletinbl.idml.textframe',
            [
                'col' => $this->cols[$col],
                'y' => $y,
                'uid' => $this->idml->getUID('textframe_' . $col),
                'parentStory' => $storyId
            ]
        )->render();

        return $storyId;
    }

    /**
     * @param int $col
     * @param int $row
     * @param array $tags
     * @return string
     */
    protected function setTags(int $col, int $row, array $tags)
    {
        $offset = 0;
        $code = '';

        // calculate column offset
        $colOffset = $this->cols[$col][0] - $this->cols[1][0];
        if ($col == 3) {
            $colOffset += 33.12469843;
        }

        foreach ($tags as $tag) {
            $coords = $this->tagBaseCoords[$tag];
            $x = str_replace(
                ',',
                '.',
                (string)($coords[0] - IDML::mmToPoint($this->tagWidth[$tag]) - $offset + $colOffset)
            );
            $y = str_replace(',', '.', (string)($coords[1] + (($row - 1) * 34.4685039370106)));
            $uid = $this->idml->getUID('tag_' . $tag);
            $code .= view('reports.bulletinbl.idml.tags.' . $tag, compact('x', 'y', 'uid'));

            $offset += IDML::mmToPoint($this->tagWidth[$tag] + 1);
        }

        return $code;
    }


}
