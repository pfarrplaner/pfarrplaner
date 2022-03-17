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

namespace App\Reports;

use App\Day;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;


/**
 * Class PersonReport
 * @package App\Reports
 */
class PersonReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Alle Gottesdienste einer Person';
    /**
     * @var string
     */
    public $group = 'Listen';
    /**
     * @var string
     */
    public $description = 'Liste mit allen Gottesdiensten, für die eine bestimmte Person eingeteilt ist';

    protected $inertia = true;

    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $users = User::all();
        return Inertia::render('Report/Person/Setup', compact( 'users'));
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'person' => 'required|int|exists:users,id',
                'start' => 'required|date',
                'end' => 'required|date',
            ]
        );

        $userIds = is_array($data['person']) ? $data['person'] : [$data['person']];
        $users = User::whereIn('id', $userIds)->get();

        $services = Service::with(['location'])
            ->between(Carbon::parse($data['start']), Carbon::parse($data['end']))
            ->whereHas(
                'participants',
                function ($query) use ($userIds) {
                    $query->whereIn('user_id', $userIds);
                }
            )->ordered()
            ->get();

        return $this->sendToBrowser(
            date('Ymd') . ' Gottesdienstliste ' . $request->get('highlight') . '.pdf',
            [
                'start' => $request->get('start'),
                'end' => $request->get('end'),
                'highlight' => $users,
                'services' => $services,
            ],
            ['format' => 'A4']
        );
    }

}
