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

namespace App\Providers;

use App\QueryLog;
use App\Seating\SeatingValidators;
use App\Services\PackageService;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::withoutComponentTags();
        Blade::aliasComponent('partials.form.tabheader', 'tabheader');
        Blade::aliasComponent('partials.form.tabheaders', 'tabheaders');
        Blade::aliasComponent('partials.form.tabs', 'tabs');
        Blade::aliasComponent('partials.form.tab', 'tab');
        Blade::include('partials.form.input', 'input');
        Blade::include('partials.form.file', 'upload');
        Blade::include('partials.form.datetimepicker', 'datetimepicker');
        Blade::include('partials.form.hidden', 'hidden');
        Blade::include('partials.form.textarea', 'textarea');
        Blade::include('partials.form.select', 'select');
        Blade::include('partials.form.selectize', 'selectize');
        Blade::include('partials.form.dayselect', 'dayselect');
        Blade::include('partials.form.cityselect', 'cityselect');
        Blade::include('partials.form.locationselect', 'locationselect');
        Blade::include('partials.form.peopleselect', 'peopleselect');
        Blade::include('partials.form.checkbox', 'checkbox');
        Blade::include('partials.form.radiogroup', 'radiogroup');
        Blade::include('partials.string.badges', 'badges');


        Validator::extendImplicit(
            'checkbox',
            function ($attribute, $value, $parameters, $validator) {
                $data = $oldData = $validator->getData();
                $data[$attribute] = ($value == "1" || strtolower($value) == "true" || strtolower(
                        $value
                    ) == "on") ? 1 : 0;
                $validator->setData($data);
                return true;
            }
        );

        Validator::extend(
            'phone_number',
            function ($attribute, $value, $parameters) {
                return preg_match('/^([0-9s(+][0-9\.-\/ ()]{7,})$/i', $value);
            }
        );

        Validator::extend(
            'zip',
            function ($attribute, $value, $parameters) {
                return preg_match('/^([0]{1}[1-9]{1}|[1-9]{1}[0-9]{1})[0-9]{3}$/i', $value);
            }
        );

        Validator::extend(
            'hash',
            function ($attribute, $value, $parameters) {
                return Hash::check($value, $parameters[0]);
            }
        );

        Validator::extend(
            'not_hash',
            function ($attribute, $value, $parameters) {
                return !Hash::check($value, $parameters[0]);
            }
        );

        Validator::extend(
            'not_current_password',
            function ($attribute, $value, $parameters) {
                return !Hash::check($value, Auth::user()->password);
            }
        );

        // seating Validators
        SeatingValidators::register();

        QueryLog::register();

        Inertia::setRootView('inertia-app');

        /**
         * Pass array or string of key column names to remove
         */
        Collection::macro('removeCols', function ($except) {
            if (!is_array($except)) $except = (array)$except;

            // Single Dimensional arrays
            if (!is_array($this->first()) && !is_object($this->first())) return $this->except($except);

            // Multi Dimensional arrays
            $out = $this->map(function ($item) use ($except) {
                return Arr::except($item, $except);
            });

            return collect($out);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->bind(
        //'Auth0\Login\Contract\Auth0UserRepository',
        //'Auth0\Login\Repository\Auth0UserRepository');
    }
}
