<?php

namespace App\Http\Requests;

use App\Location;
use App\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        } else {
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

        return $data;
    }


}
