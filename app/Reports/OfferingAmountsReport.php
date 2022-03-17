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
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Class OfferingAmountsReport
 * @package App\Reports
 */
class OfferingAmountsReport extends AbstractExcelDocumentReport
{
    /**
     * @var string
     */
    public $title = 'Übersicht der eingenommenen Opfer';
    /**
     * @var string
     */
    public $description = 'Tabelle der Opferbeiträge nach Kategorien';
    /**
     * @var string
     */
    public $group = 'Opfer';

    protected $inertia = true;


    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return Inertia::render('Report/OfferingAmounts/Setup', compact('cities'));
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
                'cities.*' => 'required|integer|exists:cities,id',
                'start' => 'required|date',
                'end' => 'required|date',
            ]
        );

        $cities = City::whereIn('id', $data['cities'])->get();

        $services = Service::whereIn('city_id', $data['cities'])
            ->between(Carbon::parse($data['start']), Carbon::parse($data['end']))
            ->ordered()
            ->get();

        $offerings = [];
        foreach ($services as $service) {
            if ($service->offering_goal != '') {
                if (!isset($offerings[$service->offering_goal])) {
                    $offerings[$service->offering_goal] = ['ct' => 0, 'amount' => 0, 'done' => 0];
                }
                $offerings[$service->offering_goal]['ct']++;
                $x = trim(strtr((string)$service->offering_amount, [',' => '.', '€' => '']));
                if (is_numeric($x)) {
                    $offerings[$service->offering_goal]['amount'] += (float)$x;
                }
                if ($service->date <= Carbon::now()) {
                    $offerings[$service->offering_goal]['done']++;
                }
            }
        }

        Settings::setLocale('de');
        setlocale(LC_ALL, 'en_US.utf8');
        $this->spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(8);
        $this->spreadsheet->setActiveSheetIndex(0);
        $sheet = $this->spreadsheet->getActiveSheet();

        // column width
        for ($i = 65; $i <= 76; $i++) {
            $sheet->getColumnDimension(chr($i))->setWidth(20);
        }
        // title row

        $headers = [
            'Opferzweck',
            "Geplante Opfer",
            'Gesammelte Opfer',
            "Summe",
        ];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue("{$column}1", $header);
            $sheet->getStyle("{$column}1")->getFont()->setBold(true);
        }

        $sheet->getColumnDimensionByColumn(1)->setAutoSize(false);
        $sheet->getColumnDimensionByColumn(1)->setWidth(50);


        // content rows

        $row = 1;
        foreach ($offerings as $category => $data2) {
            $row++;
            $sheet->setCellValue("A{$row}", $category);
            $sheet->setCellValue("B{$row}", $data2['ct']);
            $sheet->setCellValue("C{$row}", $data2['done']);
            $sheet->setCellValueExplicit(
                "D{$row}",
                (float)str_replace(',', '.', (string)$data2['amount']),
                DataType::TYPE_NUMERIC
            );
            //$sheet->setCellValue("D{$row}", ));
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.00_-€');
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // output
        $filename = 'Opfersummen von ' . Carbon::parse($data['start'])->format('Y-m-d') . ' bis ' . Carbon::parse($data['end'])->format(
                'Y-m-d'
            ) . ' -- ' . $cities->pluck('name')->join(', ');
        $this->sendToBrowser($filename);
    }


}
