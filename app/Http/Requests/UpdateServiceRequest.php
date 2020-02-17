<?php

namespace App\Http\Requests;

use App\Location;
use App\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        return $data;
    }


}
