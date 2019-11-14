<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::component('partials.form.input', 'input');
        Blade::component('partials.form.hidden', 'hidden');
        Blade::component('partials.form.dayselect', 'dayselect');
        Blade::component('partials.form.locationselect', 'locationselect');
        Blade::component('partials.form.peopleselect', 'peopleselect');
        Blade::component('partials.form.checkbox', 'checkbox');
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
