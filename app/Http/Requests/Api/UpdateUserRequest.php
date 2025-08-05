<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => ['sometimes', Password::defaults()],
            'role' => ['sometimes', 'string', 'in:admin,worker,client'],
            'business_registration_number' => [
                'sometimes',
                'required_if:role,worker',
                'nullable',
                'string',
                'max:255',
            ],
            'business_registration_document' => [
                'sometimes',
                'required_if:role,worker',
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240', // 10MB
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'role.in' => 'Role must be one of: admin, worker, client.',
            'business_registration_number.required_if' => 'Business registration number is required for workers.',
            'business_registration_number.max' => 'Business registration number cannot exceed 255 characters.',
            'business_registration_document.required_if' => 'Business registration document is required for workers.',
            'business_registration_document.file' => 'Business registration document must be a file.',
            'business_registration_document.mimes' => 'Business registration document must be a PDF, JPG, JPEG, or PNG file.',
            'business_registration_document.max' => 'Business registration document size cannot exceed 10MB.',
        ];
    }
} 