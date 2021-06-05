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

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 24.04.2019
 * Time: 14:32
 */

namespace App\HomeScreens;


use App\Absence;
use App\Replacement;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class AbstractHomeScreen
 * @package App\HomeScreens
 */
abstract class AbstractHomeScreen
{

    /** @var bool $hasConfiguration true, if this view can be configured in user profile settings */
    protected $hasConfiguration = false;


    public function __construct()
    {
    }

    /**
     * Render the homescreen
     * @return mixed
     */
    abstract public function render();


    /**
     * Called to render a specific blade view
     * @param $viewName Name of the view to render
     * @param $data Data passed to the view (absence data will be added)
     * @return Factory|View
     */
    public function renderView($viewName, $data)
    {
        if (Auth::user()->manage_absences) {
            $data = array_merge($data, $this->getAbsences());
        }
        return view($viewName, $data);
    }

    /**
     * Get absence data for view
     * @return array Absence data
     */
    protected function getAbsences()
    {
        $absences = Absence::where('user_id', Auth::user()->id)->where('to', '>=', now())->get();
        $replacements = Replacement::with('absence')
            ->whereHas(
                'users',
                function ($query) {
                    $query->where('users.id', Auth::user()->id);
                }
            )
            ->where('to', '>=', now())
            ->orderBy('from')
            ->orderBy('to')
            ->get();
        return compact('absences', 'replacements');
    }


    /**
     * Get a view for configuration setttings
     * @return string
     */
    public function renderConfigurationView()
    {
        return 'Für diesen Startbildschirm gibt es nichts zu konfigurieren.';
    }


    /**
     * Save submitted configuration
     * @param Request $request Request
     */
    public function setConfiguration(Request $request)
    {
    }

}
