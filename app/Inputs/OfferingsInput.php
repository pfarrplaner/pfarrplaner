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

namespace App\Inputs;

use App\City;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class OfferingsInput
 * @package App\Inputs
 */
class OfferingsInput extends AbstractInput
{

    /**
     * @var string
     */
    public $title = 'Opferplan';
    /**
     * @var string
     */
    protected $setupView = 'inputs.offerings.setup';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-opfer-bearbeiten');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|void
     */
    public function input(Request $request)
    {
        $request->validate(
            [
                'year' => 'int|required',
                'cities' => 'required',
                'cities.*' => 'int|required|exists:cities,id',
            ]
        );


        $cityIds = $request->get('cities');
        $cities = City::whereIn('id', $cityIds)->get();
        $year = $request->get('year');

        $services = Service::with('day', 'location')
            ->whereYear('date', $year)
            ->whereIn('city_id', $cityIds)
            ->ordered()
            ->get();


        $input = $this;
        return view($this->getInputViewName(), compact('input', 'cities', 'services', 'year'));
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
    }


}
