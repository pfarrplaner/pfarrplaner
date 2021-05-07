<?php

namespace App\Console\Commands;

use App\City;
use App\Events\ServiceUpdated;
use App\Integrations\CommuniApp\CommuniAppIntegration;
use App\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PushToCommuniApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communiapp:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push all existing services to communiapp';

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
        foreach (City::all() as $city) {
            if (CommuniAppIntegration::isActive($city)) {
                $this->line('Updating future services for "'.$city->name.'"');
                $communiApp = CommuniAppIntegration::get($city);
                $services = Service::notHidden()
                    ->inCity($city)
                    ->whereDoesntHave('funerals')
                    ->whereDoesntHave('weddings')
                    ->startingFrom(Carbon::now())
                    ->ordered()
                    ->get();
                foreach ($services as $service) {
                    $this->line('Updating service #'.$service->id);
                    $communiApp->handleServiceUpdated($service);
                }
            }
        }
    }
}
