<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\BaseFormRequest;

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
        return [
            'token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'nullable|digits_between:10,12|unique:users',
            'password' => 'required|min:6',
            'address' => 'nullable|string|max:255',
            'profile' => 'nullable|image|mimes:png,jpg|max:2024',
            
            'experience' => 'nullable|string|max:255',
            'expertises' => 'nullable|string', // comma separated values
        ];
    }
}
