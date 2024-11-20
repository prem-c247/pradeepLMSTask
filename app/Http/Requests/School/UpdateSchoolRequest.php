<?php

namespace App\Http\Requests\School;

use App\Http\Requests\BaseFormRequest;

class UpdateSchoolRequest extends BaseFormRequest
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
            'name' => 'sometimes|string|max:255',
            // 'email' => 'sometimes|email|max:255|unique:users,email,' . $this->route('id'),
            'phone' => 'sometimes|digits_between:10,12|unique:users,phone,' . $this->route('id'),
            'address' => 'sometimes|string|max:255',
            'profile' => 'sometimes|image|mimes:png,jpg|max:2024',
            'owner_name' => 'sometimes|string|max:255'
        ];
    }
}
