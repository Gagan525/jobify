<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[@$!%*#?&])(?=.*\d)[A-Za-z\d@$!%*#?&]+$/',
            ],
            'role' => 'required|in:candidate,recruiter',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'error' => $validator->errors()], 400);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User registered successfully', 'data' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password', 'role');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();
            return response()->json(['status' => 'success', 'message' => 'User logged in successfully', 'data' => $user], 200);
        } else {
            return response()->json(['status' => 'failed', 'error' => 'Unauthorized'], 401);
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'success', 'message' => 'User logged out successfully'], 200);
    }
}