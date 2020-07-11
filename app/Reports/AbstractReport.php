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

namespace App\Reports;

use Illuminate\Http\Request;

/**
 * Class AbstractReport
 * @package App\Reports
 */
class AbstractReport
{

    /**
     * @var string
     */
    public $icon = 'fa fa-file';
    /**
     * @var string
     */
    public $title = '';
    /**
     * @var string
     */
    public $group = '';
    /**
     * @var string
     */
    public $description = '';


    /**
     * Returns true if the report is active for the current user
     * @return bool
     */
    public function isActive(): bool {
        return true;
    }

    /**
     * @return string
     */
    public function getKey() {
        return lcfirst(strtr(get_called_class(), [
            'Report' => '',
            'App\\Reports\\' => '',
        ]));
    }

    /**
     * @param $view
     * @return string
     */
    public function getViewName($view) {
        return 'reports.'.strtolower($this->getKey().'.'.strtolower($view));
    }

    /**
     * @return string
     */
    public function getSetupViewName() {
        return $this->getViewName('setup');
    }

    /**
     * @return string
     */
    public function getRenderViewName() {
        return $this->getViewName('render');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function render(Request $request) {
        return '';
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderSetupView($data = []) {
        $data = array_merge(['report' => $this->getKey()], $data);
        return view($this->getSetupViewName(), $data);
    }

    /**
     * @param $view
     * @param array $data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderView($view, $data = []) {
        $data = array_merge(['report' => $this->getKey()], $data);
        return view($this->getViewName($view), $data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setup() {
        return $this->renderView('setup');
    }
}
