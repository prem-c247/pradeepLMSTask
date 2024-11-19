<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserModificationRequest extends FormRequest
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
            'type' => 'required|in:edit,delete',
            'target_id' => 'required|exists:users,id',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'profile' => 'nullable|string',
            'address' => 'nullable|string',
            'owner_name' => 'nullable|string',
            'roll_number' => 'nullable|string',
            'parents_name' => 'nullable|string',
            'experience' => 'nullable|string',
            'expertises' => 'nullable|string',
            'user_status' => 'nullable|in:Active,Inactive',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => $validator->errors()->first(),
        ], 400));
    }
}
