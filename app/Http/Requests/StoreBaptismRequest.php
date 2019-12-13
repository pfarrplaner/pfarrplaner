<?php

namespace App\Http\Requests;

use App\Baptism;
use Illuminate\Foundation\Http\FormRequest;

class StoreBaptismRequest extends FormRequest
{

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', Baptism::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service' => 'nullable|exists:services,id',
            'candidate_name' => 'required|regex:/^(\w*), (\w*)$/i',
            'candidate_email' => 'nullable|email',
            'candidate_zip' => 'nullable|zip',
            'candidate_phone' => 'nullable|phone_number',
            'city_id' => 'required|integer|exists:cities,id|in:'.$this->user()->writableCities->pluck('id')->implode(','),
            'first_contact_on' => 'nullable|date_format:d.m.Y',
            'appointment' => 'nullable|date_format:d.m.Y H:i',
            'registered' => 'nullable|integer|between:0,1',
            'signed' => 'nullable|integer|between:0,1',
            'registration_document' => 'nullable|mimetypes:application/pdf',
        ];
    }
}
