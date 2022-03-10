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
use Illuminate\Support\Facades\URL;

/**
 * Class Wedding
 * @package App
 */
class Wedding extends Model
{
    use HasEncryptedAttributes;
    use HasCommentsTrait;
    use HasAttachmentsTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'service_id',
        'spouse1_name',
        'spouse1_birth_name',
        'spouse1_email',
        'spouse1_phone',
        'pronoun_set1',
        'spouse2_name',
        'spouse2_birth_name',
        'spouse2_email',
        'spouse2_phone',
        'pronoun_set2',
        'appointment',
        'text',
        'registered',
        'registration_document',
        'signed',
        'docs_ready',
        'docs_where',
        'spouse1_dob',
        'spouse1_address',
        'spouse1_zip',
        'spouse1_city',
        'spouse1_needs_dimissorial',
        'spouse1_dimissorial_issuer',
        'spouse1_dimissorial_requested',
        'spouse1_dimissorial_received',
        'spouse2_dob',
        'spouse2_address',
        'spouse2_zip',
        'spouse2_city',
        'spouse2_needs_dimissorial',
        'spouse2_dimissorial_issuer',
        'spouse2_dimissorial_requested',
        'spouse2_dimissorial_received',
        'needs_permission',
        'permission_requested',
        'permission_received',
        'notes',
        'music',
        'gift',
        'flowers',
        'docs_format',
        'processed',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'appointment',
        'spouse1_dob',
        'spouse2_dob',
        'spouse1_dimissorial_requested',
        'spouse1_dimissorial_received',
        'spouse2_dimissorial_requested',
        'spouse2_dimissorial_received',
        'permission_requested',
        'permission_received',
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
        'spouse1_address',
        'spouse1_zip',
        'spouse1_city',
        'spouse2_address',
        'spouse2_zip',
        'spouse2_city',
        'notes',
    ];

    protected $with = ['attachments'];
    protected $appends = ['spouse1DimissorialUrl', 'spouse2DimissorialUrl'];

    /**
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Generate a record for sync'ing to external calendars
     * @return array[]|null
     */
    public function getPreparationEvent()
    {
        if (!$this->appointment) return null;

        $key = 'wedding_prep_'.$this->id;

        $description =                 '<p>Trauung am '.$this->service->date->format('d.m.Y').' um '.$this->service->timeText().' ('.$this->service->locationText().')</p>'
            .'<p><a href="'.route('funerals.edit', $this->id).'">Trauung im Pfarrplaner öffnen</a></p>'
            .'<p>Kontakt:<br />'
            .trim(' - '.$this->spouse1_name.': '.$this->spouse1_phone.' '.$this->spouse1_email).'<br />'
            .trim(' - '.$this->spouse2_name.': '.$this->spouse2_phone.' '.$this->spouse2_email)
            .'</p>'
            .AbstractSyncEngine::AUTO_WARNING;

        $record = [
            'startDate' => $this->appointment->copy(),
            'endDate' => $this->appointment->copy()->addHour(1),
            'title' => 'Traugespräch '.$this->spouse1_name.' / '.$this->spouse2_name,
            'description' => $description,
            'location' => '',
        ];
        return [$key => $record];
    }

    public function getSpouse1DimissorialUrlAttribute() {
        return URL::signedRoute('dimissorial.show', ['type' => 'trauung', 'id' => $this->id, 'spouse' => 1]);
    }

    public function getSpouse2DimissorialUrlAttribute() {
        return URL::signedRoute('dimissorial.show', ['type' => 'trauung', 'id' => $this->id, 'spouse' => 2]);
    }


}
