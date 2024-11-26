<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\CreateUserModificationRequest;
use App\Models\{User, UserModificationRequest};
use Exception;

class UserModificationController extends Controller
{
    public function index()
    {
        $loggedInUser = auth()->user();

        $query = UserModificationRequest::query();

        // If the user is not an admin, filter by the 'requested_to'
        if ($loggedInUser->role_id != User::ROLE_ADMIN) {
            $query->where('requested_to', $loggedInUser->id);
        }

        $modificationRequests = $query->with('requestedBy', 'requestedTo', 'targetUser')
            ->paginate(PAGINATE);

        if ($modificationRequests->isEmpty()) {
            return $this->notFound('user_modification_request');
        }

        return response200(__('message.fetched', ['name' => __('message.user_modification_request')]), $modificationRequests);
    }

    public function createRequest(CreateUserModificationRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = User::where('id', $request->target_id)->with('studentDetails.school', 'teacherDetails.school')->first();

            if (!empty($user->studentDetails)) {
                $requestedID = $user->studentDetails?->school?->id;
            } else {
                $requestedID = $user->teacherDetails?->school?->id;
            }

            if (!$user) {
                return $this->notFound('user');
            }

            $validatedData['requested_by'] = auth()->id();
            $validatedData['requested_to'] = $requestedID;

            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validatedData['profile'] = CommonHelper::fileUpload($request->file('profile'), 'profile-images');
            }

            $modificationRequest = UserModificationRequest::create($validatedData);


            return response201(__('message.created', ['name' => __('message.user_modification_request')]), $modificationRequest);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.user_modification_request')]), $e->getMessage());
        }
    }

    public function approvedRequest($id)
    {
        try {
            $request = UserModificationRequest::find($id);
            if (!$request) {
                return $this->notFound('user_modification_request');
            }

            $targetUser = User::find($request->target_id);
            if (!$targetUser) {
                return $this->notFound('user');
            }

            // If request type delete, remove the target user 
            if ($request->type === UserModificationRequest::DELETE) {
                $targetUser->delete();

                $request->update(['status' => UserModificationRequest::APPROVED]);

                return response200(__('message.approved', ['name' => __('message.user_modification_request')]));
            }
            $updatedData = $request->toArray();

            // Remove created and updated date from the array
            unset($updatedData['created_at'], $updatedData['updated_at']);

            // remove the null value element
            $updatedData = array_filter($request->toArray(), function ($value) {
                return !is_null($value);
            });

            // Ensure the request is pending
            if ($request->status !== UserModificationRequest::PENDING) {
                return response400(__('message.processed', ['name' => __('message.user_modification_request')]));
            }
            $targetUser->update($updatedData);

            // Update additional details in the relevant detail table
            if ($targetUser->role->id === User::ROLE_STUDENT) {
                // Update student-specific details
                $targetUser->studentDetails()->update([
                    'roll_number' => $request->roll_number ?? $targetUser->studentDetails->roll_number,
                    'parents_name' => $request->parents_name ?? $targetUser->studentDetails->parents_name,
                ]);
            } elseif ($targetUser->role->id === User::ROLE_TEACHER) {
                // Update teacher-specific details
                $targetUser->teacherDetails()->update([
                    'experience' => $request->experience ?? $targetUser->teacherDetails->experience,
                    'expertises' => $request->expertises ?? $targetUser->teacherDetails->expertises,
                ]);
            }
            $request->update(['status' => UserModificationRequest::APPROVED]);

            return response200(__('message.approved', ['name' => __('message.user_modification_request')]));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.user_modification_request')]), $e->getMessage());
        }
    }
}
