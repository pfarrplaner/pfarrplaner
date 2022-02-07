<?php

namespace App\Http\Requests;

use App\Rules\CreatedInLocalAdminDomainRule;
use App\Services\RoleService;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (isset($this->user)) {
            return $this->user()->can('update', $this->user);
        } else {
            return $this->user()->can('create', User::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'title' => 'nullable|string',
            'email' => 'nullable|string|email|max:255|unique:users,email' . ($this->user ? ',' . $this->user->id : ''),
            'password' => 'nullable|string',
            'office' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|phone_number',
            'preference_cities' => 'nullable|string',
            'manage_absences' => 'nullable|checkbox',
            'homeCities' => 'nullable',
            'homeCities.*' => 'int|exists:cities,id',
            'own_website' => 'nullable|string',
            'own_podcast_title' => 'nullable|string',
            'own_podcast_url' => 'nullable|string|url',
            'own_podcast_spotify' => 'nullable|checkbox',
            'own_podcast_itunes' => 'nullable|checkbox',
            'show_vacations_with_services' => 'nullable|checkbox',
            'needs_replacement' => 'nullable|checkbox',
        ];

        // if a password is set, an email is required
        if ($this->get('password', '') != '') {
            $rules['email'] = 'required|email';
        }

        return $rules;
    }

    public function validated()
    {
        $data = parent::validated();

        // api token
        $data['password'] = $data['password'] ?? '';
        if ((($this->user === null) || ($this->user->api_token == '')) && ($data['password'] != '')) {
            $data['api_token'] = Str::random(60);
        }

        return $data;
    }


    public function getRelationIdsForSync($key, $table = null, $tableKey = 'id')
    {
        $table ??= $key;
        return $this->validate([$key => 'nullable|exists:' . $table . ',' . $tableKey])[$key] ?? [];
    }


    public function getRoles()
    {
        $superAdminRole = Role::where('name',RoleService::ROLE_SUPER_ADMIN)->first()->id;
        $adminRole = Role::where('name', RoleService::ROLE_ADMIN)->first()->id;

        $roles = $this->getRelationIdsForSync('roles');
        if (!count($roles)) {
            return [];
        }

        if (!$this->user()->hasRole(RoleService::ROLE_SUPER_ADMIN)) {
            if (($key = array_search(RoleService::ROLE_SUPER_ADMIN, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        if (!$this->user()->hasRole(RoleService::ROLE_SUPER_ADMIN)
            || $this->user()->hasRole(RoleService::ROLE_ADMIN)) {
            if (($key = array_search(RoleService::ROLE_ADMIN, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        return $roles;
    }

}
