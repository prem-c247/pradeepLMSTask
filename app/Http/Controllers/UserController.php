<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserStatusRequest;
use App\Services\UserProfileService;

class UserController extends Controller
{
    protected $userProfileServive;

    public function __construct(UserProfileService $profileService)
    {
        $this->userProfileServive = $profileService;
    }

    /**
     * UpdateUserStatus: Update the user's status by their user ID
     *
     * @param  mixed $request
     * @return void
     */
    public function updateUserStatus(UpdateUserStatusRequest $request)
    {
        return $this->userProfileServive->updateUserStatus($request->validated());
    }
}
