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

namespace App\Inputs;

use App\City;
use App\Day;
use App\Mail\ServiceUpdated;
use App\Service;
use App\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferingsInput extends AbstractInput
{

    public $title = 'Opferplan';
    protected $setupView = 'inputs.offerings.setup';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-opfer-bearbeiten');
    }

    public function input(Request $request) {
        $request->validate([
            'year' => 'int|required',
            'cities' => 'required',
            'cities.*' => 'int|required|exists:cities,id',
        ]);


        $cityIds = $request->get('cities');
        $cities = City::whereIn('id', $cityIds)->get();
        $year = $request->get('year');

        $services = Service::with('day', 'location')
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', $cityIds)
            ->where('days.date', '>=', $year.'-01-01')
            ->where('days.date', '<=', $year.'-12-31')
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();



        $input = $this;
        return view($this->getInputViewName(), compact('input', 'cities', 'services', 'year'));

    }

    public function save(Request $request) {}


}
