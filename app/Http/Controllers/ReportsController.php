<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
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

        for ($i=1; $i<=3; $i++) {
            $section->addText('', [], ['spaceAfter' => 0]);
        }
        $section->addText('Evang. Kirchengemeinde '.$city->name, [], [
            "borderSize" => 6,
            "borderColor" => "000000",
            'indentation' => ['right' => Converter::cmToTwip(10.5)],
            'spaceAfter' => 0,
        ]);
        for ($i=1; $i<=4; $i++) {
            $section->addText('', [], ['spaceAfter' => 0]);
        }

        $doc->addTableStyle('table' ,[
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
                $table->addCell(Converter::cmToTwip(3.25))->addText($day->date->format('d.m.Y').'<w:br />');
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

}
