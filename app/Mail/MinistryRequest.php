<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class MinistryRequest extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User $user */
    protected $user;
    /** @var User $sender */
    protected $sender;

    protected $services;
    protected $ministry;
    protected $text = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $sender, $ministry, $services, $text)
    {
        $this->user = $user;
        $this->sender = $sender;
        $this->ministry = $ministry;
        $this->services = $services;
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $urlData = [
            'ministry' => $this->ministry,
            'user' => (string)$this->user->id,
            'services' => $this->services->pluck('id')->join(','),
            'sender' => $this->sender->id,
        ];
        $url = URL::signedRoute('ministry.request', $urlData);


        return $this->subject('Anfrage: ' . $this->ministry . ' im Gottesdienst')->markdown('mail.ministry.request')->with(
            [
                'user' => $this->user,
                'sender' => $this->sender,
                'ministry' => $this->ministry,
                'services' => $this->services,
                'url' => $url,
                'text' => $this->text,
            ]
        );
    }
}
