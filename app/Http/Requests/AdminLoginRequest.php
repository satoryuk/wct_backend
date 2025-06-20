<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change to true if you want to allow all users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Add custom validation: require either email or phone
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->email) {
                $validator->errors()->add('identifier', 'Either email, phone number, or username is required.');
            }
        });
    }
}
