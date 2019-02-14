<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Liturgy;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Section;
use PhpOffice\PhpWord\Style\Tab;

class ReportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function gemeindebriefSetup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = City::all();
        return view('reports.gemeindebrief.setup', ['maxDate' => $maxDate, 'cities' => $cities]);
    }

    public function gemeindebrief(Request $request)
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
            $serviceList[$day->date->format('Y-m-d')] = Service::with(['location'])
                ->join('days', 'days.id', '=', 'day_id')
                ->where('day_id', $day->id)
                ->whereIn('city_id', $request->get('includeCities'))
                ->orderBy('days.date', 'ASC')
                ->orderBy('time', 'ASC')
                ->get();
        }
        $doc = new PhpWord();
        $doc->addParagraphStyle('list', [
            'tabs' => [
                new Tab('left', Converter::cmToTwip(4.5)),
                new Tab('left', Converter::cmToTwip(6.7)),
                new Tab('left', Converter::cmToTwip(9)),
            ],
            'spaceAfter' => 0,
        ]);
        $doc->setDefaultFontName('Helvetica Condensed');
        $doc->setDefaultFontSize(10);
        $section = $doc->addSection([
            'marginTop' => Converter::cmToTwip('1.9'),
            'marginBottom' => Converter::cmToTwip('0.25'),
            'marginLeft' => Converter::cmToTwip('1.59'),
            'marginRight' => Converter::cmToTwip('0.25'),
        ]);

        $filename = date('Ymd') . ' Gottesdienstliste Gemeindebrief';

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
                if ($service->special_location) {
                    $textRun->addText(htmlspecialchars($service->special_location) . "\t");
                } else {
                    $textRun->addText(htmlspecialchars($service->location->name) . "\t");
                }
                $textRun->addText(htmlspecialchars($service->pastor));
                if ($service->descriptionText() != "") {
                    $textRun->addText(' - ' . htmlspecialchars($service->descriptionText()));
                }
            }
            $textRun = $section->addTextRun('list');
        }

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($doc, 'Word2007');
        $objWriter->save('php://output');
        exit();
    }

    public function personSetup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $name = explode(' ', Auth::user()->name);
        $name = end($name);
        return view('reports.person.setup', ['maxDate' => $maxDate, 'lastName' => $name]);
    }

    public function person(Request $request)
    {
        $request->validate([
            'highlight' => 'required',
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $services = Service::with(['location'])
            ->join('days', 'days.id', '=', 'day_id')
            ->where(function ($query) use ($request) {
                $query->where('pastor', 'like', '%' . $request->get('highlight') . '%')
                    ->orWhere('organist', 'like', '%' . $request->get('highlight') . '%')
                    ->orWhere('sacristan', 'like', '%' . $request->get('highlight') . '%');
            })->whereHas('day', function ($query) use ($request) {
                $query->where('date', '>=', Carbon::createFromFormat('d.m.Y', $request->get('start')));
                $query->where('date', '<=', Carbon::createFromFormat('d.m.Y', $request->get('end')));
            })->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        $data = [
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'highlight' => ucfirst($request->get('highlight')),
            'services' => $services,
        ];

        $pdf = PDF::loadView('reports.person.pdf', $data, [], [
            'format' => 'A4',
            'author' => isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email,
        ]);
        return $pdf->stream(date('Ymd') . ' Gottesdienstliste ' . $request->get('highlight') . '.pdf');
    }

    public function predicantsSetup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return view('reports.predicants.setup', ['maxDate' => $maxDate, 'cities' => $cities]);
    }

    public function predicants(Request $request)
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

        $doc = new PhpWord();
        $doc->setDefaultFontName('Arial');
        $doc->setDefaultFontSize(11);
        $section = $doc->addSection([
            'orientation' => Section::ORIENTATION_LANDSCAPE,
            'marginTop' => Converter::cmToTwip('1.59'),
            'marginBottom' => Converter::cmToTwip('2'),
            'marginLeft' => Converter::cmToTwip('2'),
            'marginRight' => Converter::cmToTwip('2.5'),
        ]);

        $filename = date('Ymd') . ' Prädikantenanforderung ' . $city->name;

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

        $doc->addTableStyle('table', [
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

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($doc, 'Word2007');
        $objWriter->save('php://output');
        exit();
    }

    public function largetableSetup()
    {
        $minDate = Day::orderBy('date', 'ASC')->limit(1)->get()->first();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return view('reports.largetable.setup', ['minDate' => $minDate, 'maxDate' => $maxDate, 'cities' => $cities]);
    }

    public function largetable(Request $request)
    {
        $request->validate([
            'city' => 'required|integer',
            'year' => 'required|integer',
        ]);

        $year = $request->get('year');
        $days = Day::where('date', '>=', Carbon::createFromDate($year, 1, 1)->setTime(0,0,0))
            ->where('date', '<=', Carbon::createFromDate($year, 12, 31)->setTime(23,59,59))
            ->orderBy('date', 'ASC')
            ->get();

        $city = City::find($request->get('city'));

        $serviceList = [];
        foreach ($days as $day) {
            $serviceList[$day->date->format('Y-m-d')] = Service::with(['location', 'day'])
                ->where('day_id', $day->id)
                ->where('city_id', $city->id)
                ->orderBy('time', 'ASC')
                ->get();
        }

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

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(8);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // page layout
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setRowsToRepeatAtTopByStartAndEnd(3,4);
        $sheet->getPageMargins()
            ->setTop(Converter::cmToInch(1.5))
            ->setBottom(Converter::cmToInch(1))
            ->setHeader(Converter::cmToInch(0.8))
            ->setLeft(Converter::cmToInch(1))
            ->setRight(Converter::cmToInch(1))
            ->setFooter(0);
        $sheet->getHeaderFooter()
            ->setOddHeader('&LEvangelische Kirchengemeinde '.$city->name.'&RPlan für Gottesdienste '.$year.' - Blatt &P von &N')
            ->setEvenHeader('&LEvangelische Kirchengemeinde '.$city->name.'&RPlan für Gottesdienste '.$year.' - Blatt &P von &N')
            ->setOddFooter('&CAusdruck vom &D, &T')
            ->setEvenFooter('&CAusdruck vom &D, &T');



        // column width
        foreach ($columns as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // title row
        $sheet->mergeCells('A1:N1');
        $sheet->getRowDimension('1')->setRowHeight(21);
        $style = $sheet->setCellValue('A1', 'Plan für Gottesdienste - Liturgischer Kalender für das Jahr '.$year)
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
                $style->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('ff000000');
            }
            $sheet->getStyle("{$column}3:{$column}4")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('ff000000');
        }

        // content rows

        $fontSizes = [
            6 => ['E', 'L'],
            7 => ['D', 'G', 'I'],
            8 => ['A', 'B', 'C', 'F', 'H', 'J', 'K', 'M', 'N']
        ];

        $row = 3;
        foreach ($serviceList as $services) {
            foreach ($services as $service) {

                $liturgy = Liturgy::getDayInfo($service->day, true);

                $row += 2;
                $row2 = $row+1;

                if (($row2-2) % 46 == 0) $sheet->setBreak("A{$row2}", Worksheet::BREAK_ROW);

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
                        for ($i=6; $i<=8; $i++) {
                            if (in_array($column, $fontSizes[$i])) $fontSize = $i;
                        }
                        $style->getFont()->setSize($fontSize);
                        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $style->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('ff000000');
                    }
                    $sheet->getStyle("{$column}{$row}:{$column}{$row2}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('ff000000');

                }


                $richtext = new RichText();
                $textrun = $richtext->createTextRun(strftime('%A,', $service->day->date->getTimestamp()));
                $textrun->getFont()->setName('Arial')->setSize(8)->setBold(true);
                $richtext->createText("\n".$service->day->date->format('d.m.Y'));
                $sheet->getCell("A{$row}")->setValue($richtext);
                $sheet->setCellValue("B{$row}", $service->day->name ?: $liturgy['title']);
                $sheet->setCellValue("C{$row}", $service->descriptionText());
                $sheet->setCellValue("D{$row}", strftime('%H:%M', strtotime($service->time)));
                $sheet->setCellValue("E{$row}", $service->locationText());
                $sheet->setCellValue("F{$row}", $service->pastor);
                $sheet->setCellValue("G{$row}", $service->organist);
                $sheet->setCellValue("H{$row}", $service->eucharist ? 'X' : '');
                $sheet->setCellValue("I{$row}", $service->sacristan);
                $sheet->setCellValue("J{$row}", $service->offerings_counter1);
                $sheet->setCellValue("J{$row2}", $service->offerings_counter2);
                $sheet->setCellValue("K{$row}", $service->offeringText());
                $sheet->setCellValue("L{$row}", $service->offering_description);


                // COLORS:
                // red for "Konfirmation" / "Konfirmandenabendmahl"
                if ($service->hasDescription('Konfirmation') || $service->hasDescription('Konfirmandenabendmahl')) {
                    $liturgy['litColor'] = 'red';
                }
                // liturgical color
                $sheet->getStyle("B{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($colors[$liturgy['litColor']]);

                // yellow for special location
                if ($service->special_location) {
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
                if ($service->offering_type=='PO') {
                    $sheet->getStyle("K{$row}")->getFont()->getColor()->setARGB('ffff0000');
                }
                if ($service->offering_type=='eO') {
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
        $filename = $year.' Plan für Gottesdienste '.$city->name;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
