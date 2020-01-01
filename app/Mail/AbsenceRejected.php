<?php

namespace App\Mail;

use App\Absence;
use App\Approval;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AbsenceRejected extends Mailable
{
    use Queueable, SerializesModels;


    /** @var Absence */
    protected $absence;

    /** @var Approval */
    protected $approval;




    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Absence $absence, Approval $approval)
    {
        $this->absence = $absence;
        $this->approval = $approval;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $absence = $this->absence;
        $approval = $this->approval;
        return $this
            ->subject('Urlaubsantrag abgelehnt')
            ->view('mail.notifications.absence-rejected')
            ->with(compact('absence'));
    }
}
