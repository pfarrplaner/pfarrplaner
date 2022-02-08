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
 * Date: 06.12.2019
 * Time: 14:02
 */

namespace App\UI;


use App\City;
use App\Inputs\AbstractInput;
use App\Inputs\Inputs;
use App\Location;
use App\Parish;
use App\Tag;
use App\Team;
use App\UI\Modules\Modules;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

/**
 * Class MenuBuilder
 * @package App\UI
 */
class MenuBuilder
{

    /**
     * Build the sidebar configuration
     * @return array
     */
    public static function sidebar()
    {
        return self::configure();
    }


    /**
     * Build the menu configuration from module config
     * @return array
     */
    protected static function configure(): array
    {
        $request = request();
        $route = Route::currentRouteName();

        $modulesGroups = config('modules.groups');
        $modulesConfig = Auth::user()->getSetting('modules', Modules::defaultConfig());

        $menu = [];
        foreach ($modulesGroups as $groupTitle => $modulesGroup) {
            $items = [];
            foreach ($modulesGroup as $moduleClass) {
                $module = new $moduleClass($modulesConfig);
                if ($module->isActive()) $items = $module->addItems($items);
            }
            if (count($items)) {
                if ($groupTitle != 'default') $menu[] = $groupTitle;
                foreach ($items as $item) $menu[] = $item;
            }
        }
        return $menu;
    }
}
