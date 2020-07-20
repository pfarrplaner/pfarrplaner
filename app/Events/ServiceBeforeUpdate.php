<?php

namespace App\Events;

use App\Service;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ServiceBeforeUpdate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Service */
    public $service;

    /** @var array */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param Service $service
     * @param array data
     * @return void
     */
    public function __construct(Service $service, array $data)
    {
        Log::debug('ServiceBeforeUpdate called on service #'.$service->id);
        $this->service = $service;
        $this->data = $data;
    }

}
