<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    use HasEncryptedAttributes;

    protected $fillable = [
        'user_id',
        'body',
        'private',
        'commentable_id',
        'commentable_type',
    ];

    protected $encrypted = [
        'body',
    ];

    /**
     * Get all of the owning commentable models.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
