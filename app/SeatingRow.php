<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SeatingRow extends Model
{
    protected $fillable = ['seating_section_id', 'title', 'seats', 'divides_into', 'spacing', 'split', 'color'];

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

    /**
     * Get CSS style for background color
     * @return mixed|string
     */
    public function getCSS() {
        $color = $this->color ?: $this->seatingSection->color ?: '';
        if ($color != '') $color = 'background-color: '.$color;
        return $color;
    }

}
