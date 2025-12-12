<?php

namespace App\Http\Requests\V1\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        'nickname'      => 'required|string|max:255',
        'client_id'     => 'required|string|max:255',
        'pin'           => 'required|max:4',
        'api_key'       => 'required|string|max:255',
        'client_secret' => 'nullable|string|max:255',
        'totp_secret'   => 'required|string|max:255',
      ];
    }
}
