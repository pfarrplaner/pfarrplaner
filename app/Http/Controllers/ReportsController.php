<?php

namespace App\Http\Controllers;

use App\Reports\AbstractReport;
use Illuminate\Http\Request;

class ReportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['embed']]);
    }

    public function list()
    {
        $reports = [];
        foreach (glob(app_path('Reports') . '/*Report.php') as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) !== 'Abstract') {
                $reportClass = 'App\\Reports\\' . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($reportClass)) {
                    $report = new $reportClass();
                    if ($report->isActive()) {
                        $reports[$report->group][$report->title] = $report;
                        ksort($reports[$report->group]);
                    }
                }
            }
        }
        ksort($reports);
        return view('reports.list', compact('reports'));
    }

    public function setup($report)
    {
        $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
            return $report->setup();
        } else {
            return redirect()->route('home');
        }
    }

    public function render(Request $request, $report)
    {
        $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
            return $report->render($request);
        } else {
            return redirect()->route('home');
        }
    }

    public function step (Request $request, $report, $step) {
        $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
            if (method_exists($report, $step)) {
                return $report->$step($request);
            }
        }
        return redirect()->route('home');
    }

    public function embed(Request $request, $report) {
        $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
            if (method_exists($report, 'embed')) {
                return $report->embed($request);
            }
        }
        return abort(404);
    }
}
