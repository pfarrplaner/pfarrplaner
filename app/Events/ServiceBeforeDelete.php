<?php

namespace App\Events;

use App\Service;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ServiceBeforeDelete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Service */
    public $service;

    /**
     * Create a new event instance.
     *
     * @param Service $service
     * @return void
     */
    public function __construct(Service $service)
    {
        Log::debug('ServiceDeleted called on service #'.$service->id);
        $this->service = $service;
    }

}
