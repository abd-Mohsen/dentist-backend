<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|min:4',
            'role_id' => 'required|exists:roles,id',
            'img_url' => 'nullable|url',
        ]);

        //if user was an admin, handle it
        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'img_url' => $data['img_url'],
        ]);

        $user->save();

        // Optionally, you can log in the user after registration
        // Auth::login($user);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('api-token')->plainTextToken;

        // Return a response with the user and token details
        return response()->json([
            'user' => $user->id,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 201);
    }
}

// middlewares (role) and policies

// admin can add categories

// admin can manage orders and order responses