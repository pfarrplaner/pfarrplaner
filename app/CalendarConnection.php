<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class CalendarConnection extends Model
{
    use HasEncryptedAttributes;

    public const CONNECTION_TYPE_ALL = 1;
    public const CONNECTION_TYPE_OWN = 2;

    protected $fillable = ['user_id', 'title', 'credentials', 'connection_type'];

    protected $encrypted = ['credentials'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
