<?php

namespace App;

use App\Traits\HasCommentsTrait;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class Funeral extends Model
{
    use HasEncryptedAttributes;
    use HasCommentsTrait;

    protected $fillable = [
        'service_id',
        'buried_name',
        'buried_address',
        'buried_zip',
        'buried_city',
        'text',
        'announcement',
        'type',
        'wake',
        'wake_location',
        'relative_name',
        'relative_address',
        'relative_zip',
        'relative_city',
    ];

    protected $dates = [
        'announcement',
        'wake',
    ];

    /** @var array  */
    protected $encrypted = [
        'buried_name',
        'buried_address',
        'buried_zip',
        'buried_city',
        'relative_name',
        'relative_address',
        'relative_zip',
        'relative_city',
    ];

    public function service() {
        return $this->belongsTo(Service::class);
    }
}
