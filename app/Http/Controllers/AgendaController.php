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

namespace App\Http\Controllers;


use App\Day;
use App\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgendaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $agendas = Service::whereHas(
            'day',
            function ($query) {
                $query->where('date', '1978-03-05');
            }
        )->orderBy('title')->get();
        return Inertia::render('agendas', compact('agendas'));
    }

    public function create()
    {
        $agenda = new Service(
            [
                'day_id' => Day::getAgendaDay()->id,
                'title' => 'Neue Agende',
                'description' => 'Gib hier die Beschreibung der neuen Agende ein.',
                'special_location' => 'code_fuer_neue_agende',
            ]
        );
        return response()->json($agenda);
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['day_id'] = Day::getAgendaDay()->id;
        $agenda = Service::create($data);
        $agenda->update(['slug' => $agenda->createSlug()]);
        return redirect()->route('liturgy.editor', $agenda->slug);
    }

    public function update(Request $request, Service $agenda)
    {
        $data = $this->validateRequest($request);
        $data['day_id'] = Day::getAgendaDay()->id;
        $agenda->update($data);
        return redirect()->route('liturgy.editor', $agenda->slug);
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate(
            [
                'title' => 'required|string',
                'description' => 'nullable|string',
                'special_location' => 'nullable|string',
                'internal_remarks' => 'nullable|string',
            ]
        );
    }
}
