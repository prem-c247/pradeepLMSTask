<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\Teacher\SendInviteLinkRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Mail\TeacherInvitationMail;
use App\Models\{InvitationLink, User};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    /**
     * index: Get all teachers alongwith teachers details and addresses
     * Can apply multiple filter like (name, email, phone, status)
     *
     * @param  mixed $request
     * @return void
     */
    function index(Request $request)
    {
        $query = User::teacher()->with('role', 'teacherDetails.school.schoolDetails');
        // Apply filters
        if ($request->filled('filter')) {
            $filter = $request->filter;
            $columns = ['last_name', 'status', 'email', 'phone'];
            $query->where(function ($subQuery) use ($filter, $columns) {
                $subQuery->where('first_name', 'like', '%' . $filter . '%');
                foreach ($columns as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $filter . '%');
                }
            });
        }
        $teachers = $query->paginate(PAGINATE);
        if ($teachers->isEmpty()) {
            return $this->notFound('teacher');
        }
        return response200(__('message.fetched', ['name' => __('message.teacher')]), $teachers);
    }

    /**
     * SendInviteLinkToTeacher: Send the invite link to given email. 
     * Via the link user can registered himself as teacher 
     * @param  mixed $request
     * @return void
     */
    function SendInviteLinkToTeacher(SendInviteLinkRequest $request)
    {
        try {
            $authID  = auth()->id();
            // Generate a unique token of auth id for the invitation
            $token = encrypt($authID);

            // Save the invitation link details to the database
            $invitation = InvitationLink::updateOrCreate([
                'sender_id' => $authID,
                'email' => $request->email
            ], [
                'token' => $token
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

    /**
     * details: Get the teacher details by their ID
     *
     * @param  mixed $teacherId
     * @return void
     */
    public function details($teacherId)
    {
        $teacher = User::teacher()->with('teacherDetails')->find($teacherId);
        if (!$teacher) {
            return $this->notFound('teacher');
        }
        return response200(__('message.fetched', ['name' => __('message.teacher')]), $teacher);
    }

    /**
     * update: Update the teacher details by their ID
     *
     * @param  mixed $request
     * @param  mixed $teacherId
     * @return void
     */
    public function update(UpdateTeacherRequest $request, $teacherId)
    {
        try {
            $teacher = User::teacher()->find($teacherId);
            if (!$teacher) {
                return $this->notFound('teacher');
            }
            $validated = $request->validated();
            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), PROFILE_IMAGE_DIR);

                // Remove the old image
                $oldImageName = $teacher->getAttributes()['profile'];
                if ($oldImageName) {
                    CommonHelper::deleteImageByName($oldImageName, PROFILE_IMAGE_DIR);
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

    /**
     * delete: Delete teacher by their ID
     *
     * @param  mixed $teacherId
     * @return void
     */
    public function delete($teacherId)
    {
        try {
            $teacher = User::teacher()->find($teacherId);
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
