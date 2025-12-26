<?php

namespace App\Http\Requests\V1\Accounts;

use App\Rules\AngleOneSignUpPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class AngleOneAccountCreateRequest extends FormRequest
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
        'account_name' => ['required','string','max:255',],
        'email' => ['required','email','max:255',],
        'mobile' => ['required','digits:10'],
        'client_id' => ['required','string'],
        'password' => ['required','confirmed',new AngleOneSignUpPasswordRule()],
      ];
    }
}
