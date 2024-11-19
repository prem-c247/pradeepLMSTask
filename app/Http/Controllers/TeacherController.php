<?php

namespace App\Http\Controllers;

use App\Mail\TeacherInvitationMail;
use App\Models\{InvitationLink, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Mail, Validator};

class TeacherController extends Controller
{
    function index(Request $request)
    {
        $query = User::teacher()->with('role', 'teacherDetails.school.schoolDetails');

        // Apply filters
        if ($request->filled('filter')) {
            $filter = $request->filter;

            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter . '%')
                    ->orWhere('email', 'like', '%' . $filter . '%')
                    ->orWhere('status', 'like', '%' . $filter . '%');
            });
        }

        // Filter by the school name
        if ($request->filled('school_name')) {
            $query->whereHas('teacherDetails.school', function ($q) use ($request) {
                $q->where('name', 'like', "%$request->school_name%");
            });
        }

        $teachers = $query->paginate(PAGINATE);

        if ($teachers->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Teachers not found!',
                'data' => []
            ], 200);
        }

        return response()->json(['status' => true, 'message' => 'Get teachers successfully.', 'data' => $teachers], 200);
    }

    function SendInviteLinkToTeacher(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
            }

            $authID  = auth()->id();

            // Generate a unique token of auth id for the invitation
            $token = encrypt($authID);

            // Check if an invitation has already been sent to this email
            // $existingInvitation = InvitationLink::where('email', $request->email)->exists();

            // if ($existingInvitation) {
            //     return response()->json(['status' => false, 'message' => 'An invitation is already in progress for this email.'], 400);
            // }

            // Save the invitation link details to the database
            $invitation = InvitationLink::updateOrCreate([
                'sender_id' => $authID,
                'email' => $request->email
            ], [
                'token' => $token,
                // 'expires_at' => now()->addDays(7)
            ]);

            // invitation URL defined in env 
            $invitationLink = env('TEACHER_REGISTRATION_URL') . $token;

            // Send the invitation email
            Mail::to($request->email)->send(new TeacherInvitationMail($invitationLink));

            return response()->json([
                'status' => true,
                'message' => 'Invitation link has been sent to this email address successfully.',
                'token' => $token,
                'data' => $invitation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "An unexpected error occurred. Please try again.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
