<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = (int) $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($userId)],
            'national_id' => ['nullable', 'string', 'max:50', Rule::unique('users', 'national_id')->ignore($userId)],
            'password' => ['prohibited'],
            'password_confirmation' => ['prohibited'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'scope' => ['required', 'string', Rule::in(['organization', 'branch', 'warehouse', 'outlet'])],
            'scope_id' => ['nullable', 'integer'],
            'include_descendents' => ['sometimes', 'boolean'],
        ];
    }
}
