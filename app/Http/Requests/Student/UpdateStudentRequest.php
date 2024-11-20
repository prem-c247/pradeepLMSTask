<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\BaseFormRequest;

class UpdateStudentRequest extends BaseFormRequest
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
            'phone' => 'sometimes|digits_between:10,12|unique:users,phone,' . $this->route('id'),
            'address' => 'sometimes|string|max:255',
            'profile' => 'sometimes|image|mimes:png,jpg|max:2024',

            'roll_number' => 'sometimes|string|max:255',
            'parents_name' => 'required|string|max:255',
        ];
    }
}
