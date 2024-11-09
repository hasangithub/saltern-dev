<?php
// app/Http/Requests/StoreOwnerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Gender;
use App\Enums\CivilStatus;

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
            'full_name' => 'required|string|max:255', // Full name required
            'gender' => 'required|in:' . implode(',', array_column(Gender::cases(), 'value')), // Gender enum values
            'civil_status' => 'required|in:' . implode(',', array_column(CivilStatus::cases(), 'value')), // Civil status enum values
            'date_of_birth' => 'required|date', // Date of birth should be a valid date
            'nic' => 'required|string|max:20|unique:representatives', // NIC should be unique
            'phone_number' => 'required|string|max:15', // Primary phone number, required
            'secondary_phone_number' => 'nullable|string|max:15', // Secondary phone number is optional
            'email' => 'required|email|unique:representatives', // Email should be unique
            'address_line_1' => 'required|string|max:255', // First line of address, required
            'address_line_2' => 'nullable|string|max:255', // Second line of address, optional
           // 'profile_picture' => 'nullable|image|max:2048', // Profile picture, optional but should be an image
         //   'membership_no' => 'required|string|max:50|unique:representatives', // Membership number, required and unique
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