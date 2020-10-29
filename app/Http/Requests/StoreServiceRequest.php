<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

use App\Location;
use App\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreServiceRequest
 * @package App\Http\Requests
 */
class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($service = $this->route('service')) {
            return $this->user()->can('update', $service);
        } else {
            return $this->user()->can('create', Service::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day_id' => 'required|integer|exists:days,id',
            'location_id' => 'nullable',
            'time' => 'nullable|date_format:"H:i"',
            'description' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'special_location' => 'nullable|string',
            'need_predicant' => 'checkbox',
            'baptism' => 'checkbox',
            'eucharist' => 'checkbox',
            'offerings_counter1' => 'nullable|string',
            'offerings_counter2' => 'nullable|string',
            'offering_goal' => 'nullable|string',
            'offering_description' => 'nullable|string',
            'offering_amount' => 'nullable',
            'offering_type' => [
                Rule::in(['eO', 'PO', ''])
            ],
            'others' => 'nullable|string',
            'cc' => 'checkbox',
            'cc_location' => 'nullable|string',
            'cc_lesson' => 'nullable|string',
            'cc_staff' => 'nullable|string',
            'cc_alt_time' => 'nullable|date_format:"H:i"',
            'internal_remarks' => 'nullable|string',
            'title' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'cc_streaming_url' => 'nullable|string',
            'offerings_url' => 'nullable|string',
            'meeting_url' => 'nullable|string',
            'recording_url' => 'nullable|string',
            'external_url' => 'nullable|string',
            'sermon_title' => 'nullable|string',
            'sermon_reference' => 'nullable|string',
            'sermon_description' => 'nullable|string',
            'konfiapp_event_type' => 'nullable|int',
            'konfiapp_event_qr' => 'nullable|string',
            'hidden' => 'nullable|int|in:0,1',
            'needs_reservations' => 'nullable|int|in:0,1',
            'exclude_sections' => 'nullable|string',
        ];
    }

    /**
     * Return validated data with sensible defaults set
     *
     * @return array|void
     */
    public function validated()
    {
        $data = parent::validated();

        // set time and place
        if ($data['special_location'] = ($data['special_location'] ?? '')) {
            $data['location_id'] = 0;
            $data['time'] = $data['time'] ?: '';
            $data['cc_location'] = $data['cc_location'] ?: '';
        } elseif (isset($data['location_id'])) {
            $locationId = $data['location_id'] ?: 0;
            if ($locationId) {
                $location = Location::find($locationId);
                $data['location_id'] = $locationId;
                $data['time'] = $data['time'] ?: $location->default_time;
                $data['cc_location'] = $data['cc_location'] ?? ($data['cc'] ? $location->cc_default_location : '');
            } else {
                $data['time'] = $data['time'] ?: '';
                $data['cc_location'] = $data['cc_location'] ?: '';
            }
        }

        $data['hidden'] = $data['hidden'] ?? 0;

        return $data;
    }


}
