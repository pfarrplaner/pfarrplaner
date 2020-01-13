<?php

namespace App\Mail;

use App\Absence;
use App\Approval;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AbsenceApproved extends Mailable
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
        Log::debug('AbsenceApproved mailing: build');
        $absence = $this->absence;
        $approval = $this->approval;
        return $this
            ->subject('Neue Genehmigung für Urlaubsantrag')
            ->view('mail.notifications.absence-approved')
            ->with(compact('absence', 'approval'));
    }
}
