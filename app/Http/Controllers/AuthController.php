<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PharIo\Manifest\Email;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $token = $user->createToken('AuthToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    
    }
    

public function login(Request $request)
{
    
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    
    if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
        // successfull authentication
        $user = User::find(Auth::user()->id);

        $user_token['token'] = $user->createToken('appToken')->accessToken;

        return response()->json([
            'success' => true,
            'token' => $user_token,
            'user' => $user,
        ], 200);
    } else {
        // failure to authenticate
        return response()->json([
            'success' => false,
            'message' => 'Failed to authenticate.',
        ], 401);
    }

}



    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json(['message'=>'user logged out'],200);
    }

}
