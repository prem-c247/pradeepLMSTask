<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserModificationRequest;
use App\Services\UserModificationService;

class UserModificationController extends Controller
{
    protected $userModificationService;

    public function __construct(UserModificationService $userModificationService)
    {
        $this->userModificationService = $userModificationService;
    }

    /**
     * index: Get all user modifications requests
     * NOTE: Admin can see all the requests, School will see their teachers and students requests
     * @return void
     */
    public function index()
    {
        return $this->userModificationService->getModificationRequests();
    }

    /**
     * createRequest: Store the user modification request for the target user by their ID
     * NOTE: Modification request will be added by the admin behalf of their school
     * @param  mixed $request
     * @return void
     */
    public function createRequest(CreateUserModificationRequest $request)
    {
        return $this->userModificationService->storeRequestForUserModification($request);
    }

    /**
     * approvedRequest: Approved the user modication request by the ID.
     * if type "EDIT" After the approval it will update the targeted user's details.
     * if type "DELETE" After the approval it will delete targeted user.
     * @param  mixed $requestId
     * @return void
     */
    public function approvedRequest($requestId)
    {
        return $this->userModificationService->storeRequestForUserModification($requestId);
    }
}
