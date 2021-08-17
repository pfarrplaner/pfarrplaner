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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Baptism
 * @package App
 */
class Baptism extends Model
{
    use HasEncryptedAttributes;
    use HasCommentsTrait;
    use HasAttachmentsTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'service_id',
        'candidate_name',
        'candidate_address',
        'candidate_zip',
        'candidate_city',
        'candidate_email',
        'candidate_phone',
        'pronoun_set',
        'first_contact_with',
        'first_contact_on',
        'registered',
        'signed',
        'appointment',
        'docs_ready',
        'docs_where',
        'city_id',
        'text',
        'notes',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'first_contact_on',
        'appointment',
    ];

    /** @var array */
    protected $encrypted = [
        'candidate_name',
        'candidate_address',
        'candidate_city',
        'candidate_email',
        'candidate_phone',
        'text'
    ];

    protected $with = ['attachments'];

    protected $appends = ['hasRegistrationForm'];

    /**
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return bool
     */
    public function getHasRegistrationFormAttribute()
    {
        $found = false;
        foreach ($this->attachments as $attachment) {
            $found = $found || ($attachment->title == 'Anmeldeformular');
        }
        return $found;
    }

    /**
     * Generate a record for sync'ing to external calendars
     * @return array[]|null
     */
    public function getPreparationEvent()
    {
        if (!$this->appointment) return null;

        $key = 'baptism_prep_'.$this->id;

        $contacts = [];
        if ($this->candidate_phone) $contacts[] = $this->candidate_phone;
        if ($this->candidate_email) $contacts[] = $this->candidate_email;

        $record = [
            'startDate' => $this->appointment->copy()->shiftTimezone('Europe/Berlin')->setTimezone('UTC'),
            'endDate' => $this->appointment->copy()->shiftTimezone('Europe/Berlin')->setTimezone('UTC')->addHour(1),
            'title' => 'Taufgespräch '.$this->candidate_name,
            'description' =>
                '<p>Taufe am '.$this->service->day->date->format('d.m.Y').' um '.$this->service->timeText().' ('.$this->service->locationText().')</p>'
                .'<p><a href="'.route('baptisms.edit', $this->id).'">Taufe im Pfarrplaner öffnen</a></p>'
                .(count($contacts) ? '<p>Kontakt: '.join(', ', $contacts).'</p>' : '')
                .AbstractSyncEngine::AUTO_WARNING,
            'location' => $this->candidate_address.', '.$this->candidate_zip.' '.$this->candidate_city,
        ];
        return [$key => $record];
    }
}
