<?php

namespace App\Mail;

use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Service $service */
    protected $service;

    /** @var Service $original */
    protected $original;

    /** @var User $user */
    protected $user;

    protected $changes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Service $service, Service $original, User $user)
    {
        $this->service = $service;
        $this->original = $original;
        $this->user = $user;

        $this->changes = $service->getDirty();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        return $this->subject('Änderungen am Gottesdienst vom '.$this->original->day->date->format('d.m.Y').', '.$this->original->timeText().' ('.$this->original->locationText().')')
            ->view('mail.notifications.service-update')->with([
            'service' => $this->service,
            'original' => $this->original,
            'changes' => $this->changes,
            'user' => $this->user,
        ]);
    }
}
