<?php

namespace App\Http\Requests;

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
            'name' => 'required|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|digits_between:10,12|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:255',
            'profile' => 'nullable|mimes:png,jpg|max:2024',
        ];
    }
}
