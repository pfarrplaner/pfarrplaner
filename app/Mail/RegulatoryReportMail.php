<?php

namespace App\Mail;

use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class RegulatoryReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data = [];
    protected $user = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $user = null)
    {
        $this->data = $data;
        $this->user = $user ?? Auth::user();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $service = Service::find($this->data['service']);
        $sender = 'Evangelische Kirchengemeinde '.$service->city->name.'';
        $text = strtr(
            $this->data['template'],
            [
                '###GOTTESDIENST###' => join(', ',
                                             [
                                                 $service->day->date->format('d.m.Y'),
                                                 $service->timeText(),
                                                 $service->city->name,
                                                 $service->locationText(),
                                             ]
                    ).($service->titleText() != 'GD' ? ', '.$service->titleText() : ''),
                '###TEILNEHMERZAHL###' => $this->data['number'],
                '###KIRCHENGEMEINDE###' => $sender,
            ]
        );

        $user = $this->user;

        return $this->from('noreply@pfarrplaner.de', $sender)
            ->replyTo($user)
            ->subject('Gottesdienstmeldung nach §1g Abs. 3 CoronaVO')
            ->markdown('reports.regulatory.mail', compact('text', 'user', 'sender'));
    }
}
