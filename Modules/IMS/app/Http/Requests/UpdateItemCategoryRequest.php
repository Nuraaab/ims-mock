<?php

namespace Modules\IMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $itemCategoryId = (int) $this->route('item_category');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('item_categories', 'code')->ignore($itemCategoryId)],
            'parent_id' => ['nullable', 'integer', 'exists:item_categories,id', Rule::notIn([$itemCategoryId])],
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
