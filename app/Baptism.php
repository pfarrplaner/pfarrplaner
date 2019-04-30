<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class Baptism extends Model
{
    use HasEncryptedAttributes;

    protected $fillable = [
        'service_id',
        'candidate_name',
        'candidate_address',
        'candidate_zip',
        'candidate_city',
        'candidate_email',
        'candidate_phone',
        'first_contact_with',
        'first_contact_on',
        'registered',
        'registration_document',
        'signed',
        'appointment',
        'docs_ready',
        'docs_where'
        ];

    protected $dates = [
        'first_contact_on',
        'appointment',
    ];

    /** @var array  */
    protected $encrypted = [
        'candidate_name',
        'candidate_address',
        'candidate_city',
        'candidate_email',
        'candidate_phone',
    ];

    public function service() {
        $this->belongsTo(Service::class);
    }

}
