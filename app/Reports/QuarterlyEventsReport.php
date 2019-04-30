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
use App\Location;
use App\Service;
use function Complex\add;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\Adapter\AbstractAdapter;
use niklasravnsborg\LaravelPdf\Pdf;
use PhpOffice\PhpWord\Reader\HTML;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Section;
use PhpOffice\PhpWord\Style\Tab;
use PhpOffice\PhpWord\Style\Table;


class QuarterlyEventsReport extends AbstractWordDocumentReport
{

    public $title = 'Quartalsprogramm';
    public $group = 'Listen';
    public $description = 'Übersicht aller Termine für ein Quartal an einem bestimmten Veranstaltungsort';

    public function setup() {
        $minDate = max(Carbon::now(), Day::orderBy('date', 'ASC')->limit(1)->get()->first()->getAttribute('date'));
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first()->getAttribute('date');
        $locations = Location::whereIn('city_id', Auth::user()->cities->pluck('id'))->get();
        return $this->renderSetupView(compact('minDate', 'maxDate', 'locations'));
    }

    public function render(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'quarter' => 'required',
            'location' => 'required|integer',
        ]);

        $location = Location::find($request->get('location'));
        $title = $request->get('title') ?: 'Quartalsprogramm für '.$location->name;
        $quarter = Carbon::createFromFormat('Y-m-d', $request->get('quarter'));

        $days = Day::where('date', '>=', $quarter->format('Y-m-d'))
            ->where('date', '<=', $quarter->endOfQuarter()->format('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->get();

        $serviceList = [];
        foreach ($days as $day) {
            $serviceList[$day->date->format('Y-m-d')] = Service::with(['location', 'day'])
                ->where('day_id', $day->id)
                ->where('location_id', $location->id)
                ->orderBy('time', 'ASC')
                ->get();
        }

        $dates = [];
        foreach ($serviceList as $day => $services) {
            foreach ($services as $service) {
                $dates[] = $service->day->date;
            }
        }

        $this->wordDocument->setDefaultFontName('Helvetica Condensed');
        $this->wordDocument->setDefaultFontSize(14);
        $section = $this->wordDocument->addSection([
            'orientation' => Section::ORIENTATION_PORTRAIT,
            'marginTop' => Converter::cmToTwip('1.59'),
            'marginBottom' => Converter::cmToTwip('2'),
            'marginLeft' => Converter::cmToTwip('2'),
            'marginRight' => Converter::cmToTwip('2.5'),
        ]);


        $section->addText($title,
            [
                'size' => 24,
                'bold' => true,
            ]);
        $sectionStyle = $section->getStyle();

        $section->addText('Für das '.$quarter->quarter.'. Quartal '.$quarter->year, ['size' => 18]);

        if ($notes = $request->get('notes1')) {
            $section->addText(str_replace("\n", '<w:br />', $notes));
            $section->addText();
        }

        $this->wordDocument->addTableStyle('table', [
            'unit' => 'dxa',
            'width' => $sectionStyle->getPageSizeW()-$sectionStyle->getMarginLeft()-$sectionStyle->getMarginRight(),
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => Converter::cmToTwip('0.2'),
        ], []);

        $table = $section->addTable('table');
        $table->addRow();
        $table->addCell()->addText('Datum', ['bold' => true]);
        $table->addCell()->addText('Uhrzeit', ['bold' => true]);
        if ($request->get('includePastor')) $table->addCell()->addText('Pfarrer*in', ['bold' => true]);
        if ($request->get('includeOrganist')) $table->addCell()->addText('Organist*in', ['bold' => true]);
        if ($request->get('includeSacristan')) $table->addCell()->addText('Mesner*in', ['bold' => true]);
        if ($request->get('includeDescription')) $table->addCell()->addText('Hinweise', ['bold' => true]);

        foreach ($serviceList as $dayList) {
            foreach ($dayList as $service) {
                $table->addRow();
                $table->addCell()->addText(strftime('%a., %d. %B', $service->day->date->getTimeStamp()));
                $table->addCell()->addText(strftime('%H:%M Uhr', strtotime($service->time)));
                if ($request->get('includePastor')) $table->addCell()->addText($service->participantsText('P'));
                if ($request->get('includeOrganist')) $table->addCell()->addText($service->participantsText('O'));
                if ($request->get('includeSacristan')) $table->addCell()->addText($service->participantsText('M'));
                if ($request->get('includeDescription')) $table->addCell()->addText($service->descriptionText());
            }
        }

        if ($notes = $request->get('notes2')) {
            $section->addText();
            $section->addText(str_replace("\n", '<w:br />', $notes));
        }

        if ($request->get('includeContact')) {
            if ($x = Auth::user()->office) $data[] = $x;
            $data[] = Auth::user()->name;
            if ($x = Auth::user()->phone) $data[] = 'Tel. '.$x;
            $data[] = Auth::user()->email;

            $section->addText();
            $run = $section->addTextRun();
            $run->addText('Weitere Informationen:', ['underline' => Font::UNDERLINE_SINGLE]);
            $run->addText('<w:br />');
            $run->addText(join(', ', $data));
        }

        $section->addText('Stand: '.Carbon::now()->setTimezone(new \DateTimeZone('Europe/Berlin'))->format('d.m.Y, H:i').' Uhr', ['size' => 7]);


        $filename = $quarter->year.'-'.$quarter->quarter. ' '.$title;
        $this->sendToBrowser($filename);

    }

}
