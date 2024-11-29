<?php
// app/Http/Requests/UpdateOwnerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Gender;
use App\Enums\CivilStatus;
use App\Models\Membership;

class UpdateMembershipRequest extends FormRequest
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
        $membershipId = $this->route('membership')->id;

        return [
            'membership.saltern_id' => [
                'required',
                'exists:salterns,id', // Ensure the saltern exists
                function ($attribute, $value, $fail) use ($membershipId) {
                    // Check if any other membership with this saltern_id is inactive
                    $exists = Membership::where('saltern_id', $value)
                        ->where('is_active', true)
                        ->where('id', '<>', $membershipId) // Exclude the current membership
                        ->exists();

                    if ($exists) {
                        $fail('This saltern is already assigned to an inactive membership. You cannot update the membership with this saltern.');
                    }
                },
            ],
            'membership.owner_id' => 'required|exists:owners,id',
            'membership.membership_date' => 'required|date',
            'membership.is_active' => 'boolean',
            'membership.owner_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Owner's signature validation
            'membership.representative_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Representative's signature validation
            'representative.full_name' => 'required|string|max:255', // Full name required
            'representative.gender' => 'required|in:' . implode(',', array_column(Gender::cases(), 'value')), // Gender enum values
            'representative.civil_status' => 'required|in:' . implode(',', array_column(CivilStatus::cases(), 'value')), // Civil status enum values
            'representative.date_of_birth' => 'required|date', // Date of birth should be a valid date
            'representative.nic' => 'required|string|max:20|unique:representatives,nic,'.optional($this->route('membership')->representative)->id, // NIC should be unique
            'representative.phone_number' => 'required|string|max:15', // Primary phone number, required
            'representative.secondary_phone_number' => 'nullable|string|max:15', // Secondary phone number is optional
            'representative.email' => 'required|email|unique:representatives,email,'.optional($this->route('membership')->representative)->id, // Email should be unique
            'representative.address_line_1' => 'required|string|max:255', // First line of address, required
            'representative.address_line_2' => 'nullable|string|max:255', // Second line of address, optional
            'representative.profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Profile picture, optional but should be an image
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
       return [];
    }
}