<?php

namespace App\Http\Requests;

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
        return [
            'category_id' => 'required|int',
            'name' => 'required|string|min:2',
            'description' => 'required|string|min:2',
            'quantity' => 'required|int',
            'srp' => 'required|numeric',
            'member_price' => 'required|numeric',
        ];
    }

    
}
