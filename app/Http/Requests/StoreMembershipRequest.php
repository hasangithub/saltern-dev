<?php
// app/Http/Requests/StoreOwnerRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Gender;
use App\Enums\CivilStatus;

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
            'saltern_id' => 'required|exists:salterns,id',
            'owner_id' => 'required|exists:owners,id',
            'membership_date' => 'required|date',
            'owner_signature' => 'required|image|max:2048',
            'representative_signature' => 'required|image|max:2048',
            'is_active' => 'boolean',
            'owner_signature' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Owner's signature validation
            'representative_signature' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Representative's signature validation
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