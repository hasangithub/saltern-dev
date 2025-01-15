<?php
// app/Http/Requests/UpdateOwnerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\RelationshipType;
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
            'representative.name_with_initial' => 'required|string|max:255', // Full name required
            'representative.relationship' => 'required|in:' . implode(',', array_column(RelationshipType::cases(), 'value')), // Gender enum values
            'representative.phone_number' => 'required|string|max:15', // Primary phone number, required
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