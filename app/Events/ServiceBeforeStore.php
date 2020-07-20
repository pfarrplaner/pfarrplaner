<?php

namespace App\Events;

use App\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceBeforeStore
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
     * @param array $data
     */
    public function __construct(Service $service, array $data)
    {
        $this->service = $service;
        $this->data = $data;
    }

}
