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

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class UpdateServiceRequest
 * @package App\Http\Requests
 */
class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($service = $this->route('service')) {
            return Auth::user()->can('update', $service);
        } else {
            return false;
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
            'day_id' => 'nullable|integer|exists:days,id',
            'location_id' => 'nullable',
            'time' => 'nullable|date_format:"H:i"',
            'description' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
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
                'nullable',
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
            'hidden' => 'nullable|int|in:0,1',
            'needs_reservations' => 'nullable|int|in:0,1',
            'exclude_sections' => 'nullable|string',
            'registration_active' => 'nullable|int|in:0,1',
            'exclude_places' => 'nullable|string',
            'registration_phone' => 'nullable|string',
            'registration_max' => 'nullable|int',
            'reserved_places' => 'nullable|string',
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
        if (isset($data['special_location'])) {
            $data['location_id'] = 0;
        }

        $data['hidden'] = $data['hidden'] ?? 0;
        $data['needs_reservations'] = $data['needs_reservations'] ?? 0;
        $data['registration_active'] = $data['registration_active'] ?? 0;

        if (isset($data['registration_online_start'])) $data['registration_online_start'] = Carbon::createFromFormat('d.m.Y H:i', $data['registration_online_start']);
        if (isset($data['registration_online_end'])) $data['registration_online_end'] = Carbon::createFromFormat('d.m.Y H:i', $data['registration_online_end']);
        dd($data);

        return $data;
    }


}
