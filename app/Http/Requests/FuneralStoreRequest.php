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
            'service' => 'int|required|exists:services,id',
            'buried_name' => 'required',
            'dob' => 'nullable|date_format:d.m.Y',
            'dod' => 'nullable|date_format:d.m.Y',
            'wake' => 'nullable|date_format:d.m.Y',
            'announcement' => 'nullable|date_format:d.m.Y',
            'appointment' => 'nullable|date_format:d.m.Y H:i',
        ];
    }
}
