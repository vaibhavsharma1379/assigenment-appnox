<?php

namespace App\Http\Controllers;

use App\Jobs\BulkCreateUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(){
        $user=Auth::user();
        
        if($user->role==="SuperAdmin"){
            $users=Cache::remember('user_list', 60, function () {
                return User::withTrashed()->get(); // Include soft-deleted users
            });
            
        }
        elseif ($user->role === 'Admin') {
            $users = User::where('role', 'User')->get();
        }
        else {
            $users = User::where('id', $user->id)->get();
        }
        return response()->json(['users' => $users], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'users' => 'required|array', // Expects an array of users
            'users.*.name' => 'required|string',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.password' => 'required|min:6',
            'users.*.role' => 'required|in:SuperAdmin,Admin,User',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        // Dispatch the job to create users in the background
        BulkCreateUser::dispatch($request->users);
    
        return response()->json(['message' => 'Users are being created in the background.'], 202);
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Only Admins and SuperAdmins can update users
        if ($user->role !== 'Admin' && $user->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
        }

        $targetUser = User::findOrFail($id);

        // Admins cannot update SuperAdmins or other Admins
        if ($user->role === 'Admin' && ($targetUser->role === 'SuperAdmin' || $targetUser->role === 'Admin')) {
            return response()->json(['error' => 'Unauthorized. Cannot update this user.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $targetUser->id,
            'password' => 'sometimes|min:6',
            'role' => 'sometimes|in:Admin,User',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        $targetUser->update($validatedData);

        return response()->json(['user' => $targetUser], 200);
    }
    
    public function destroy($id)
    {
        $user = Auth::user();
        $targetUser = User::findOrFail($id);

        // Admins cannot delete SuperAdmins or other Admins
        if ($user->role === 'Admin' && ($targetUser->role === 'SuperAdmin' || $targetUser->role === 'Admin')) {
            return response()->json(['error' => 'Unauthorized. Cannot delete this user.'], 403);
        }


        // Admins cannot delete SuperAdmins or other Admins
        $targetUser->delete(); // Soft delete the user

        return response()->json(['message' => 'User soft deleted successfully.'], 200);
    }
}
