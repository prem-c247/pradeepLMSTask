<?php

namespace App\Http\Controllers;

use App\Mail\TeacherInvitationMail;
use App\Models\{InvitationLink};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Mail, Validator};

class TeacherController extends Controller
{
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
