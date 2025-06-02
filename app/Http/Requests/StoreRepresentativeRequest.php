<?php
// app/Http/Requests/StoreOwnerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RelationshipType;

class StoreRepresentativeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name_with_initial' => 'nullable|string|max:255', // Full name required
            'relationship' => 'nullable|in:' . implode(',', array_column(RelationshipType::cases(), 'value')), // Gender enum values
            'nic' => 'nullable|string|max:20|unique:representatives', // NIC should be unique
            'phone_number' => 'nullable|string|max:15', // Primary phone number, required
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
           
        ];
    }
}