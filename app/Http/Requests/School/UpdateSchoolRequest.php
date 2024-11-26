<?php

namespace App\Http\Requests\School;

use App\Helpers\CommonHelper;
use App\Http\Requests\BaseFormRequest;
use App\Rules\ImageUploadRule;
use App\Rules\NameRule;

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
        $addressValidationArray = CommonHelper::getAddressValidationRules();
        
        $rules = [
            'name' => ['sometimes', 'string', 'max:100',],
            // 'email' => 'sometimes|email|max:100|unique:users,email,' . $this->route('id'),
            'phone' => 'sometimes|digits_between:10,12|unique:users,phone,' . $this->route('id'),
            'profile' =>  ['nullable', new ImageUploadRule()],
            'owner_name' => 'sometimes|string|max:50'
        ];
        
        return array_merge($addressValidationArray, $rules);
    }
}
