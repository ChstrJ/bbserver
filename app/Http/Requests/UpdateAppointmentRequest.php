<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'full_name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'start_time' => 'sometimes|different:start_time',
            'end_time' => 'sometimes|different:end_time',
            'date' => 'sometimes',
            'status' => 'sometimes'
        ];
    }
}
