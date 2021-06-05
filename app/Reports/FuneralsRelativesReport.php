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
use App\Funeral;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Exception;

/**
 * Class FuneralsRelativesReport
 * @package App\Reports
 */
class FuneralsRelativesReport extends AbstractExcelDocumentReport
{
    /**
     * @var string
     */
    public $title = 'Liste der Angehörigen';
    /**
     * @var string
     */
    public $description = 'Adressliste von Angehörigen für die Beerdigungen eines Jahres';
    /**
     * @var string
     */
    public $group = 'Kasualien';


    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $minDate = Day::orderBy('date', 'ASC')->limit(1)->get()->first();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderSetupView(['minDate' => $minDate, 'maxDate' => $maxDate, 'cities' => $cities]);
    }

    /**
     * @param Request $request
     * @return string|void
     * @throws Exception
     */
    public function render(Request $request)
    {
        $request->validate(
            [
                'city' => 'required|integer',
                'start' => 'required|date|date_format:d.m.Y',
            ]
        );

        $start = Carbon::createFromFormat('d.m.Y H:i:s', $request->get('start') . ' 0:00:00');
        $city = City::find($request->get('city'));

        $funerals = Funeral::with('service')
            ->whereHas(
                'service',
                function ($query) use ($city, $start) {
                    $query->where('city_id', $city->id);
                    $query->whereHas(
                        'day',
                        function ($query2) use ($start) {
                            $query2->where('date', '>=', $start);
                        }
                    );
                }
            )
            ->get();

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
            'Datum',
            "Verstorben_Name",
            'Verstorben_Adresse',
            "Verstorben_PLZ",
            'Verstorben_Ort',
            'Predigttext',
            'Pfarrer',
            'Friedhof',
            'Hinterblieben_Name',
            'Hinterblieben_Adresse',
            "Hinterblieben_PLZ",
            'Hinterblieben_Ort',
        ];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue("{$column}1", $header);
            $sheet->getStyle("{$column}1")->getFont()->setBold(true);
        }

        // content rows

        $row = 1;
        foreach ($funerals as $funeral) {
            $row++;
            $sheet->setCellValue("A{$row}", $funeral->service->day->date->format('d.m.Y'));
            $sheet->setCellValue("B{$row}", $funeral->buried_name);
            $sheet->setCellValue("C{$row}", $funeral->buried_address);
            $sheet->setCellValue("D{$row}", $funeral->buried_zip);
            $sheet->setCellValue("E{$row}", $funeral->buried_city);
            $sheet->setCellValue("F{$row}", $funeral->text);
            $sheet->setCellValue("G{$row}", $funeral->service->participantsText('P'));
            $sheet->setCellValue("H{$row}", $funeral->service->locationText());
            $sheet->setCellValue("I{$row}", $funeral->relative_name);
            $sheet->setCellValue("J{$row}", $funeral->relative_address);
            $sheet->setCellValue("K{$row}", $funeral->relative_zip);
            $sheet->setCellValue("L{$row}", $funeral->relative_city);
        }

        // output
        $filename = 'Beerdigungen ab ' . $start->format('Y-m-d') . ', ' . $city->name;
        $this->sendToBrowser($filename);
    }


}
