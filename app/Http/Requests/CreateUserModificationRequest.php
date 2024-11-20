<?php

namespace App\Http\Requests;

use App\Rules\UniqueAcrossTables;

class CreateUserModificationRequest extends BaseFormRequest
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
        $id = request('target_id');

        return [
            'type' => 'required|in:edit,delete',
            'target_id' => 'required|exists:users,id',
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                new UniqueAcrossTables('email', ['users', 'user_modification_requests'], $id),
            ],
            'phone' => [
                'sometimes',
                'digits_between:10,12',
                new UniqueAcrossTables('phone', ['users', 'user_modification_requests'], $id),
            ],
            'profile' => 'sometimes|image|mimes:png,jpg|max:2048',
            'address' => 'sometimes|string|max:255',
            'owner_name' => 'sometimes|string|max:255',
            'roll_number' => 'sometimes|string|max:255',
            'parents_name' => 'sometimes|string|max:255',
            'experience' => 'sometimes|string|max:255',
            'expertises' => 'sometimes|string|max:255',
            'user_status' => 'sometimes|in:Active,Inactive',
        ];
    }
}
