<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function register(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
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
            'acess_token' => $user->createToken("access token")->plainTextToken,
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
            'token' => $token,
        ]);
    }

    public function logout(Request $request) : JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(true, 201);
    }

    public function profile(Request $request) : JsonResponse
    {
        $user = $request->user();
        return new JsonResponse([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'img_url' => $user->imgUrl,
            'role' => $user->role(),
        ]);
    }
}

// middlewares (role) and policies

// admin can add categories

// admin can manage orders and order responses