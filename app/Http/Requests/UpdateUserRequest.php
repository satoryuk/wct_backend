<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username' => 'sometimes|string|max:255',
            'gender' => 'sometimes|string|max:255',
            'profile' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'role' => 'sometimes|in:admin,customer',
            'email' => 'sometimes|email|max:255',
            'password' => 'nullable|string|min:6',
        ];
    }
}
