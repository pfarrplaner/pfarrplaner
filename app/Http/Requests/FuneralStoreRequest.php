<?php

namespace App\Http\Requests;

use App\Funeral;
use Illuminate\Foundation\Http\FormRequest;

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
            'text' => 'nullable|string',
            'type' => 'nullable|string',
            'relative_name' => 'required|string',
            'relative_address' => 'nullable|string',
            'relative_zip' => 'nullable|zip',
            'relative_city' => 'nullable|string',
            'relative_contact_data' => 'nullable|string',
            'wake_location' => 'nullable|string',
            'wake' => 'nullable|date_format:d.m.Y',
            'announcement' => 'nullable|date_format:d.m.Y',
            'appointment' => 'nullable|date_format:d.m.Y H:i',
        ];
    }
}
