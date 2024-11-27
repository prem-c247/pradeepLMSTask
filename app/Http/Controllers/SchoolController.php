<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SchoolController extends Controller
{
    /**
     * index: Get the all schools along with school details
     * Can apply multiple filter like (name, email, phone, status)
     * @param  mixed $request
     * @return void
     */
    function index(Request $request)
    {
        $query = User::school()->with('role', 'schoolDetails');
        // Apply filters
        if ($request->filled('filter')) {
            $columns = ['email', 'status', 'phone'];
            $filter = $request->filter;

            // filter by the school name
            $query->whereHas('schoolDetails', function ($subQuery) use ($filter) {
                $subQuery->where('school_name', 'like', "%$filter%");
            });
            $query->orWhere(function ($subQuery) use ($filter, $columns) {
                foreach ($columns as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $filter . '%');
                }
            });
        }
        $schools = $query->paginate(PAGINATE);
        if ($schools->isEmpty()) {
            return $this->notFound('school');
        }
        return response200(__('message.fetched', ['name' => __('message.school')]), $schools);
    }

    /**
     * details: Get the details of specific school
     *
     * @param  mixed $schoolId
     * @return void
     */
    public function details($schoolId)
    {
        $school = User::school()->with('schoolDetails')->find($schoolId);
        if (!$school) {
            return $this->notFound('school');
        }
        return response200(__('message.fetched', ['name' => __('message.school')]), $school);
    }

    /**
     * update: Update the school details provided by school ID
     *
     * @param  mixed $request
     * @param  mixed $schoolId
     * @return void
     */
    public function update(UpdateSchoolRequest $request, $schoolId)
    {
        try {
            $school = User::school()->find($schoolId);
            if (!$school) {
                return $this->notFound('school');
            }
            $validated = $request->validated();

            // upload profile image by the helper function
            if ($request->hasFile('profile')) {
                $validated['profile'] = CommonHelper::fileUpload($request->file('profile'), PROFILE_IMAGE_DIR);

                // Remove the old image
                $oldImageName = $school->getAttributes()['profile'];
                CommonHelper::deleteImageByName($oldImageName, PROFILE_IMAGE_DIR);
            }
            $school->update($validated);
            $school->schoolDetails()->update(Arr::only($validated, ['school_name', 'owner_name']));

            // get the address validation rule array's keys and update the address
            $addressValidationArray = CommonHelper::getAddressValidationRules();
            $addressValidationArrayKeys = array_keys($addressValidationArray);
            $school->addresses()->update(Arr::only($validated, $addressValidationArrayKeys));

            // load the all related data
            $school = $school->load('schoolDetails', 'addresses');
            return response200(__('message.updated', ['name' => __('message.school')]), $school);
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.updation')]), $e->getMessage());
        }
    }

    /**
     * delete: Delete the specific school by the school ID
     *
     * @param  mixed $schoolId
     * @return void
     */
    public function delete($schoolId)
    {
        try {
            $school = User::find($schoolId);
            if (!$school) {
                return $this->notFound('school');
            }
            $school->delete();
            return response200(__('message.deleted', ['name' => __('message.school')]));
        } catch (Exception $e) {
            return response500(__('message.server_error', ['name' => __('message.deletion')]), $e->getMessage());
        }
    }
}
