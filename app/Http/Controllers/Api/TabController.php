<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Http\Controllers\Api;

use App\HomeScreen\Tabs\HomeScreenTabFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TabController extends \App\Http\Controllers\Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Retrieve data for a single tab
     * @param $tab Tab key
     * @return \Illuminate\Http\JsonResponse
     */
    public function tab($tab)
    {
        $config = Auth::user()->getSetting('homeScreenTabsConfig') ?? [];
        $tabIndex = filter_var($tab, FILTER_SANITIZE_NUMBER_INT);
        $tab = HomeScreenTabFactory::getOne($config['tabs'][$tabIndex], $tabIndex);
        return response()->json($tab);
    }

    /**
     * Retrieve item count for a single tab
     * @param $tab Tab key
     * @return \Illuminate\Http\JsonResponse
     */
    public function tabCount($tab)
    {
        $config = Auth::user()->getSetting('homeScreenTabsConfig') ?? [];
        $tabIndex = filter_var($tab, FILTER_SANITIZE_NUMBER_INT);
        return response()->json(['key' => $tab, 'count' => HomeScreenTabFactory::getCount($config['tabs'][$tabIndex], $tabIndex)]);
    }

}
