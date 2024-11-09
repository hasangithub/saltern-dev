<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Gender;
use App\Enums\CivilStatus;

class UpdateOwnerRequest extends FormRequest
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
        $ownerId = $this->route('owner') ? $this->route('owner')->id : null;

        return [
            'full_name' => 'required|string|max:255', // Full name required
            'gender' => 'required|in:' . implode(',', array_column(Gender::cases(), 'value')), // Gender enum values
            'civil_status' => 'required|in:' . implode(',', array_column(CivilStatus::cases(), 'value')), // Civil status enum values
            'date_of_birth' => 'required|date', // Date of birth should be a valid date
            'nic' => ['required', 'regex:/^(\d{9}[VXvx]|\d{12})$/', 'string', 'max:12', 'unique:owners,nic,' . $ownerId], // NIC should be unique except for the current owner
            'phone_number' => 'required|string|max:10|regex:/^0\d{9}$/', // Primary phone number, required
            'secondary_phone_number' => 'nullable|string|max:12|regex:/^0\d{9}$/', // Secondary phone number is optional
            'email' => ['required', 'email', 'unique:owners,email,' . $ownerId], // Email should be unique except for the current owner
            'address_line_1' => 'required|string|max:255', // First line of address, required
            'address_line_2' => 'nullable|string|max:255', // Second line of address, optional
           // 'profile_picture' => 'nullable|image|max:2048', // Profile picture, optional but should be an image
         //   'membership_no' => 'required|string|max:50|unique:owners', // Membership number, required and unique
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
            'full_name.required' => 'Full name is required.',
            'gender.required' => 'Please select a gender.',
            'civil_status.required' => 'Please select a civil status.',
            'date_of_birth.required' => 'Date of birth is required.',
            'nic.required' => 'NIC is required.',
            'nic.unique' => 'This NIC is already taken.',
            'nic.regex' => 'The NIC format is invalid. Use either the old format (#########V/X) or the new format (12 digits).',
            'phone_number.required' => 'Phone number is required.',
            'email.required' => 'Email is required.',
            'address_line_1.required' => 'Address line 1 is required.',
            'membership_no.required' => 'Membership number is required.',
            'membership_no.unique' => 'This membership number is already taken.',
            'profile_picture.image' => 'The profile picture must be an image.',
            'profile_picture.max' => 'The profile picture size cannot exceed 2MB.',
        ];
    }
}
