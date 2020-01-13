<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['title', 'file', 'attachable'];

    public function attachable()
    {
        return $this->morphTo();
    }
}
