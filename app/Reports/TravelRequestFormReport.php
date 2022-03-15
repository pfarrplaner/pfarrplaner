<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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


use App\Absence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Mpdf\Mpdf;

class TravelRequestFormReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Dienstreiseantrag';
    /**
     * @var string
     */
    public $group = 'Formulare';
    /**
     * @var string
     */
    public $description = 'Dienstreiseantrag für das Dekanatamt';

    protected $inertia = true;


    /**
     * Returns true if the report is active for the current user
     * @return bool
     */
    public function isActive(): bool
    {
        return Auth::user()->hasRole('Pfarrer*in');
    }

    public function setup()
    {
        $absences = Absence::where('user_id', Auth::user()->id)
            ->where('from', '>', Carbon::now())
            ->orderBy('from')
            ->get();

        return Inertia::render('Report/TravelRequest/Setup', compact('absences'));
    }

    public function render(Request $request)
    {
        $data = $request->validate(['absence' => 'int|exists:absences,id']);
        $data['absence'] = Absence::find($data['absence']);

        $data['duration'] = $data['absence']->to->diff($data['absence']->from)->days+1;

        $packageConfig = json_decode(file_get_contents(base_path('package.json')), true);
        $data['version'] = $packageConfig['version'];


        $fileName = $data['absence']->from->format('Ymd')
            . ($data['absence']->to != $data['absence']->from ? '-' . $data['absence']->to->format('Ymd') : '')
            . ' Dienstreiseantrag ' . Auth::user()->name . '.pdf';

        $config = [
            'instanceConfigurator' => function ($mpdf) {
                /** @var $mpdf Mpdf */
                $mpdf->useActiveForms = true;
                $mpdf->shrink_tables_to_fit = 0;
            },
            'format' => 'A4',
            'shrink_tables_to_fit' => 0,
        ];

        $pdf = $this->renderPDF($data, $config);
        return $pdf->download($fileName);

    }


}
