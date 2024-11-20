<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\BaseFormRequest;

class StudentRegisterRequest extends BaseFormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'nullable|digits_between:10,12|unique:users',
            'password' => 'required|min:6',
            'address' => 'nullable|string|max:255',
            'school_id' => 'required|integer|exists:users,id', // relation with school type pf user id
            'parents_name' => 'nullable|string|max:255',
            'profile' => 'nullable|image|mimes:png,jpg|max:2024',
        ];
    }
}
