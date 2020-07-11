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

class AbstractInput
{
    public $title = '';
    protected $setupView = 'inputs.setup';

    public function canEdit(): bool {
        return true;
    }

    public function getKey() {
        return lcfirst(strtr(get_called_class(), [
            'Input' => '',
            'App\\Inputs\\' => '',
        ]));
    }

    public function getInputViewName() {
        return $this->getViewName('input');
    }

    public function getViewName($viewName) {
        return 'inputs.'.strtolower($this->getKey()).'.'.$viewName;
    }

    public function getValues(City $city, $days) {
        return [];
    }

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

    public function input(Request $request) {}
    public function save(Request $request) {}

}
