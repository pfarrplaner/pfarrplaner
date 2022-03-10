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
use App\Day;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Section;

/**
 * Class PredicantsReport
 * @package App\Reports
 */
class PredicantsReport extends AbstractWordDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Prädikantenanforderung';
    /**
     * @var string
     */
    public $group = 'Formulare';
    /**
     * @var string
     */
    public $description = 'Vorausgefülltes Prädikantenformular für das Dekanatamt';

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderSetupView(['maxDate' => $maxDate, 'cities' => $cities]);
    }

    /**
     * @param Request $request
     * @return string|void
     * @throws Exception
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required|integer',
                'start' => 'required|date|date_format:d.m.Y',
                'end' => 'required|date|date_format:d.m.Y',
            ]
        );

        $serviceList = Service::between(Carbon::createFromFormat('d.m.Y', $data['start']), Carbon::createFromFormat('d.m.Y', $data['end']))
            ->notHidden()
            ->whereIn('city_id', $request->get('includeCities'))
            ->ordered()
            ->get()
            ->groupBy('key_date');



        $city = City::find($data['city']);

        $this->wordDocument->setDefaultFontName('Arial');
        $this->wordDocument->setDefaultFontSize(11);
        $section = $this->wordDocument->addSection(
            [
                'orientation' => Section::ORIENTATION_LANDSCAPE,
                'marginTop' => Converter::cmToTwip('1.59'),
                'marginBottom' => Converter::cmToTwip('2'),
                'marginLeft' => Converter::cmToTwip('2'),
                'marginRight' => Converter::cmToTwip('2.5'),
            ]
        );


        $section->addText(
            'Anforderung von Prädikant/innen bzw. Pfarrer/innen im Ruhestand über das Dekanatamt',
            [
                'size' => 13,
                'bold' => true,
            ],
            [
                'alignment' => 'center',
                "borderSize" => 6,
                "borderColor" => "000000",
                'spaceAfter' => 0,
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $section->addText('', [], ['spaceAfter' => 0]);
        }
        $section->addText(
            'Evang. Kirchengemeinde ' . $city->name,
            [],
            [
                "borderSize" => 6,
                "borderColor" => "000000",
                'indentation' => ['right' => Converter::cmToTwip(10.5)],
                'spaceAfter' => 0,
            ]
        );
        for ($i = 1; $i <= 4; $i++) {
            $section->addText('', [], ['spaceAfter' => 0]);
        }

        $this->wordDocument->addTableStyle(
            'table',
            [
                'borderSize' => 6,
                'borderColor' => '000000',
            ],
            []
        );

        $table = $section->addTable('table');
        $table->addRow();
        $table->addCell(Converter::cmToTwip(3.25))->addText("Datum<w:br />", ['bold' => true]);
        $table->addCell(Converter::cmToTwip(5))->addText("Kirche /<w:br />Gemeindezentrum", ['bold' => true]);
        $table->addCell(Converter::cmToTwip(3.75))->addText("Beginn des<w:br />Gottesdienstes", ['bold' => true]);
        $table->addCell(Converter::cmToTwip(6))->addText("Abendmahl / Taufe /<w:br />Bemerkungen", ['bold' => true]);
        $textRun = $table->addCell(Converter::cmToTwip(7.25))->addTextRun();
        $textRun->addText(
            'Rückmeldung Dekanatamt<w:br />',
            [
                'bold' => true,
                'underline' => Font::UNDERLINE_SINGLE,
                'italic' => true,
            ]
        );
        $textRun->addText('GD-Vertretung übernimmt:', ['bold' => true]);

        foreach ($serviceList as $services) {
            foreach ($services as $service) {
                $table->addRow();
                $table->addCell(Converter::cmToTwip(3.25))->addText($service->date->format('d.m.Y') . '<w:br />');
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
