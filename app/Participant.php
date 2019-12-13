<?php

namespace App;

use \Illuminate\Database\Eloquent\Relations\Pivot;

class Participant extends Pivot
{
    public $incrementing = true;
    protected $table = 'service_user';
}