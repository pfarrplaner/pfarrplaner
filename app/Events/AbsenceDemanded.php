<?php

namespace App\Events;

use App\Absence;
use Illuminate\Queue\SerializesModels;

class AbsenceDemanded {

    use SerializesModels;

    /** @var Absence */
    public $absence;

    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }
}