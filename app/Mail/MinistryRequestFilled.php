<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class MinistryRequestFilled extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User $user */
    protected $user;
    /** @var User $sender */
    protected $sender;

    protected $services;
    protected $ministry;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $sender, $ministry, $services)
    {
        $this->user = $user;
        $this->sender = $sender;
        $this->ministry = $ministry;
        $this->services = $services;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Zusage: ' . $this->ministry . ' im Gottesdienst')->markdown('mail.ministry.filled')->with(
            [
                'user' => $this->user,
                'sender' => $this->sender,
                'ministry' => $this->ministry,
                'services' => $this->services,
            ]
        );
    }}
