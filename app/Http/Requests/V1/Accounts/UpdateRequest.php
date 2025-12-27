<?php

namespace App\Http\Requests\V1\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        'nickname' => ['required', 'string', 'max:255'],
        // Optional sensitive fields
        'pin' => ['nullable', 'digits:4'],
        'api_key' => ['nullable', 'string', 'max:255'],
        'client_secret' => ['nullable', 'string', 'max:255'],

        // Profile image
        'profile_image' => [
          'nullable',
          'image',
          'mimes:jpg,jpeg,png',
          'max:5120',
        ],
      ];
    }
}
