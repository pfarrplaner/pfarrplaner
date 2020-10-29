<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psy\Util\Str;

class Booking extends Model
{
    use HasEncryptedAttributes;

    protected $fillable = [
        'service_id',
        'code',
        'name',
        'first_name',
        'contact',
        'number'
    ];

    protected $encrypted = [
        'name',
        'first_name',
        'contact'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('number', 'desc');
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service() {
        return $this->belongsTo(Service::class);
    }

    public static function createCode() {
        return str_pad(dechex(rand(0x100000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
    }

}
