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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AbstractInput
 * @package App\Inputs
 */
class AbstractInput
{
    /**
     * @var string
     */
    public $title = '';
    /**
     * @var string
     */
    protected $setupView = 'inputs.setup';

    public function canEdit(): bool {
        return true;
    }

    /**
     * @return string
     */
    public function getKey() {
        return lcfirst(strtr(get_called_class(), [
            'Input' => '',
            'App\\Inputs\\' => '',
        ]));
    }

    /**
     * @return string
     */
    public function getInputViewName() {
        return $this->getViewName('input');
    }

    /**
     * @param $viewName
     * @return string
     */
    public function getViewName($viewName) {
        return 'inputs.'.strtolower($this->getKey()).'.'.$viewName;
    }

    /**
     * @param City $city
     * @param $days
     * @return array
     */
    public function getValues(City $city, $days) {
        return [];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setup(Request $request) {
        $minDate = Day::orderBy('date', 'ASC')->limit(1)->get()->first();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->writableCities;

        return view($this->setupView, [
            'input' => $this,
            'minDate' => $minDate,
            'maxDate' => $maxDate,
            'cities' => $cities,
        ]);
    }

    /**
     * @param Request $request
     */
    public function input(Request $request) {}

    /**
     * @param Request $request
     */
    public function save(Request $request) {}

}
