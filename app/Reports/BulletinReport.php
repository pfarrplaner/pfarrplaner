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
use App\Liturgy;
use App\Service;
use Carbon\Carbon;
use Carbon\PHPStan\AbstractMacro;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Tab;


/**
 * Class BulletinReport
 * @package App\Reports
 */
class BulletinReport extends AbstractWordDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Gemeindebrief';
    /**
     * @var string
     */
    public $group = 'Listen';
    /**
     * @var string
     */
    public $description = 'Gottesdienstliste für den Gemeindebrief';

    /**
     * @var string[]
     */
    public $formats = ['Tailfingen', 'Truchtelfingen'];

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderSetupView(['maxDate' => $maxDate, 'cities' => $cities, 'formats' => $this->formats]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'includeCities' => 'required',
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


        $format = $request->get('format') ?: $this->formats[0];
        $renderMethod = "render{$format}Format";
        if (method_exists($this, $renderMethod)) {
            return $this->$renderMethod($serviceList);
        } else {
            return redirect()->route('reports.setup', $this->getKey());
        }
    }

    /**
     * @param $days
     * @param $serviceList
     */
    public function renderTailfingenFormat($serviceList)
    {
        $section = $this->commonDocumentSetup();
        $table = $section->addTable('table');

        foreach ($serviceList as $dayList) {
            $ctr = 0;
            foreach ($dayList as $service) {
                $ctr++;
                $textRun = $section->addTextRun('list');
                if ($ctr == 1) {
                    $textRun->addText($service->date->format('d.m.Y'));
                }
                if ($ctr == 2) {
                    $textRun->addText(htmlspecialchars($dayList->name));
                }
                $textRun->addText("\t");
                $textRun->addText($service->timeText()."\t");
                if (!is_object($service->location)) {
                    $textRun->addText(htmlspecialchars($service->special_location) . "\t");
                } else {
                    $textRun->addText(htmlspecialchars($service->location->name) . "\t");
                }
                $textRun->addText(htmlspecialchars($service->participantsText('P', false, false)));
                if ($x = $service->titleAndDescriptionCombinedText()) {
                    $textRun->addText(' - ' . htmlspecialchars($x));
                }
            }
            $textRun = $section->addTextRun('list');
        }

        $filename = date('Ymd') . ' Gottesdienstliste Gemeindebrief';
        $this->sendToBrowser($filename);
    }

    /**
     * @return Section
     */
    public function commonDocumentSetup()
    {
        $this->wordDocument->addParagraphStyle(
            'list',
            [
                'tabs' => [
                    new Tab('left', Converter::cmToTwip(4.5)),
                    new Tab('left', Converter::cmToTwip(6.7)),
                    new Tab('left', Converter::cmToTwip(9)),
                ],
                'spaceAfter' => 0,
            ]
        );
        $this->wordDocument->setDefaultFontName('Helvetica Condensed');
        $this->wordDocument->setDefaultFontSize(10);
        $section = $this->wordDocument->addSection(
            [
                'marginTop' => Converter::cmToTwip('1.9'),
                'marginBottom' => Converter::cmToTwip('0.25'),
                'marginLeft' => Converter::cmToTwip('1.59'),
                'marginRight' => Converter::cmToTwip('0.25'),
            ]
        );
        return $section;
    }

    /**
     * @param $days
     * @param $serviceList
     */
    public function renderTruchtelfingenFormat($serviceList)
    {
        $section = $this->commonDocumentSetup();
        $table = $section->addTable('table');

        foreach ($serviceList as $day => $dayList) {
            $liturgy = Liturgy::getDayInfo($day);
            /** @var Service $service */
            $first = true;
            foreach ($dayList as $service) {
                $table->addRow();
                $table->addCell(Converter::cmToTwip(2.5))->addText($first ? ($liturgy['title'] ?? '') : '');
                $table->addCell(Converter::cmToTwip(1.73))->addText($first ? $service->date->format('d.m.Y') : '');
                $table->addCell(Converter::cmToTwip(1.58))->addText($service->timeText());
                $table->addCell(Converter::cmToTwip(2.11))->addText($service->locationText());
                $table->addCell(Converter::cmToTwip(2.32))->addText($service->descriptionText());
                $table->addCell(Converter::cmToTwip(3.52))->addText($service->participantsText('P', false, false));
                $table->addCell(Converter::cmToTwip(2.25))->addText(
                    isset($liturgy['perikope']) ? $liturgy['litTextsPerikope' . $liturgy['perikope']] : ''
                );
                $table->addCell(Converter::cmToTwip(2.5))->addText(
                    $service->offering_goal ? 'Opfer für ' . $service->offering_goal : ''
                );
                $first = false;
            }
        }

        $filename = date('Ymd') . ' Gottesdienstliste Gemeindebrief';
        $this->sendToBrowser($filename);
    }

}
