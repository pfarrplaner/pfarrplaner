<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = ['absence_id', 'user_id', 'status'];

    public function approver() {
        return $this->belongsTo(User::class);
    }

    public function absence() {
        return $this->belongsTo(Absence::class);
    }
}
