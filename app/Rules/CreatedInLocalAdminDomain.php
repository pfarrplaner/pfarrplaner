<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CreatedInLocalAdminDomain implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $cityIds = Auth::user()->adminCities->pluck('id');
        $pass = false;
        foreach ($cityIds as $cityId) {
            if (($value[$cityId]['permission'] ?? 'n') != 'n') $pass = true;
        }
        return $pass;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Du musst mindestens für eine der von dir verwalteteten Kirchengemeinden Rechte vergeben, sonst kannst du den Benutzer nicht mehr verwalten.';
    }
}
