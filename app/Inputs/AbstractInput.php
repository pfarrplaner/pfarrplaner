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

namespace App\Inputs;

use App\City;
use Illuminate\Http\Request;

class AbstractInput
{
    public $title = '';

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
        return 'inputs.input.'.strtolower($this->getKey());
    }

    public function getValues(City $city, $days) {
        return [];
    }

    public function input(Request $request) {}
    public function save(Request $request) {}

}
