<?php

namespace Modules\IMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'code' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')],
            'product_group_id' => ['nullable', 'integer', 'exists:product_groups,id'],
            'default_measurement_id' => ['nullable', 'integer', 'exists:measurements,id'],
            'track_stock' => ['nullable', 'boolean'],
            'track_batch' => ['nullable', 'boolean'],
            'track_expiry' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
