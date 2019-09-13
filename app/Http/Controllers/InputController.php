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

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Inputs\AbstractInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function getInputClass($input): AbstractInput
    {
        $inputClass = 'App\\Inputs\\' . ucfirst($input) . 'Input';
        if (class_exists($inputClass)) {
            $input = new $inputClass();
            if (!$input->canEdit()) {
                return redirect()->back();
            }
            return $input;
        } else {
            return redirect()->back();
        }
    }

    public function setup($input)
    {
        $input = $this->getInputClass($input);
        $minDate = Day::orderBy('date', 'ASC')->limit(1)->get()->first();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->writableCities;

        return view('inputs.setup', [
            'input' => $input,
            'minDate' => $minDate,
            'maxDate' => $maxDate,
            'cities' => $cities,
        ]);
    }

    public function input(Request $request, $input) {
        $input = $this->getInputClass($input);
        return $input->input($request);
    }

    public function save(Request $request, $input) {
        $input = $this->getInputClass($input);
        return $input->save($request);
    }

}
