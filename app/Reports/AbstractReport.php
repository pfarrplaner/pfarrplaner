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

namespace App\Reports;

use Illuminate\Http\Request;

class AbstractReport
{

    public $icon = 'fa fa-file';
    public $title = '';
    public $group = '';
    public $description = '';


    public function getKey() {
        return lcfirst(strtr(get_called_class(), [
            'Report' => '',
            'App\\Reports\\' => '',
        ]));
    }

    public function getViewName($view) {
        return 'reports.'.strtolower($this->getKey().'.'.strtolower($view));
    }

    public function getSetupViewName() {
        return $this->getViewName('setup');
    }

    public function getRenderViewName() {
        return $this->getViewName('render');
    }

    public function render(Request $request) {
        return '';
    }

    public function renderSetupView($data = []) {
        $data = array_merge(['report' => $this->getKey()], $data);
        return view($this->getSetupViewName(), $data);
    }

    public function setup() {
        return $this->renderSetupView();
    }
}
