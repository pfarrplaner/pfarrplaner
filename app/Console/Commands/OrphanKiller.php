<?php

namespace App\Console\Commands;

use App\Absence;
use App\Attachment;
use App\Baptism;
use App\Booking;
use App\CalendarConnection;
use App\Funeral;
use App\Location;
use App\Parish;
use App\Participant;
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
