<?php

namespace App\Http\Requests;

use App\Http\Helpers\ProductCategories;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categories_length = ProductCategories::getCategories();
        return [
            'category_id' => "required|int|min:1|max:$categories_length",
            'name' => 'sometimes|string|min:2',
            'description' => 'sometimes|string|min:2',
            'quantity' => 'sometimes|int|min:1|max:9999',
            'srp' => 'sometimes|numeric|min:1|max:9999',
            'is_remove' => 'required|boolean',
            'member_price' => 'sometimes|numeric|min:1|max:9999',
        ];
    }
}
