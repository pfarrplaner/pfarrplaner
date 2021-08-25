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

namespace App\Console\Commands\DevBuilder;

use App\Absence;
use App\Baptism;
use App\Booking;
use App\CalendarConnection;
use App\Funeral;
use App\Location;
use App\Parish;
use App\Replacement;
use App\SeatingRow;
use App\SeatingSection;
use App\Service;
use App\StreetRange;
use App\Subscription;
use App\UserSetting;
use App\Wedding;
use Illuminate\Console\Command;

class OrphanKiller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orphans:kill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orphaned records';
    protected $orphans = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function findOrphans($class, $relatedClass) {
        $missingIds = $class::whereDoesntHave($relatedClass)->get()->pluck('id');
        foreach ($missingIds as $missingId) {
            $this->line($class.' #'.$missingId.' doesn\'t have a parent record ('.$relatedClass.')' );
            $class::find($missingId)->delete();
            $this->orphans++;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orphans = 0;

        // Absence
        $this->findOrphans(Absence::class, 'user');

        // Baptism/Funeral/Wedding
        $this->findOrphans(Baptism::class, 'service');
        $this->findOrphans(Funeral::class, 'service');
        $this->findOrphans(Wedding::class, 'service');

        // Booking
        $this->findOrphans(Booking::class, 'service');

        // CalendarConnection
        $this->findOrphans(CalendarConnection::class, 'user');

        // Location
        $this->findOrphans(Location::class, 'city');

        // Parish
        $this->findOrphans(Parish::class, 'owningCity');

        // Participant
        //$this->findOrphans(Participant::class, 'service');

        // Replacement
        $this->findOrphans(Replacement::class, 'absence');

        // SeatingRow
        $this->findOrphans(SeatingRow::class, 'seatingSection');

        // SeatingSection
        $this->findOrphans(SeatingSection::class, 'location');

        // Service
        $this->findOrphans(Service::class, 'day');

        // StreetRange
        $this->findOrphans(StreetRange::class, 'parish');

        // Subscription
        $this->findOrphans(Subscription::class, 'city');
        $this->findOrphans(Subscription::class, 'user');

        // UserSetting
        $this->findOrphans(UserSetting::class, 'user');


        // morphables: attachment, comment

        $this->line($this->orphans.' orphaned records found and deleted.');
    }
}
