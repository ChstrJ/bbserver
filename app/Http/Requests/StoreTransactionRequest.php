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
            'customer_id' => 'sometimes|exists:customers,id',
            'customer_name' => 'sometimes|exists:customers,name',
            'customer_phone_number' => 'sometimes|exists:customers,phone_number',
            'customer_address' => 'sometimes|exists:customers,address',
            'customer_email' => 'sometimes|exists:customers,email',
            'amount_due' => 'sometimes|numeric',
            'number_of_items' => 'sometimes|int',
            'payment_method' => 'required|int',
            'checkouts' => 'required|array',
            'checkouts.*.id' => 'required|int|exists:products,id',
            'checkouts.*.name' => 'required|string|exists:products,name',
            'checkouts.*.quantity' => 'required|int|min:1',
            'checkouts.*.srp' => 'required|numeric|min:1',
        ];
    }
}
