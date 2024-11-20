<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\CreateUserModificationRequest;
use App\Models\{User, UserModificationRequest};
use Illuminate\Support\Facades\{DB};

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
            return response()->json([
                'status' => false,
                'message' => 'No modification requests found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'User modification requests retrieved successfully.',
            'data' => $modificationRequests
        ]);
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
                return response()->json(['status' => false, 'message' => 'Target user not found!', 'data' => []], 404);
            }

            $validatedData['requested_by'] = auth()->id();
            $validatedData['requested_to'] = $requestedID;

            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validatedData['profile'] = CommonHelper::fileUpload($request->file('profile'), 'profile-images');
            }

            $modificationRequest = UserModificationRequest::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'User modification request created successfully.',
                'data' => $modificationRequest,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approvedRequest($id)
    {
        try {
            $request = UserModificationRequest::find($id);

            $updatedData = $request->toArray();

            // Remove created and updated date from the array
            unset($updatedData['created_at'], $updatedData['updated_at']);

            // remove the null value element
            $updatedData = array_filter($request->toArray(), function ($value) {
                return !is_null($value);
            });

            if (!$request) {
                return response()->json(['status' => false, 'message' => 'Request is not found!.',], 404);
            }

            // Ensure the request is pending
            if ($request->status !== 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'This request has already been processed.',
                ], 400);
            }

            DB::beginTransaction();

            $targetUser = User::find($request->target_id);

            if (!$targetUser) {
                return response()->json(['status' => false, 'message' => 'Targeted user is not found!.',], 404);
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

            $request->update(['status' => 'Approved']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User modification request approved successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
