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

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FuneralStoreRequest
 * @package App\Http\Requests
 */
class FuneralStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function validated()
    {
        $data = parent::validated();
        foreach (
            [
                'buried_address',
                'buried_zip',
                'buried_city',
                'pronoun_set',
                'text',
                'type',
                'relative_name',
                'relative_address',
                'relative_zip',
                'relative_city',
                'wake_location',
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
            ] as $key
        ) {
            $data[$key] = $data[$key] ?? '';
        }

        $data['dimissorial_requested'] ? $data['dimissorial_requested'] = Carbon::createFromFormat('d.m.Y', $data['dimissorial_requested']) : $data['dimissorial_requested'] = null;
        $data['dimissorial_received'] ? $data['dimissorial_received'] = Carbon::createFromFormat('d.m.Y', $data['dimissorial_received']) : $data['dimissorial_received'] = null;


        return $data;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id' => 'required|int|exists:services,id',
            'buried_name' => 'required|string',
            'buried_address' => 'nullable|string',
            'buried_zip' => 'nullable|zip',
            'buried_city' => 'nullable|string',
            'pronoun_set' => 'nullable|string',
            'text' => 'nullable|string',
            'type' => 'nullable|string',
            'relative_name' => 'nullable|string',
            'relative_address' => 'nullable|string',
            'relative_zip' => 'nullable|zip',
            'relative_city' => 'nullable|string',
            'relative_contact_data' => 'nullable|string',
            'wake_location' => 'nullable|string',
            'wake' => 'nullable|date_format:d.m.Y',
            'dob' => 'nullable|date_format:d.m.Y',
            'dod' => 'nullable|date_format:d.m.Y',
            'announcement' => 'nullable|date_format:d.m.Y',
            'appointment' => 'nullable|date_format:d.m.Y H:i',
            'spouse' => 'nullable|string',
            'parents' => 'nullable|string',
            'children' => 'nullable|string',
            'further_family' => 'nullable|string',
            'baptism' => 'nullable|string',
            'confirmation' => 'nullable|string',
            'undertaker' => 'nullable|string',
            'eulogies' => 'nullable|string',
            'notes' => 'nullable|string',
            'announcements' => 'nullable|string',
            'childhood' => 'nullable|string',
            'profession' => 'nullable|string',
            'family' => 'nullable|string',
            'further_life' => 'nullable|string',
            'faith' => 'nullable|string',
            'events' => 'nullable|string',
            'character' => 'nullable|string',
            'death' => 'nullable|string',
            'life' => 'nullable|string',
            'attending' => 'nullable|string',
            'quotes' => 'nullable|string',
            'spoken_name' => 'nullable|string',
            'professional_life' => 'nullable|string',
            'birth_place' => 'nullable|string',
            'death_place' => 'nullable|string',
            'processed' => 'nullable|integer|between:0,1',
            'needs_dimissorial' => 'nullable|integer|between:0,1',
            'dimissorial_issuer' => 'nullable|string',
            'dimissorial_requested' => 'nullable|date_format:d.m.Y',
            'dimissorial_received' => 'nullable|date_format:d.m.Y',
        ];
    }
}
