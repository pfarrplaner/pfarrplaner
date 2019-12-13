<?php

namespace App\Http\Requests;

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
            'location_id' => 'nullable|exists:locations,id',
            'time' => 'nullable|date_format:"H:i"',
            'cc_alt_time' => 'nullable|date_format:"H:i"',
            'baptism' => 'nullable|integer|between:0,1',
            'eucharist' => 'nullable|integer|between:0,1',
            'need_predicant' => 'nullable|integer|between:0,1',
            'cc' => 'nullable|integer|between:0,1',
            'description' => 'nullable|string',
            'offering_type' => [Rule::in(['eO', 'PO', ''])
                ],
        ];
    }
}
