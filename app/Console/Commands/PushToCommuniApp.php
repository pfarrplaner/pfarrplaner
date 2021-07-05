<?php

namespace App\Console\Commands;

use App\City;
use App\Events\ServiceUpdated;
use App\Integrations\CommuniApp\CommuniAppIntegration;
use App\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
                    ->notHidden()
                    ->whereDoesntHave('funerals')
                    ->whereDoesntHave('weddings')
                    ->where(function($query) {
                        $query->where(function ($q) {
                            $q->whereNull('communiapp_listing_start')
                                ->startingFrom(Carbon::now())
                                ->endingAt(Carbon::now()->addDays(8));
                        })
                        ->orWhere(function ($q) {
                            $q->whereNotNull('communiapp_listing_start')
                                ->startingFrom(Carbon::now())
                                ->where('communiapp_listing_start', '<=', Carbon::now());
                        });
                    })
                    ->ordered()
                    ->get();
                foreach ($services as $service) {
                    $this->line('Updating service #'.$service->id.' ('.$service->dateTime->setTimeZone('Europe/Berlin')->format('d.m.Y H:i').')');
                    $communiApp->handleServiceUpdated($service);
                }
            }
        }
    }

    public function line($string, $style = null, $verbosity = null)
    {
        Log::debug ($string);
        parent::line($string, $style, $verbosity);
    }


}
