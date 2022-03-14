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

namespace App\Http\Controllers;

use App\Reports\AbstractReport;
use App\Services\ColorService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;

/**
 * Class ReportsController
 * @package App\Http\Controllers
 */
class ReportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['embed']]);
    }

    /**
     * @return \Inertia\Response
     */
    public function list()
    {
        $reports = collect();
        foreach (glob(app_path('Reports') . '/*Report.php') as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) !== 'Abstract') {
                $reportClass = 'App\\Reports\\' . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($reportClass)) {
                    /** @var AbstractReport $report */
                    $report = new $reportClass();
                    if ($report->isActive()) {
                        $reports->push([
                            'title' => $report->title,
                            'key' => $report->getKey(),
                            'icon' => $report->icon,
                            'description' => $report->description,
                            'group' => $report->group,
                            'inertia' => $report->isInertia(),
                        ]);
                    }
                }
            }
        }
        $rainbow = ColorService::rainbowArray($reports->pluck('group')->unique());

        $reports = $reports
            ->map(function ($item) use ($rainbow) {
                $item['backgroundColor'] = $rainbow[$item['group']];
                $item['color'] = ColorService::contrastColor($item['backgroundColor']);
                return $item;
            })
            ->sortBy(['group','title']);
        return Inertia::render('Reports/Index', compact('reports'));
    }

    /**
     * @param $report
     * @return Application|Factory|RedirectResponse|View
     */
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

    /**
     * @param Request $request
     * @param $report
     * @return RedirectResponse|string
     */
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

    /**
     * @param Request $request
     * @param $report
     * @param $step
     * @return RedirectResponse
     */
    public function step(Request $request, $report, $step)
    {
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

    /**
     * @param Request $request
     * @param $report
     */
    public function embed(Request $request, $report)
    {
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
