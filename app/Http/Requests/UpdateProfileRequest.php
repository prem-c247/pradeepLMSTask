<?php

namespace App\Http\Requests;

use App\Rules\ImageUploadRule;
use App\Rules\NameRule;

class UpdateProfileRequest extends BaseFormRequest
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
        $user    =   auth()->user();

        return [
            'first_name' => ['required', 'string', 'max:50', new NameRule()],
            'last_name' => ['required', 'string', 'max:50', new NameRule()],
            // 'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|digits_between:10,12|unique:users,phone,' . $user->id,
            'profile_image' => ['nullable', new ImageUploadRule()],
        ];
    }
}
