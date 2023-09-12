<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'phone' => 'required|string|min:4',
            'role' => 'required|in:dentist,supplier',
        ]);

        $role_id = Role::where('title' , $data['role'])->first()->id;
        
        //if user was an admin, handle it
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'role_id' => $role_id,
        ]);

        event(new Registered($user));
        
        //use enum for roles

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $user->createToken("access token")->plainTextToken,
            ], 201);
    }

    public function login(Request $request) : JsonResponse
    {
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

        //delete user previous tokens 
        $user->tokens()->delete();
        
        $token = $user->createToken('access token')->plainTextToken;

        return response()->json([
            'user' => $user->id,
            'access_token' => $token,
        ],201);
    }

    public function logout(Request $request) : JsonResponse
    {
        //delete current and all previous sessions 
        $request->user()->tokens()->delete();
        return response()->json(true, 201);
    }
}
