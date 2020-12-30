<?php

namespace App\Console\Commands;

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
