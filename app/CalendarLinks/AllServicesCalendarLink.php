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
 * Time: 13:45
 */

namespace App\CalendarLinks;


use App\Service;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AllServicesCalendarLink
 * @package App\CalendarLinks
 */
class AllServicesCalendarLink extends AbstractCalendarLink
{

    /**
     * @var string
     */
    protected $title = 'Gottesdienste nach Kirchengemeinden';
    /**
     * @var string
     */
    protected $description = 'Kalender, der alle Gottesdienste ausgewählter Kirchengemeinden enthält';

    /**
     * @return array
     */
    public function setupData()
    {
        $cities = Auth::user()->cities;
        return compact('cities');
    }

    /**
     * @param Request $request
     */
    public function setDataFromRequest(Request $request)
    {
        $this->data['cities'] = join('-', $request->get('includeCities') ?: []);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array|Builder[]|Collection
     */
    public function getRenderData(Request $request, User $user)
    {
        $cityIds = explode('-', $request->get('cities', ''));
        $serviceQuery = Service::with(['day', 'location'])
            ->whereIn('city_id', $cityIds);
        if (!$request->get('includeHidden', 0)) $serviceQuery->notHidden();
        return $serviceQuery->get();
    }


}
