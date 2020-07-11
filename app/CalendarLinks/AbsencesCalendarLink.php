<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 29.10.2019
 * Time: 14:19
 */

namespace App\CalendarLinks;


use App\Absence;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AbsencesCalendarLink
 * @package App\CalendarLinks
 */
class AbsencesCalendarLink extends AbstractCalendarLink
{

    /**
     * @var string
     */
    protected $title = 'Urlaubskalender';
    /**
     * @var string
     */
    protected $description = 'Kalender mit allen Urlaubseinträgen, die du sehen kannst';
    /**
     * @var string
     */
    protected $viewName = 'absences';

    /**
     * @return string
     */
    public function setupRoute()
    {
        return route('ical.link', ['key' => $this->getKey()]);
    }

    /**
     * @param Request $request
     */
    public function setDataFromRequest(Request $request)
    {
        if (null !== Auth::user()) {
            $this->data['user'] = Auth::user()->id;
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function getRenderData(Request $request, User $user)
    {
        $users = $user->getViewableAbsenceUsers();
        $userId = $user->id;
        $data = Absence::whereIn('user_id', $users->pluck('id'))
            ->orWhere(
                function ($query2) use ($userId) {
                    $query2->whereHas(
                        'replacements',
                        function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        }
                    );
                }
            )->get();

        return $data;
    }


}
