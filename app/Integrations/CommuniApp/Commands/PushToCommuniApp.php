<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Integrations\CommuniApp\Commands;

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
