<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user.phone' => ['nullable', 'string', 'max:255'],
            'user.national_id' => ['nullable', 'string', 'max:255'],
            'user.password' => ['required', 'string', 'min:8', 'confirmed'],
            'organization.name' => ['required', 'string', 'max:255'],
            'organization.tin_number' => ['required', 'string', 'max:255', 'unique:organizations,tin_number'],
            'organization.VAT_reg_number' => ['nullable', 'string', 'max:255'],
            'organization.VAT_reg_date' => ['nullable', 'date'],
            'organization.email' => ['nullable', 'email', 'max:255'],
            'organization.phone' => ['nullable', 'string', 'max:255'],
            'organization.house_number' => ['nullable', 'string', 'max:255'],
            'organization.trade_name' => ['nullable', 'string', 'max:255'],
            'organization.legal_name' => ['nullable', 'string', 'max:255'],
            'organization.woreda_id' => ['nullable', 'integer', 'exists:woredas,id'],
            'organization.kebele_id' => ['nullable', 'integer', 'exists:kebeles,id'],
            'organization.locality_id' => ['nullable', 'integer', 'exists:localities,id'],
            'organization.tax_center_id' => ['nullable', 'integer', 'exists:tax_centers,id'],
            'organization.sector_type' => ['nullable', 'string', 'max:255'],
        ];
    }
}

