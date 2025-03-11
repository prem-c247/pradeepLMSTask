<?php

namespace App\Http\Requests\Teacher;

use App\Helpers\CommonHelper;
use App\Http\Requests\BaseFormRequest;
use App\Rules\ImageUploadRule;
use App\Rules\NameRule;
use App\Rules\PasswordRule;

class TeacherRegisterRequest extends BaseFormRequest
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
        $addressValidationArray = CommonHelper::getAddressValidationRules();

        $rules = [
            'token' => 'required|string|max:255',
            'first_name' => ['required', 'string', 'max:50', new NameRule()],
            'last_name' => ['required', 'string', 'max:50', new NameRule()],
            'email' => 'required|email|unique:users|max:100',
            'phone' => 'nullable|digits_between:10,12|unique:users',
            'password' => ['required', new PasswordRule],
            'profile_image' =>  ['nullable', new ImageUploadRule()],

            'experience' => 'nullable|numeric',
            'expertises' => 'nullable|array',
        ];
        return array_merge($addressValidationArray, $rules);
    }
}
