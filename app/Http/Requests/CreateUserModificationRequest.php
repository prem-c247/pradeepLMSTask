<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\UserModificationRequest;
use App\Rules\ImageUploadRule;
use App\Rules\NameRule;
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
            'type' => 'required|in:' . UserModificationRequest::EDIT . ',' . UserModificationRequest::DELETE,
            'target_id' => 'required|exists:users,id',
            'first_name' => ['sometimes', 'string', 'max:50', new NameRule()],
            'last_name' => ['sometimes', 'string', 'max:50', new NameRule()],
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
            'profile' =>  ['nullable', new ImageUploadRule()],
            // 'address' => 'nullable|array',
            'owner_name' => ['sometimes', 'string', 'max:50', new NameRule()],
            'roll_number' => 'sometimes|string|max:20',
            'parents_name' => ['sometimes', 'string', 'max:50', new NameRule()],
            'experience' => 'sometimes|numeric',
            'expertises' => 'sometimes|array',
            'user_status' => 'sometimes|in:' . User::ACTIVE . ',' . User::INACTIVE
        ];
    }
}
