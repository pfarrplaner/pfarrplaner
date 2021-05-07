<?php

namespace App\Console\Commands;

use App\City;
use App\Integrations\CommuniApp\CommuniAppIntegration;
use App\Service;
use Illuminate\Console\Command;

class KillCommuniApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communiapp:kill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $services = Service::whereNotNull('communiapp_id')->get();
        $ca = CommuniAppIntegration::get(City::find(1));
        foreach ($services as $service) {
            $this->line('Trying to delete service #'.$service->id.' (CA #'.$service->communiapp_id.')');
            $ca->handleserviceDeleted($service);
        }
    }
}
