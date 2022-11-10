<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
use App\Participant;
use App\Service;
use App\Services\NameService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ServicesByWTCCategoryReport extends AbstractExcelDocumentReport
{

    /**
     * @var string
     */
    public $group = 'Personal';
    protected $inertia = true;
    public $title = 'Gottesdienste nach AZE-Kategorie';
    public $description = 'Gottesdienste von hauptamtlichen Mitarbeitern in einem bestimmten Zeitraum mit AZE-Kategorie';


    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        $people = User::all();
        return Inertia::render('Report/ServicesByWTCCategory/Setup', compact('cities', 'people'));
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
                'people.*' => 'required|integer|exists:users,id',
                'start' => 'required|date',
                'end' => 'required|date',
            ]
        );

        $data['start'] = Carbon::createFromFormat('d.m.Y', $data['start'], 'Europe/Berlin')->setTime(0, 0, 0);
        $data['end'] = Carbon::createFromFormat('d.m.Y', $data['end'], 'Europe/Berlin')->setTime(23, 59, 59);

        $cities = City::whereIn('id', $data['cities'])->get();

        $records = [];
        foreach ($data['people'] as $userId) {
            $user = User::find($userId);
            if ($user) {
                $record = ['user' => $user];

                // get all the user's services for a year
                $serviceIds = Service::select('services.id')
                    ->startingFrom($data['start'])
                    ->endingAt($data['end'])
                    ->whereIn('city_id', $data['cities'])
                    ->userParticipates($user)
                    ->get()->pluck('id');

                $ptcCategories = Participant::where('user_id', $user->id)
                    ->whereIn('service_id', $serviceIds)
                    ->get()
                    ->pluck('category')
                    ->unique();

                foreach ($ptcCategories as $ptcCategory) {
                    foreach (['HG', 'WG', null] as $wtcCategory) {
                        $record['categories'][$ptcCategory][($wtcCategory ?? 'ohne')] = Service::whereIn('services.id', $serviceIds)
                            ->userParticipates($user, $ptcCategory)
                            ->where('wtc_category', $wtcCategory)
                            ->count();
                    }
                }
                $records[] = $record;
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

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

        $conditionalPositive = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalPositive->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditionalPositive->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL);
        $conditionalPositive->addCondition(0);
        $conditionalPositive->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
        $conditionalPositive->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $conditionalPositive->getStyle()->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

        $conditionalNegative = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalNegative->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
        $conditionalNegative->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
        $conditionalNegative->addCondition(0);
        $conditionalNegative->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $conditionalNegative->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $conditionalNegative->getStyle()->getFill()->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);


        $headers = [
            'Person',
            'Tätig als',
            'Hauptgd.',
            'Sonst.',
            'Ohne',
            'Summe',
            'Hauptgd.',
            'Sonst.',
            'Ohne',
            'Summe',
            'Hauptgd.',
            'Sonst.',
            'Ohne',
            'Summe',
        ];

        $sheet->setCellValue("A1", 'AZE-Statistik vom '.$data['start']->format('d.m.Y').' bis '.$data['end']->format('d.m.Y'));
        $sheet->getStyle("A1")->getFont()->setBold(true);
        $sheet->getStyle("A1")->getFont()->setSize(16);


        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue("{$column}4", $header);
            $sheet->getStyle("{$column}4")->getFont()->setBold(true);
        }
        $sheet->setCellValue("C3", 'Geleistet');
        $sheet->getStyle("C3")->getFont()->setBold(true);
        $sheet->setCellValue("G3", 'Laut AZE');
        $sheet->getStyle("G3")->getFont()->setBold(true);
        $sheet->setCellValue("K3", 'Saldo');
        $sheet->getStyle("K3")->getFont()->setBold(true);

        $sheet->getColumnDimensionByColumn(1)->setAutoSize(false);
        $sheet->getColumnDimensionByColumn(1)->setWidth(40);
        for($i=2; $i<=14; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $rowCtr = 5;
        foreach ($records as $record) {
            $sheet->setCellValue("A{$rowCtr}", NameService::fromUser($record['user'])->format(NameService::LAST_COMMA_FIRST));
            $sheet->getStyle("A{$rowCtr}")->getFont()->setBold(true);
            foreach ($record['categories'] as $key => $categoryData) {
                $title = $key;
                if ($title == 'P') $title = 'Pfarrer:in';
                if ($title == 'O') $title = 'Organist:in';
                if ($title == 'M') $title = 'Mesner:in';
                if ($title == 'A') $title = 'Sonstige';
                $sheet->setCellValue("B{$rowCtr}", $title);
                $sheet->setCellValue("C{$rowCtr}", $categoryData['HG']);
                $sheet->setCellValue("D{$rowCtr}", $categoryData['WG']);
                $sheet->setCellValue("E{$rowCtr}", $categoryData['ohne']);
                $sheet->setCellValue("F{$rowCtr}", "=SUM(C{$rowCtr}:E{$rowCtr})");
                $sheet->setCellValue("G{$rowCtr}", 0);
                $sheet->setCellValue("H{$rowCtr}", 0);
                $sheet->setCellValue("I{$rowCtr}", 0);
                $sheet->setCellValue("J{$rowCtr}", "=SUM(G{$rowCtr}:I{$rowCtr})");
                $sheet->setCellValue("K{$rowCtr}", "=(C{$rowCtr}-G{$rowCtr})");
                $sheet->setCellValue("L{$rowCtr}", "=(D{$rowCtr}-H{$rowCtr})");
                $sheet->setCellValue("M{$rowCtr}", "=(E{$rowCtr}-I{$rowCtr})");
                $sheet->setCellValue("N{$rowCtr}", "=SUM(K{$rowCtr}:M{$rowCtr})");
                $sheet->getStyle("K{$rowCtr}")->getNumberFormat()->setFormatCode('+0;-0');
                $sheet->getStyle("L{$rowCtr}")->getNumberFormat()->setFormatCode('+0;-0');
                $sheet->getStyle("M{$rowCtr}")->getNumberFormat()->setFormatCode('+0;-0');
                $sheet->getStyle("N{$rowCtr}")->getNumberFormat()->setFormatCode('+0;-0');

                $sheet->getStyle("K{$rowCtr}:N{$rowCtr}")->setConditionalStyles([$conditionalNegative, $conditionalPositive]);
                $rowCtr++;
            }
            if (!count($record['categories'])) $rowCtr++;
        }


        // output
        $filename = 'AZE-Statistik von ' . Carbon::parse($data['start'])->format('Y-m-d') . ' bis ' . Carbon::parse($data['end'])->format(
                'Y-m-d'
            ) . ' -- ' . $cities->pluck('name')->join(', ');
        $this->sendToBrowser($filename);


    }

}
