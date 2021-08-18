<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App;

use App\Calendars\SyncEngines\AbstractSyncEngine;
use App\Traits\HasAttachmentsTrait;
use App\Traits\HasCommentsTrait;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Funeral
 * @package App
 */
class Funeral extends Model
{
    use HasEncryptedAttributes;
    use HasCommentsTrait;
    use HasAttachmentsTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'service_id',
        'buried_name',
        'buried_address',
        'buried_zip',
        'buried_city',
        'pronoun_set',
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
        'spouse',
        'parents',
        'children',
        'further_family',
        'baptism',
        'confirmation',
        'undertaker',
        'eulogies',
        'notes',
        'announcements',
        'childhood',
        'profession',
        'family',
        'further_life',
        'faith',
        'events',
        'character',
        'death',
        'life',
        'attending',
        'quotes',
        'spoken_name',
        'professional_life',
        'birth_place',
        'death_place',
        'processed',
        'needs_dimissorial',
        'dimissorial_issuer',
        'dimissorial_requested',
        'dimissorial_received',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'announcement',
        'wake',
        'appointment',
        'dob',
        'dod',
        'dimissorial_requested',
        'dimissorial_received',
    ];

    /** @var array */
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
        'spouse',
        'parents',
        'children',
        'further_family',
        'baptism',
        'confirmation',
        'eulogies',
        'notes',
        'announcements',
        'childhood',
        'profession',
        'family',
        'further_life',
        'faith',
        'events',
        'character',
        'death',
        'life',
        'attending',
        'quotes',
        'spoken_name',
        'professional_life',
        'birth_place',
        'death_place',
    ];

    protected $appends = ['age'];
    protected $with = ['attachments'];

    /**
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return string
     */
    public function age()
    {
        if (($this->dob) && ($this->dod)) {
            return $this->dod->diffInYears($this->dob);
        }
        return '';
    }

    /**
     * @param $date
     */
    public function setDobAttribute($date)
    {
        if (!is_null($date)) {
            $this->attributes['dob'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }

    /**
     * @param $date
     */
    public function setDodAttribute($date)
    {
        if (!is_null($date)) {
            $this->attributes['dod'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }

    /**
     * @param $date
     */
    public function setAnnouncementAttribute($date)
    {
        if (!is_null($date)) {
            $this->attributes['announcement'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }

    /**
     * @param $date
     */
    public function setWakeAttribute($date)
    {
        if (!is_null($date)) {
            $this->attributes['wake'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }

    /**
     * @param $date
     */
    public function setAppointmentAttribute($date)
    {
        if (!is_null($date)) {
            $this->attributes['appointment'] = Carbon::createFromFormat('d.m.Y H:i', $date);
        }
    }

    public function getAgeAttribute()
    {
        return $this->age();
    }

    /**
     * Generate a record for sync'ing to external calendars
     * @return array[]|null
     */
    public function getPreparationEvent()
    {
        if (!$this->appointment) return null;

        $key = 'funeral_prep_'.$this->id;

        $record = [
            'startDate' => $this->appointment->copy()->shiftTimezone('Europe/Berlin')->setTimezone('UTC'),
            'endDate' => $this->appointment->copy()->shiftTimezone('Europe/Berlin')->setTimezone('UTC')->addHour(1),
            'title' => 'Trauergespräch '.$this->buried_name,
            'description' =>
                '<p>'.$this->type.' am '.$this->service->day->date->format('d.m.Y').' um '.$this->service->timeText().' ('.$this->service->locationText().')</p>'
                .'<p><a href="'.route('funerals.edit', $this->id).'">Bestattung im Pfarrplaner öffnen</a></p>'
                .'<p>Kontakt: '.nl2br($this->relative_contact_data).'</p>'
                .AbstractSyncEngine::AUTO_WARNING,
            'location' => $this->relative_address.', '.$this->relative_zip.' '.$this->relative_city,
        ];
        return [$key => $record];
    }


}
