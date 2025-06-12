<?php
// app/Http/Requests/StoreOwnerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Gender;
use App\Enums\CivilStatus;
use App\Models\Membership;

class StoreMembershipRequest extends FormRequest
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
            'saltern_id' => [
                'required',
                'exists:salterns,id', // Ensure the saltern exists in the salterns table
                function ($attribute, $value, $fail) {
                    // Check if there is a membership with the same saltern_id where is_active is false
                    $exists = Membership::where('saltern_id', $value)
                        ->where('is_active', true)
                        ->exists();

                    if ($exists) {
                        $fail('This saltern is already assigned to an active membership. You cannot create a new membership with this saltern.');
                    }
                },
            ],
            'owner_id' => 'required|exists:owners,id',
            'membership_date' => 'required|date',
            'membership_no'=> 'required',
            'is_active' => 'boolean',
            'owner_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Owner's signature validation
            'representative_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Representative's signature validation
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