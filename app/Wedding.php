<?php

namespace App;

use App\Traits\HasAttachmentsTrait;
use App\Traits\HasCommentsTrait;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    use HasEncryptedAttributes;
    use HasCommentsTrait;
    use HasAttachmentsTrait;

    protected $fillable = [
        'service_id',
        'spouse1_name',
        'spouse1_birth_name',
        'spouse1_email',
        'spouse1_phone',
        'spouse2_name',
        'spouse2_birth_name',
        'spouse2_email',
        'spouse2_phone',
        'appointment',
        'text',
        'registered',
        'registration_document',
        'signed',
        'docs_ready',
        'docs_where',
    ];

    protected $dates = [
        'appointment',
    ];

    /** @var array $encrypted These fields are en-/decrypted on-the-fly */
    protected $encrypted = [
        'spouse1_name',
        'spouse1_birth_name',
        'spouse1_email',
        'spouse1_phone',
        'spouse2_name',
        'spouse2_birth_name',
        'spouse2_email',
        'spouse2_phone',
    ];

    public function service() {
        return $this->belongsTo(Service::class);
    }

}
