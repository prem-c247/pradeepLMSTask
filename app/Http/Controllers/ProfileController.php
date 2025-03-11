<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserProfileService;

class ProfileController extends Controller
{
    protected $userProfileServive;

    public function __construct(UserProfileService $profileService)
    {
        $this->userProfileServive = $profileService;
    }
    
    /**
     * getProfile: Get user's basic informations
     *
     * @return void
     */
    public function getProfile()
    {
        return $this->userProfileServive->getProfile();
    }

    /**
     * updateProfile: Update the user's basic informations 
     *
     * @param  mixed $request
     * @return void
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->userProfileServive->updateProfile($request);
    }

    /**
     * changePassword: Update the user's password
     *
     * @param  mixed $request
     * @return void
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->userProfileServive->changePassword($request->validated());
    }
}
