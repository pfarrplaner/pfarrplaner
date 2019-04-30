<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasEncryptedAttributes;

    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];

    protected $encrypted = [
        'value'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
