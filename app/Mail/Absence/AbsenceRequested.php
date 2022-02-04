<?php

namespace App\Mail\Absence;

use App\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AbsenceRequested extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Absence $absence  */
    protected $absence = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug('AbsenceRequested mailing: build');
        return $this->subject('Abwesenheitsantrag: Bitte um Prüfung')
            ->markdown('absences.mail.requested', ['absence' => $this->absence]);
    }
}
