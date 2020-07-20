<?php

namespace App\Events;

use App\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Service */
    public $service;

    /**
     * Create a new event instance.
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

}
