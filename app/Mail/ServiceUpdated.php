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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Service $service, array $data)
    {
        parent::__construct($user, $service, $data);
        $this->changed = $service->changed;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $participants = [];
        if (isset($this->changed['participants'])) {
            foreach (['original', 'changed'] as $type) {
                foreach ($this->changed['participants'][$type] as $participant) {
                    $category = $participant['pivot']['category'];
                    switch ($category) {
                        case 'P': $category = 'Pfarrer*in'; break;
                        case 'O': $category = 'Organist*in'; break;
                        case 'M': $category = 'Mesner*in'; break;
                        case 'A': $category = 'Andere Beteiligte'; break;
                    }
                    $participants[$category][$type][] = $participant;
                }
            }
        }
        $tmpParticipants = $participants;
        foreach ($tmpParticipants as $category => $p) {
            if (print_r($p['original'] ?? [], 1) == print_r($p['changed'] ?? [], 1)) unset($participants[$category]);
        }

        $ics = view(
            'ical/ical',
            ['services' => [$this->service], 'token' => null, 'action' => null, 'key' => null]
        )->render();
        $icsTitle = 'GD ' . $this->service->day->date->format('Ymd') . ' ' . $this->service->timeText(
                false
            ) . ' ' . $this->service->locationText() . '.ics';
        return $this->subject('Änderungen am einem Gottesdienst vom '.$this->service->dateText().', '.$this->service->timeText().' ('.$this->service->locationText().')')
            ->view('mail.notifications.service-update')->with(
                [
                    'service' => $this->service,
                    'changes' => $this->changed,
                    'participants' => $participants,
                    'user' => $this->user,
                ])->attachData(
                $ics,
                $icsTitle,
                [
                    'mime' => 'text/calendar',
                ]
            );
    }
}
