<?php

namespace App\Events;

use App\Absence;
use App\Approval;
use Illuminate\Queue\SerializesModels;

class AbsenceRejected {

    use SerializesModels;

    /** @var Absence */
    public $absence;

    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }
}