<?php

namespace App\Http\Requests\Teacher;

use App\Helpers\CommonHelper;
use App\Http\Requests\BaseFormRequest;
use App\Rules\ImageUploadRule;
use App\Rules\NameRule;

class UpdateTeacherRequest extends BaseFormRequest
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
            'first_name' => ['sometimes', 'string', 'max:50', new NameRule()],
            'last_name' => ['sometimes', 'string', 'max:50', new NameRule()],
            'phone' => 'sometimes|digits_between:10,12|unique:users,phone,' . $this->route('teacherId'),
            'profile' =>  ['nullable', new ImageUploadRule()],
            'experience' => 'sometimes|numeric',
            'expertises' => 'sometimes|array',
        ];
        return array_merge($addressValidationArray, $rules);
    }
}
