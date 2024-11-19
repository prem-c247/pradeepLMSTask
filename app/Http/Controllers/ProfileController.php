<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user       =   auth()->user();

        return response()->json(['status' => true, 'message' => 'Get profile successfully', 'data' => $user]);
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

        return response()->json(['status' => true, 'message' => 'Profile updated successfully', 'data' => $user]);
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['status' => false, 'message' => 'Old password is incorrect.'], 401);
            }

            $user->update(['password' => $request->password]);

            return response()->json(['status' => true, 'message' => 'The password has been changed successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => __('message.fatal_error'), 'error' => $e->getMessage()], 500);
        }
    }
}
