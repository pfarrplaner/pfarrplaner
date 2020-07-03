<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('partials.form.tabheader', 'tabheader');
        Blade::component('partials.form.tabheaders', 'tabheaders');
        Blade::component('partials.form.tabs', 'tabs');
        Blade::component('partials.form.tab', 'tab');
        Blade::include('partials.form.input', 'input');
        Blade::include('partials.form.file', 'upload');
        Blade::include('partials.form.datetimepicker', 'datetimepicker');
        Blade::include('partials.form.hidden', 'hidden');
        Blade::include('partials.form.textarea', 'textarea');
        Blade::include('partials.form.select', 'select');
        Blade::include('partials.form.selectize', 'selectize');
        Blade::include('partials.form.dayselect', 'dayselect');
        Blade::include('partials.form.locationselect', 'locationselect');
        Blade::include('partials.form.peopleselect', 'peopleselect');
        Blade::include('partials.form.checkbox', 'checkbox');
        Blade::include('partials.form.radiogroup', 'radiogroup');
        Blade::include('partials.string.badges', 'badges');


        Validator::extendImplicit('checkbox', function($attribute, $value, $parameters, $validator)
        {
            $data = $oldData = $validator->getData();
            $data[$attribute] = ($value == "1" || strtolower($value) == "true" || strtolower($value) == "on") ? 1: 0;
            $validator->setData($data);
            return true;
        });

        Validator::extend('phone_number', function($attribute, $value, $parameters)
        {
            return preg_match('/^([0-9s(+][0-9\.-\/ ()]{7,})$/i', $value);
        });

        Validator::extend('zip', function($attribute, $value, $parameters)
        {
            return preg_match('/^([0]{1}[1-9]{1}|[1-9]{1}[0-9]{1})[0-9]{3}$/i', $value);
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
