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

namespace App\Console\Commands\Registrations;

use App\Booking;
use App\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registrations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete registrations from services older than 5 weeks';

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
        $now = Carbon::now();
        $services = Service::with('day')->whereHas('bookings')->get();
        /** @var Service $service */
        foreach ($services as $service) {
            $expiryDate = $service->day->date->copy()->addWeeks(5);
            if ($expiryDate < $now) {
                $bookingCount = count($service->bookings);
                foreach ($service->bookings->pluck('id') as $bookingId) Booking::find($bookingId)->delete();
                $this->line('Service #'.$service->id.' ('.$service->day->date->format('d.m.Y').' '.$service->timeText(false).') has '
                            .$bookingCount.' bookings which expired on '.$expiryDate->format('d.m.Y').' --> deleted');
            } else {
                $this->line('Service #'.$service->id.' ('.$service->day->date->format('d.m.Y').' '.$service->timeText(false).') has '
                            .count($service->bookings).' bookings which will expire on '.$expiryDate->format('d.m.Y').' --> keeping');

            }
        }
    }
}
