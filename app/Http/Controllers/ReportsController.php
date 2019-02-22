<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Liturgy;
use App\Reports\AbstractReport;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpWord\Shared\Converter;

class ReportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list() {
        $reports = [];
        foreach (glob(app_path('Reports').'/*Report.php') as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) !== 'Abstract') {
                $reportClass = 'App\\Reports\\'.pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($reportClass));
                $report = new $reportClass();
                $reports[$report->group][$report->title] = $report ;
                ksort($reports[$report->group]);
            }
        }
        ksort($reports);
        return view('reports.list', compact('reports'));
    }

    public function setup($report) {
        $reportClass = 'App\\Reports\\'.ucfirst($report).'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
            return $report->setup();
        } else {
            return redirect()->route('home');
        }
    }

    public function render(Request $request, $report) {
        $reportClass = 'App\\Reports\\'.ucfirst($report).'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
            return $report->render($request);
        } else {
            return redirect()->route('home');
        }
    }
}
