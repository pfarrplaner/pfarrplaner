<?php

namespace App\Events;

use App\Absence;
use App\Approval;
use Illuminate\Queue\SerializesModels;

class AbsenceApproved {

    use SerializesModels;

    /** @var Absence */
    public $absence;

    /** @var Approval */
    public $approval;

    public function __construct(Absence $absence, Approval $approval)
    {
        $this->absence = $absence;
        $this->approval = $approval;
    }
}