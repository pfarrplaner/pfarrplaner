<?php
/*
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

namespace App\Http\Middleware;

use App\Facades\Settings;
use App\Http\Resources\UserResource;
use App\Services\PackageService;
use App\UI\MenuBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $packageConfig = json_decode(file_get_contents(base_path('package.json')), true);
        $version = $packageConfig['version'];

        $data = array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'dev' => config('app.dev') ? true:  false,
            'demo' => config('demo_mode') ? true:  false,
            'errors' => fn() => Session::get('errors')
                ? Session::get('errors')->getBag('default')->getMessages()
                : (object)[],
            'package' =>  fn() => PackageService::info(),
            'route' => fn() => Route::currentRouteName(),
            'currentRoute' => fn() => Route::currentRouteName(),
            'version' => $version,
            'activeTab' => request()->get('tab', 'home'),
        ]);

        if (!Auth::guest()) {
            $data = array_merge($data, [
                'currentUser' => fn () => $request->user()
                    ? new UserResource($request->user())
                    : null,
                'menu' => fn() => MenuBuilder::sidebar(),
                'settings' => fn() => Settings::all(Auth::user()),
            ]);
        }

        return $data;
    }
}
