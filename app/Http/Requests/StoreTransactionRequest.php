<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'amount_due' => 'sometimes|numeric',
            'number_of_items' => 'sometimes|int',
            'payment_method' => 'required|int',
            'commission' => 'sometimes|int|min:1',
            // 'image' => 'sometimes|image|mimes:png,jpg,jpeg|max:1024',
            'checkouts' => 'required|array',
            'checkouts.*.id' => 'required|int|exists:products,id',
            'checkouts.*.product_code' => 'required|string|exists:products,product_code',
            'checkouts.*.category_id' => 'required|int|exists:products,category_id',
            'checkouts.*.name' => 'required|string|exists:products,name',
            'checkouts.*.quantity' => 'required|int|min:1',
            'checkouts.*.srp' => 'required|numeric|min:1',
            'checkouts.*.member_price' => 'required|numeric|min:1',
            
        ];
    }
}
