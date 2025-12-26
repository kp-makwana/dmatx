<?php

namespace App\Http\Requests\V1\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class OTPValidateRequest extends FormRequest
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
            'email_otp' => 'required|string|min:6',
            'mobile_otp' => 'required|string|min:6',
        ];
    }
}
