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
            'amount_due' => 'sometimes|numeric',
            'number_of_items' => 'sometimes|int',
            'payment_method' => 'required|int',
            'customer' => 'sometimes|array',
            'customer.*name' => 'sometimes|string',
            'customer.*phone_number' => 'sometimes|string',
            'customer.*address' => 'sometimes|string',
            'checkouts' => 'required|array',
            'checkouts.*.id' => 'required|int|exists:products,id',
            'checkouts.*.quantity' => 'required|int|min:1',
            'checkouts.*.srp' => 'required|numeric|min:1',
        ];
    }
}
