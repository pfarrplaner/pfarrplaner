<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class BuildManualPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the online manual';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // create empty pages for every get route not yet covered
        $empty = base_path('manual/notfound.md');
        $routes = Route::getRoutes()->getRoutesByMethod();
        $routeKeys = [];
        ksort($routes);
        /** @var \Illuminate\Routing\Route $route */
        foreach ($routes['GET'] as $route) {
            $routeKey = $route->getAction('as');
            if ($routeKey) {
                $targetFile = base_path('manual/'.$routeKey.'.md');
                if (!file_exists(base_path('manual/'.$routeKey.'.md'))) {
                    $this->line('Creating new manual page '.$targetFile);
                    copy($empty, $targetFile);
                }
            }
        }

    }
}
