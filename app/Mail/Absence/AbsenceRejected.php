<?php

namespace App\Mail\Absence;

use App\Absence;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AbsenceRejected extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Absence $absence  */
    protected $absence = null;

    /** @var User $author */
    protected $author = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Absence $absence, User $author)
    {
        $this->absence = $absence;
        $this->author = $author;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug('AbsenceRejected mailing: build');
        return $this->subject('Urlaubsantrag abgelehnt')
            ->markdown('absences.mail.rejected', ['absence' => $this->absence, 'author' => $this->author]);
    }
}
