<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreetRange extends Model
{
    protected $fillable = [
        'parish_id', 'name', 'odd_start', 'odd_end', 'even_start', 'even_end'
    ];

    public function parish() {
        return $this->belongsTo(Parish::class);
    }
}
