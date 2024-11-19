<?php

namespace App\Http\Controllers;

use App\Models\{User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator};

class UserController extends Controller
{
    public function UpdateUserStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:' . User::ACTIVE . ',' . User::INACTIVE
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 404);
        }

        $user = User::find($request->user_id);
        $user->update(['status' => $request->status]);

        $currentStatus = 'deactived';
        if ($user->status === User::ACTIVE) {
            $currentStatus = 'activated';
        }

        return response()->json(['status' => true, 'message' => "User $currentStatus successfully", 'data' => $user], 200);
    }
}
