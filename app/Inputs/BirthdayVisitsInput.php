<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Inputs;

use App\Services\BirthdayVisitsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BirthdayVisitsInput extends AbstractInput
{

    /**
     * @var string
     */
    public $title = 'Geburtstagsbesuche';
    public $description = 'Geburtstagsbesuche in den Kalender übertragen';

    public function canEdit(): bool
    {
        return Auth::user()->calendarConnections->count() > 0;
    }

    /**
     * @return \Inertia\Response
     */
    public function setup(Request $request)
    {
        $calendarConnections = Auth::user()->calendarConnections;
        $setup = Auth::user()->getSetting('birthdayVisits_setup', []);
        return Inertia::render('Inputs/BirthdayVisits/Setup', compact('calendarConnections', 'setup'));
    }

    /**
     * @return \Inertia\Response
     */
    public function save(Request $request)
    {
        $setup = $request->get('setup') ?? [];
        $setup['permitted'] = json_decode($setup['permitted'], true);
        $events = [];
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments');
            $events = (new BirthdayVisitsService(storage_path('app/'.$path), $setup))->exportBirthdaysToCalendar();
            unlink(storage_path('app/'.$path));
        }

        // save settings
        unset($setup['password']);
        Auth::user()->setSetting('birthdayVisits_setup', $setup);

        return Inertia::render('Inputs/BirthdayVisits/Results', compact('events'));
    }


}
