<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\Adapter\AbstractAdapter;
use niklasravnsborg\LaravelPdf\Pdf;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Tab;


class PersonReport extends AbstractPDFDocumentReport
{

    public $title = 'Alle Gottesdienste einer Person';
    public $group = 'Listen';
    public $description = 'Liste mit allen Gottesdiensten, für die eine bestimmte Person eingeteilt ist';

    public function setup() {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $users = User::all();
        return $this->renderSetupView(compact('maxDate', 'users'));
    }

    public function render(Request $request)
    {
        $request->validate([
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $userIds = $request->get('person');
        $userIds = is_array($userIds) ? $userIds : [$userIds];
        $users = User::whereIn('id', $userIds)->get();

        $services = Service::with(['location'])
            ->select(['services.*','days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereHas('participants', function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            })->whereHas('day', function ($query) use ($request) {
                $query->where('date', '>=', Carbon::createFromFormat('d.m.Y', $request->get('start')));
                $query->where('date', '<=', Carbon::createFromFormat('d.m.Y', $request->get('end')));
            })->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        return $this->sendToBrowser(
            date('Ymd') . ' Gottesdienstliste ' . $request->get('highlight') . '.pdf',
            [
                'start' => $request->get('start'),
                'end' => $request->get('end'),
                'highlight' => $users,
                'services' => $services,
            ],
            ['format' => 'A4']);

    }

}
