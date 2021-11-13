<?php

namespace App\Events;

use App\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ServiceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Service */
    public $service;

    /** @var Collection */
    public $originalParticipants;

    /**
     * Create a new event instance.
     *
     * @param Service $service
     * @param Collection $originalParticipants
     */
    public function __construct(Service $service, Collection $originalParticipants)
    {
        Log::debug('ServiceUpdated called on service #'.$service->id);
        $this->service = $service;
        $this->originalParticipants = $originalParticipants;

        // update youtube if necessary
        if (($service->youtube_url != '') && ($service->city->google_access_token != '')) {
            Broadcast::get($service)->update();
        }

    }

}
