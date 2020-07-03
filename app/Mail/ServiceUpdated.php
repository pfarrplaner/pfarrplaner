<?php

namespace App\Mail;

use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\View;

class ServiceUpdated extends AbstractServiceMailable
{

    protected $originalRelations;
    protected $originalAttributes;
    protected $originalAppendedAttributes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Service $service, array $data)
    {
        parent::__construct($user, $service, $data);
        $this->original = $service->originalObject;
        $this->originalRelations = $service->originalRelations;
        $this->originalAttributes = $service->originalAttributes;
        $this->originalAppendedAttributes = $service->originalAppendedAttributes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->original = $this->service->restoreOriginal($this->originalAttributes, $this->originalRelations);
        $this->original->originalAppendedAttributes = $this->originalAppendedAttributes;

        $ics = view(
            'ical/ical',
            ['services' => [$this->service], 'token' => null, 'action' => null, 'key' => null]
        )->render();
        $icsTitle = 'GD ' . $this->service->day->date->format('Ymd') . ' ' . $this->service->timeText(
                false
            ) . ' ' . $this->service->locationText() . '.ics';
        return $this->subject('Änderungen an einem Gottesdienst')
            ->view('mail.notifications.service-update')->with(
                [
                    'service' => $this->service,
                    'original' => $this->original,
                    'originalRelations' => $this->originalRelations,
                    'changed' => $this->service,
                    'changes' => $this->changed,
                    'user' => $this->user,
                ]
            )->attachData(
                $ics,
                $icsTitle,
                [
                    'mime' => 'text/calendar',
                ]
            );
    }
}
