<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Exception;
use Illuminate\Support\Facades\{Auth, Hash};

class ProfileController extends Controller
{
    public function getProfile()
    {
        try {
            $user = auth()->user();
            return response200(__('message.fetched', ['name' => 'profile']), $user);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => 'profile']), $e->getMessage());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        // upload profile image by the helper function
        if ($request->hasFile('profile_image')) {
            $data['profile'] = CommonHelper::fileUpload($request->file('profile_image'), 'profile-images');
        }

        $user->update($data);
        return response200(__('message.updated', ['name' => 'profile']), $user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->current_password, $user->password)) {
                return response401(__('message.incorrect_password'));
            }

            $user->update(['password' => $request->password]);
            return response200(__('message.updated', ['name' => 'password']));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => 'updation']), $e->getMessage());
        }
    }
}
