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

use App\City;
use App\Day;
use App\Ministry;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MinistrySignupSheetReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Leerer Dienstplan';
    /**
     * @var string
     */
    public $group = 'Listen';
    /**
     * @var string
     */
    public $description = 'Dienstplan für bestimmte Dienste zum Eintragen im Umlaufverfahren';

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $cities = Auth::user()->writableCities;
        $ministries = Ministry::all();
        $ministries->push('Pfarrer*in', 'Organist*in', 'Mesner*in');
        $ministries = $ministries->sort();
        return $this->renderSetupView(compact('cities', 'ministries'));
    }

    public function render(Request $request)
    {
        $data = $request->validate([
                                       'city' => 'required|int|exists:cities,id',
                                       'ministries.*' => 'string',
                                       'start' => 'required|date_format:d.m.Y',
                                       'end' => 'required|date_format:d.m.Y',
                                   ]);
        $data['city'] = City::find($data['city']);
        $data['start'] = Carbon::createFromFormat('d.m.Y H:i:s', $data['start'] . ' 0:00:00');
        $data['end'] = Carbon::createFromFormat('d.m.Y H:i:s', $data['end'] . ' 23:59:00');
        $data['services'] = Service::between($data['start'], $data['end'])->inCity($data['city'])->ordered()->get();

        return $this->sendToBrowser(
            date('Ymd') . ' Leerer Dienstplan '.join(', ', $data['ministries'])  . '.pdf',
            $data,
            ['format' => 'A4-L']
        );
    }


}
