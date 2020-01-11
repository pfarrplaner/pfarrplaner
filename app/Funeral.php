<?php

namespace App;

use App\Traits\HasAttachmentsTrait;
use App\Traits\HasCommentsTrait;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Funeral extends Model
{
    use HasEncryptedAttributes;
    use HasCommentsTrait;
    use HasAttachmentsTrait;

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
        'relative_contact_data',
        'appointment',
        'dob',
        'dod',
    ];

    protected $dates = [
        'announcement',
        'wake',
        'appointment',
        'dob',
        'dod',
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
        'relative_contact_data',
    ];

    public function service() {
        return $this->belongsTo(Service::class);
    }

    public function age() {
        if (($this->dob) && ($this->dod)) {
            return $this->dod->diffInYears($this->dob);
        }
        return '';
    }

    public function setDobAttribute($date) {
        if (!is_null($date)) {
            $this->attributes['dob'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }
    public function setDodAttribute($date) {
        if (!is_null($date)) {
            $this->attributes['dod'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }
    public function setAnnouncementAttribute($date) {
        if (!is_null($date)) {
            $this->attributes['announcement'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }
    public function setWakeAttribute($date) {
        if (!is_null($date)) {
            $this->attributes['wake'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }
    public function setAppointmentAttribute($date) {
        if (!is_null($date)) {
            $this->attributes['appointment'] = Carbon::createFromFormat('d.m.Y H:i', $date);
        }
    }
}
