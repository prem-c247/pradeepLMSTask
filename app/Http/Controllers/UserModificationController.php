<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserModificationRequest;
use App\Models\{User, UserModificationRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserModificationController extends Controller
{
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

            $targetUser->update([
                'name' => $request->name ?? $targetUser->name,
                'email' => $request->email ?? $targetUser->email,
                'phone' => $request->phone ?? $targetUser->phone,
                'status' => $request->user_status ?? $targetUser->status,
            ]);

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
