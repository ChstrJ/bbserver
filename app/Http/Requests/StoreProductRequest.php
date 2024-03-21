<?php

namespace App\Http\Requests;


use App\Http\Helpers\ProductCategories;
use Illuminate\Foundation\Http\FormRequest;


class StoreProductRequest extends FormRequest
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
        // $categories_length = ProductCategories::getCategories();
        // 'category_id' => "required|int|min:1|max:$categories_length",
        return [
            'category_id' => "required|int|exist:categories,id",
            'name' => 'required|string|min:2|unique:products,name',
            'description' => 'required|string|min:2',
            'quantity' => 'required|int',
            'srp' => 'required|numeric',
            'member_price' => 'required|numeric',
        ];
    }
}
