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

use App\City;
use App\Day;
use App\Liturgy;
use App\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Tab;


class BulletinReport extends AbstractWordDocumentReport
{

    public $title = 'Gemeindebrief';
    public $group = 'Listen';
    public $description = 'Gottesdienstliste für den Gemeindebrief';

    public $formats = ['Tailfingen', 'Truchtelfingen'];

    public function setup() {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderSetupView(['maxDate' => $maxDate, 'cities' => $cities, 'formats' => $this->formats]);
    }



    public function render(Request $request)
    {
        $request->validate([
            'includeCities' => 'required',
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $days = Day::where('date', '>=', Carbon::createFromFormat('d.m.Y', $request->get('start')))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y', $request->get('end')))
            ->orderBy('date', 'ASC')
            ->get();

        $serviceList = [];
        foreach ($days as $day) {
            $serviceList[$day->date->format('Y-m-d')] = Service::with(['location', 'day'])
                ->where('day_id', $day->id)
                ->whereIn('city_id', $request->get('includeCities'))
                ->orderBy('time', 'ASC')
                ->get();
        }

        $format = $request->get('format') ?: $this->formats[0];
        $renderMethod = "render{$format}Format";
        if (method_exists($this, $renderMethod)) {
            return $this->$renderMethod($days, $serviceList);
        } else {
            return redirect()->route('reports.setup', $this->getKey());
        }
    }

    public function commonDocumentSetup() {
        $this->wordDocument->addParagraphStyle('list', [
            'tabs' => [
                new Tab('left', Converter::cmToTwip(4.5)),
                new Tab('left', Converter::cmToTwip(6.7)),
                new Tab('left', Converter::cmToTwip(9)),
            ],
            'spaceAfter' => 0,
        ]);
        $this->wordDocument->setDefaultFontName('Helvetica Condensed');
        $this->wordDocument->setDefaultFontSize(10);
        $section = $this->wordDocument->addSection([
            'marginTop' => Converter::cmToTwip('1.9'),
            'marginBottom' => Converter::cmToTwip('0.25'),
            'marginLeft' => Converter::cmToTwip('1.59'),
            'marginRight' => Converter::cmToTwip('0.25'),
        ]);
        return $section;
    }

    public function renderTailfingenFormat($days, $serviceList) {
        $section = $this->commonDocumentSetup();

        foreach ($days as $day) {
            $ctr = 0;
            foreach ($serviceList[$day->date->format('Y-m-d')] as $service) {
                $ctr++;
                $textRun = $section->addTextRun('list');
                if ($ctr == 1) {
                    $textRun->addText($day->date->format('d.m.Y'));
                }
                if ($ctr == 2) {
                    $textRun->addText(htmlspecialchars($day->name));
                }
                $textRun->addText("\t");
                $textRun->addText(strftime('%H:%M', strtotime($service->time)) . " Uhr\t");
                if (!is_object($service->location)) {
                    $textRun->addText(htmlspecialchars($service->special_location) . "\t");
                } else {
                    $textRun->addText(htmlspecialchars($service->location->name) . "\t");
                }
                $textRun->addText(htmlspecialchars($service->participantsText('P', false, false)));
                if ($service->descriptionText() != "") {
                    $textRun->addText(' - ' . htmlspecialchars($service->descriptionText()));
                }
            }
            $textRun = $section->addTextRun('list');
        }

        $filename = date('Ymd') . ' Gottesdienstliste Gemeindebrief';
        $this->sendToBrowser($filename);

    }

    public function renderTruchtelfingenFormat($days, $serviceList) {
        $section = $this->commonDocumentSetup();
        $table = $section->addTable('table');


        /** @var Day $day */
        foreach ($days as $day) {
            $liturgy = Liturgy::getDayInfo($day);
            /** @var Service $service */
            $first = true;
            foreach ($serviceList[$day->date->format('Y-m-d')] as $service) {
                $table->addRow();
                $table->addCell(Converter::cmToTwip(2.5))->addText($first ? $day->name : '');
                $table->addCell(Converter::cmToTwip(1.73))->addText($first ? $day->date->format('d.m.Y') : '');
                $table->addCell(Converter::cmToTwip(1.58))->addText(strftime('%H:%M Uhr', strtotime($service->time)));
                $table->addCell(Converter::cmToTwip(2.11))->addText($service->locationText());
                $table->addCell(Converter::cmToTwip(2.32))->addText($service->descriptionText());
                $table->addCell(Converter::cmToTwip(3.52))->addText($service->participantsText('P', false, false));
                $table->addCell(Converter::cmToTwip(2.25))->addText(isset($liturgy['perikope']) ? $liturgy['litTextsPerikope'.$liturgy['perikope']] : '');
                $table->addCell(Converter::cmToTwip(2.5))->addText($service->offering_goal ? 'Opfer für '.$service->offering_goal : '');
                $first = false;
            }
        }

        $filename = date('Ymd') . ' Gottesdienstliste Gemeindebrief';
        $this->sendToBrowser($filename);
    }

}
