<?php

namespace App;

use App\Seating\AbstractSeatingModel;
use App\Seating\SeatingModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SeatingSection extends Model
{
    protected $fillable = ['location_id', 'title', 'seating_model', 'sorting', 'priority'];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('title', 'asc');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location() {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seatingRows() {
        return $this->hasMany(SeatingRow::class);
    }

    public function getSeatingModelAttribute() {
        $class = $this->attributes['seating_model'];
        return new $class();
    }

    public function setSeatingModelAttribute($seatingModel)
    {
        if (!is_string($seatingModel)) $seatingModel = get_class($seatingModel);
        $this->attributes['seating_model'] = $seatingModel;
    }

}
