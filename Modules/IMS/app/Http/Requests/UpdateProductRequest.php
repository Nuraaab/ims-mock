<?php

namespace Modules\IMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = (int) $this->route('product');

        return [
            'item_id' => ['sometimes', 'required', 'integer', 'exists:items,id'],
            'code' => ['sometimes', 'nullable', 'string', 'max:255'],
            'barcode' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($productId)],
            'product_group_id' => ['sometimes', 'nullable', 'integer', 'exists:product_groups,id'],
            'default_measurement_id' => ['sometimes', 'nullable', 'integer', 'exists:measurements,id'],
            'track_stock' => ['sometimes', 'boolean'],
            'track_batch' => ['sometimes', 'boolean'],
            'track_expiry' => ['sometimes', 'boolean'],
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
