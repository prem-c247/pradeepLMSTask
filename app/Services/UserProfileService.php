<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\{Auth, Hash};

class UserProfileService
{
    public function getProfile()
    {
        try {
            $user = auth()->user();
            return response200(__('message.fetched', ['name' => __('message.profile')]), $user);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.profile')]), $e->getMessage());
        }
    }

    public function updateProfile(object $request)
    {
        try {
            $user = auth()->user();
            $validatedData = $request->validated();

            // upload profile image by the helper function
            if ($request->hasFile('profile_image')) {
                $validatedData['profile'] = CommonHelper::fileUpload($request->file('profile_image'), PROFILE_IMAGE_DIR);

                // remove old image (get the last segment of URL)
                $oldImageName = substr(strrchr($user->profile, "/"), 1);
                CommonHelper::deleteImageByName($oldImageName, PROFILE_IMAGE_DIR);
            }
            $user->update($validatedData);
            return response200(__('message.updated', ['name' => __('message.profile')]), $user);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }

    public function changePassword(array $validatedData)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response401(__('message.incorrect_password'));
            }

            $user->update(['password' => $validatedData['password']]);
            return response200(__('message.updated', ['name' => 'password']));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }

    /**
     * UpdateUserStatus: Update the user's status by their user ID
     *
     * @param  mixed $request
     * @return void
     */
    public function updateUserStatus(array $validatedData)
    {
        try {
            $user = User::find($validatedData['user_id']);
            $user->update(['status' => $validatedData['status']]);

            if ($user->status === User::ACTIVE) {
                $message = __('message.activated', ['name' => __('message.user')]);
            } else {
                $message = __('message.deactivated', ['name' => __('message.user')]);
            }

            return response200($message, $user);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }
}
