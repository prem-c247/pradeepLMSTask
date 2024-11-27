<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * UpdateUserStatus: Update the user's status by their user ID
     *
     * @param  mixed $request
     * @return void
     */
    public function UpdateUserStatus(UpdateUserStatusRequest $request)
    {
        $user = User::find($request->user_id);
        $user->update(['status' => $request->status]);

        if ($user->status === User::ACTIVE) {
            $message = __('message.actived', ['name' => __('message.user')]);
        } else {
            $message = __('message.deactived', ['name' => __('message.user')]);
        }

        return response200($message, $user);
    }
}
