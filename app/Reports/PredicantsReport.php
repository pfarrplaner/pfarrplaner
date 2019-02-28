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
use App\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Section;

class PredicantsReport extends AbstractWordDocumentReport
{

    public $title = 'Prädikantenanforderung';
    public $group = 'Formulare';
    public $description = 'Vorausgefülltes Prädikantenformular für das Dekanatamt';

    public function setup() {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderSetupView(['maxDate' => $maxDate, 'cities' => $cities]);
    }

    public function render(Request $request)
    {
        $request->validate([
            'city' => 'required|integer',
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $days = Day::where('date', '>=', Carbon::createFromFormat('d.m.Y', $request->get('start')))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y', $request->get('end')))
            ->orderBy('date', 'ASC')
            ->get();

        $city = City::find($request->get('city'));

        $serviceList = [];
        foreach ($days as $day) {
            $serviceList[$day->date->format('Y-m-d')] = Service::with(['location', 'day'])
                ->join('days', 'days.id', '=', 'day_id')
                ->where('day_id', $day->id)
                ->where('need_predicant', 1)
                ->where('city_id', $city->id)
                ->orderBy('days.date', 'ASC')
                ->orderBy('time', 'ASC')
                ->get();
        }

        foreach ($serviceList as $key => $item) {
            if (!count($item)) {
                unset($serviceList[$key]);
            }
        }

        $this->wordDocument->setDefaultFontName('Arial');
        $this->wordDocument->setDefaultFontSize(11);
        $section = $this->wordDocument->addSection([
            'orientation' => Section::ORIENTATION_LANDSCAPE,
            'marginTop' => Converter::cmToTwip('1.59'),
            'marginBottom' => Converter::cmToTwip('2'),
            'marginLeft' => Converter::cmToTwip('2'),
            'marginRight' => Converter::cmToTwip('2.5'),
        ]);


        $section->addText('Anforderung von Prädikant/innen bzw. Pfarrer/innen im Ruhestand über das Dekanatamt',
            [
                'size' => 13,
                'bold' => true,
            ], [
                'alignment' => 'center',
                "borderSize" => 6,
                "borderColor" => "000000",
                'spaceAfter' => 0,
            ]);

        for ($i = 1; $i <= 3; $i++) {
            $section->addText('', [], ['spaceAfter' => 0]);
        }
        $section->addText('Evang. Kirchengemeinde ' . $city->name, [], [
            "borderSize" => 6,
            "borderColor" => "000000",
            'indentation' => ['right' => Converter::cmToTwip(10.5)],
            'spaceAfter' => 0,
        ]);
        for ($i = 1; $i <= 4; $i++) {
            $section->addText('', [], ['spaceAfter' => 0]);
        }

        $this->wordDocument->addTableStyle('table', [
            'borderSize' => 6,
            'borderColor' => '000000',
        ], []);

        $table = $section->addTable('table');
        $table->addRow();
        $table->addCell(Converter::cmToTwip(3.25))->addText("Datum<w:br />", ['bold' => true]);
        $table->addCell(Converter::cmToTwip(5))->addText("Kirche /<w:br />Gemeindezentrum", ['bold' => true]);
        $table->addCell(Converter::cmToTwip(3.75))->addText("Beginn des<w:br />Gottesdienstes", ['bold' => true]);
        $table->addCell(Converter::cmToTwip(6))->addText("Abendmahl / Taufe /<w:br />Bemerkungen", ['bold' => true]);
        $textRun = $table->addCell(Converter::cmToTwip(7.25))->addTextRun();
        $textRun->addText('Rückmeldung Dekanatamt<w:br />', [
            'bold' => true,
            'underline' => Font::UNDERLINE_SINGLE,
            'italic' => true,
        ]);
        $textRun->addText('GD-Vertretung übernimmt:', ['bold' => true]);

        foreach ($serviceList as $services) {
            foreach ($services as $service) {
                $day = Day::find($service->getAttribute('day_id'));
                $table->addRow();
                $table->addCell(Converter::cmToTwip(3.25))->addText($day->date->format('d.m.Y') . '<w:br />');
                $table->addCell(Converter::cmToTwip(5))->addText($service->locationText());
                $table->addCell(Converter::cmToTwip(3.25))->addText(strftime('%H:%M Uhr', strtotime($service->time)));
                $table->addCell(Converter::cmToTwip(6))->addText($service->descriptionText());
                $table->addCell(Converter::cmToTwip(7.25));
            }
        }

        $filename = date('Ymd') . ' Prädikantenanforderung ' . $city->name;
        $this->sendToBrowser($filename);
    }

}
