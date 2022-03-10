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
use App\Liturgy;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpWord\Shared\Converter;

/**
 * Class ServiceTableReport
 * @package App\Reports
 */
class ServiceTableReport extends AbstractExcelDocumentReport
{
    /**
     * @var string
     */
    public $title = 'Jahresplan der Gottesdienste';
    /**
     * @var string
     */
    public $description = 'Große Exceltabelle mit Übersicht zu Gottesdiensten, liturgischen Farben, Opfern, ...';
    /**
     * @var string
     */
    public $group = 'Listen';


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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required|integer',
                'year' => 'required|integer',
            ]
        );


        $serviceList = Service::between(
            Carbon::createFromDate($data['year'], 1, 1),
            Carbon::createFromDate($data['year'], 12, 31)->setTime(23, 59, 59),
        )->notHidden()
            ->where('city_id', $data['city'])
            ->ordered()
            ->get()
            ->groupBy('key_date');


        $city = City::findOrFail($data['city']);


        $ministries = $request->get('ministries') ?? [];
        $nameFormat = $request->get('name_format') ?? User::NAME_FORMAT_DEFAULT;

        $columns = [
            'A' => 12.73046875,
            'B' => 15,
            'C' => 18,
            'D' => 5.86328125,
            'E' => 9.265625,
            'F' => 12,
            'G' => 11,
            'H' => 3.86328125,
            'I' => 10.5,
            'J' => 10.73046875,
            'K' => 20,
            'L' => 22.73046875,
            'M' => 10.1328125,
            'N' => 14,
            'O' => 10.73046875
        ];

        $lastColumn = chr(78 + count($ministries));
        $colCtr = 0;
        foreach ($ministries as $ministry) {
            $colCtr++;
            $col = chr(78 + $colCtr);
            $columns[$col] = 10.73046875;
        }

        $this->spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(8);
        $this->spreadsheet->setActiveSheetIndex(0);
        $sheet = $this->spreadsheet->getActiveSheet();

        // page layout
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setRowsToRepeatAtTopByStartAndEnd(3, 4);
        $sheet->getPageMargins()
            ->setTop(Converter::cmToInch(1.5))
            ->setBottom(Converter::cmToInch(1))
            ->setHeader(Converter::cmToInch(0.8))
            ->setLeft(Converter::cmToInch(1))
            ->setRight(Converter::cmToInch(1))
            ->setFooter(0);
        $sheet->getHeaderFooter()
            ->setOddHeader(
                '&LEvangelische Kirchengemeinde ' . $city->name . '&RPlan für Gottesdienste ' . $data['year'] . ' - Blatt &P von &N'
            )
            ->setEvenHeader(
                '&LEvangelische Kirchengemeinde ' . $city->name . '&RPlan für Gottesdienste ' . $data['year'] . ' - Blatt &P von &N'
            )
            ->setOddFooter('&CAusdruck vom &D, &T')
            ->setEvenFooter('&CAusdruck vom &D, &T');


        // column width
        foreach ($columns as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // title row
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->getRowDimension('1')->setRowHeight(21);
        $style = $sheet->setCellValue('A1', 'Plan für Gottesdienste - Liturgischer Kalender für das Jahr ' . $data['year'])
            ->getStyle('A1');
        $style->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('ff0066');
        $style->getFont()->setBold(true);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // header row
        $sheet->getRowDimension(3)->setRowHeight(21);
        $sheet->getRowDimension(4)->setRowHeight(21);

        $colors = [
            'white' => 'ffffffff',
            'green' => 'ff00da05',
            'purple' => 'ffdb05ff',
            'black' => 'ff808080',
            'red' => 'ffff0000',
        ];

        $headers = [
            'Datum',
            "Sonn-/Festtag\nFarbe",
            'Anmerkung zum Gottesdienst',
            "Uhr-\nzeit",
            'Ort',
            'Predigt',
            'Orgel',
            'AM',
            'Mesner',
            '',
            "Opfer-\nbestimmung",
            'Anmerkungen zum Opfer',
            "Opfer-\nbetrag",
            "Weiter-\nleitung",
        ];

        foreach ($ministries as $ministry) {
            $headers[] = $ministry;
        }

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            if ($header != '') {
                $sheet->mergeCells("{$column}3:{$column}4")->setCellValue("{$column}3", $header);
                $maxRow = 3;
            } else {
                $maxRow = 4;
                $sheet->setCellValue('J3', '1. Opferzähler');
                $sheet->setCellValue('J4', '2. Opferzähler');
            }
            for ($row = 3; $row <= $maxRow; $row++) {
                $style = $sheet->getStyle("{$column}{$row}");
                if ($column == 'C') {
                    $fontSize = 9;
                } elseif ($column == 'J') {
                    $fontSize = 6;
                } elseif (in_array($column, ['H', 'I'])) {
                    $fontSize = 8;
                } else {
                    $fontSize = 10;
                }
                $style->getFont()->setBold(true)->setSize($fontSize);
                $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                $style->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(
                    'ff000000'
                );
            }
            $sheet->getStyle("{$column}3:{$column}4")->getBorders()->getOutline()->setBorderStyle(
                Border::BORDER_THIN
            )->getColor()->setARGB('ff000000');
        }

        // content rows

        $fontSizes = [
            6 => ['E', 'L'],
            7 => ['D', 'G', 'I'],
            8 => ['A', 'B', 'C', 'F', 'H', 'J', 'K', 'M', 'N']
        ];

        $colCtr = 0;
        foreach ($ministries as $ministry) {
            $colCtr++;
            $col = chr(78 + $colCtr);
            $fontSizes[8][] = $col;
        }

        $row = 3;
        foreach ($serviceList as $services) {
            foreach ($services as $service) {
                $liturgy = Liturgy::getDayInfo($service->date, true);

                $row += 2;
                $row2 = $row + 1;

                if (($row2 - 2) % 46 == 0) {
                    $sheet->setBreak("A{$row2}", Worksheet::BREAK_ROW);
                }

                foreach (array_keys($headers) as $index) {
                    $column = chr(65 + $index);
                    if ($column != 'J') {
                        $sheet->mergeCells("{$column}{$row}:{$column}{$row2}");
                        $maxRow = $row;
                    } else {
                        $maxRow = $row2;
                    }
                    for ($thisRow = $row; $thisRow <= $maxRow; $thisRow++) {
                        $style = $sheet->getStyle("{$column}{$thisRow}");
                        // font sizes
                        for ($i = 6; $i <= 8; $i++) {
                            if (in_array($column, $fontSizes[$i])) {
                                $fontSize = $i;
                            }
                        }
                        $style->getFont()->setSize($fontSize);
                        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $style->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB(
                            'ff000000'
                        );
                    }
                    $sheet->getStyle("{$column}{$row}:{$column}{$row2}")->getBorders()->getOutline()->setBorderStyle(
                        Border::BORDER_THIN
                    )->getColor()->setARGB('ff000000');
                }


                $richtext = new RichText();
                $textrun = $richtext->createTextRun(strftime('%A,', $service->date->getTimestamp()));
                $textrun->getFont()->setName('Arial')->setSize(8)->setBold(true);
                $richtext->createText("\n" . $service->date->format('d.m.Y'));
                $sheet->getCell("A{$row}")->setValue($richtext);
                $sheet->setCellValue("B{$row}", $liturgy['title'] ?: '');
                $sheet->setCellValue("C{$row}", $service->descriptionText());
                $sheet->setCellValue("D{$row}", $service->timeText(false));
                $sheet->setCellValue("E{$row}", $service->locationText());
                $sheet->setCellValue(
                    "F{$row}",
                    $this->peopleListFormatted($service->participantsByCategory('P'), $nameFormat)
                );
                $sheet->setCellValue(
                    "G{$row}",
                    $this->peopleListFormatted($service->participantsByCategory('O'), $nameFormat)
                );
                $sheet->setCellValue("H{$row}", $service->eucharist ? 'X' : '');
                $sheet->setCellValue(
                    "I{$row}",
                    $this->peopleListFormatted($service->participantsByCategory('A'), $nameFormat)
                );
                $sheet->setCellValue("J{$row}", $service->offerings_counter1);
                $sheet->setCellValue("J{$row2}", $service->offerings_counter2);
                $sheet->setCellValue("K{$row}", $service->offeringText());
                $sheet->setCellValue("L{$row}", $service->offering_description);


                $colCtr = 0;
                foreach ($ministries as $ministry) {
                    $colCtr++;
                    $col = chr(78 + $colCtr);
                    $sheet->setCellValue(
                        "{$col}{$row}",
                        $this->peopleListFormatted($service->participantsByCategory($ministry), $nameFormat)
                    );
                }


                // COLORS:
                // red for "Konfirmation" / "Konfirmandenabendmahl"
                if ($service->hasDescription('Konfirmation') || $service->hasDescription('Konfirmandenabendmahl')) {
                    $liturgy['litColor'] = 'red';
                }
                // liturgical color
                $sheet->getStyle("B{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($colors[$liturgy['litColor']]);

                // yellow for special location
                if (!is_object($service->location)) {
                    $sheet->getStyle("E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('ffffff00');
                }

                // light green for "Gottesdienst im Grünen"
                if ($service->hasDescription('gottesdienst im grünen')) {
                    $sheet->getStyle("C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('ff92d050');
                    $sheet->getStyle("E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('ff92d050');
                }

                // colors for required/recommended offerings
                if ($service->offering_type == 'PO') {
                    $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB('ffff0000');
                }
                if ($service->offering_type == 'eO') {
                    $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB('ff838dd5');
                }

                // color for offering description
                if ($service->offering_description) {
                    $sheet->getStyle("L{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('ffffc000');
                }
            }
        }

        // output
        $filename = $data['year'] . ' Plan für Gottesdienste ' . $city->name;
        $this->sendToBrowser($filename);
    }

    /**
     * @param $people
     * @param $format
     * @return string
     */
    protected function peopleListFormatted($people, $format)
    {
        $recs = [];
        foreach ($people as $person) {
            $recs[] = $person->formattedName($format);
        }
        return join(', ', $recs);
    }

}
