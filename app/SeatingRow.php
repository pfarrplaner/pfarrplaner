<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SeatingRow extends Model
{
    protected $fillable = ['seating_section_id', 'title', 'seats', 'divides_into', 'spacing', 'split'];

    public $bookings = [];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('title', 'asc');
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seatingSection() {
        return $this->belongsTo(SeatingSection::class);
    }

}
