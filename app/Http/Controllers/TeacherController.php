<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Mail\TeacherInvitationMail;
use App\Models\{InvitationLink, User};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
                $q->where('first_name', 'like', '%' . $filter . '%')
                    ->orWhere('last_name', 'like', '%' . $filter . '%')
                    ->orWhere('email', 'like', '%' . $filter . '%')
                    ->orWhere('status', 'like', '%' . $filter . '%');
            });
        }
        $teachers = $query->paginate(PAGINATE);
        if ($teachers->isEmpty()) {
            return $this->notFound('teacher');
        }

        return response200(__('message.fetched', ['name' => __('message.teacher')]), $teachers);
    }

    function SendInviteLinkToTeacher(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:50'
            ]);

            if ($validator->fails()) {
                return response400($validator->errors()->first());
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

            return response200(__('message.send_invite'), ['token' => $token, 'data' => $invitation]);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.sending_mail')]), $e->getMessage());
        }
    }

    public function details($teacherID)
    {
        $teacher = User::teacher()->with('teacherDetails')->find($teacherID);

        if (!$teacher) {
            return $this->notFound('teacher');
        }

        return response200(__('message.fetched', ['name' => __('message.teacher')]), $teacher);
    }

    public function update(UpdateTeacherRequest $request, $teacherID)
    {
        try {
            $teacher = User::teacher()->find($teacherID);
            if (!$teacher) {
                return $this->notFound('teacher');
            }
            $validated = $request->validated();

            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), 'profile-images');

                // Remove the old image
                $oldImageName = $teacher->getAttributes()['profile'];
                if ($oldImageName) {
                    CommonHelper::deleteImageByName($oldImageName, 'profile-images');
                }
            }
            // Update the teacher's basic information
            $teacher->update(Arr::only($validated, ['name', 'email', 'phone', 'profile', 'status']));

            $validated['expertises']  = json_encode($request->expertises);
            // Update the teacher's details
            $teacher->teacherDetails()->update(Arr::only($validated, ['experience', 'expertises']));

            // get the address validation rule array's keys and update the address
            $addressValidationArray = CommonHelper::getAddressValidationRules();
            $addressValidationArrayKeys = array_keys($addressValidationArray);
            $teacher->addresses()->update(Arr::only($validated, $addressValidationArrayKeys));

            // load the all related data
            $teacher = $teacher->load('teacherDetails', 'addresses');
            return response200(__('message.updated', ['name' => __('message.teacher')]), $teacher);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }

    public function delete($teacherID)
    {
        try {
            $teacher = User::teacher()->find($teacherID);

            if (!$teacher) {
                return $this->notFound('teacher');
            }

            $teacher->delete();

            return response200(__('message.deleted', ['name' => __('message.teacher')]), $teacher);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.deletion')]), $e->getMessage());
        }
    }
}
