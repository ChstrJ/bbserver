<?php

namespace App\Http\Requests;

use App\Http\Helpers\ProductCategories;
use DB;
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
        //$categories_length = \App\Http\Utils\ProductCategories::getCategories();
        $product = DB::table("products")->where("id", $this->id)->first();
       
        return [
            'category_id' => "sometimes|int|min:1|exists:categories,id",
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'quantity' => [
                'sometimes',
                'int',
                'min:1',
                'max:10000',
                function ($attribute, $value, $fail) use ($product) {
                    if ($product && $value <= $product->quantity) {
                        $fail("The {$attribute} must be greater than the current quantity");
                    }
                 }
            ],
            'srp' => 'sometimes|numeric|gt:member_price|min:1|max:500000',
            'member_price' => 'sometimes|numeric|min:1|max:500000',
        ];
    }
}
