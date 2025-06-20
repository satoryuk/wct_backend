<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'username' => 'required|string|max:255|unique:users,username',
            'gender' => 'required|in:male,female',
            'profile' => 'sometimes|string|max:255',
            'dob' => 'sometimes|date',
            'role' => 'required|in:admin,customer',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'sometimes|string|max:20',
            'status' => 'sometimes|integer', // 0 for inactive, 1 for active
        ];
    }
}
