<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Image as ModelsImage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(Request $request) : JsonResponse
    {
        $user = $request->user();
        return response()->json(new UserResource($user));
    }



    public function allUsers(Request $request) : JsonResponse
    {
        $users = User::all();
        return response()->json(new UserResource($users));
    }



    public function uploadProfileImage(Request $request) : JsonResponse
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg|max:2048']);

        $user = $request->user();

        $imgData = Image::make($request->file('image'))->fit(480)->encode('jpg');
        $fileName = $user->id . '-' . uniqid() . '.jpg';
        Storage::put('public/profile/'.$fileName , $imgData);

        $image = ModelsImage::create([
            'path' => 'storage/profile/' . $fileName,
            'type' => 'profile'
        ]);

        $user->image_id = $image->id;
        $user->save();

        return response()->json(['image' => $image->path], 201);
    }



    public function editProfile(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|min:4',
            'phone' => 'required|string|min:4',
        ]);

        $user = $request->user();

        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->save();

        return response()->json(true);
    }



    public function editPassword(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }
        $user->password = bcrypt($data['new_password']);
        $user->save();
        return response()->json(['message' => 'Password updated successfully']);
    }



    public function search($query) : JsonResponse //to save bandwidth and memory, create a simpler resource 
    { 
        $users =  UserResource::collection(User::search($query)->get());
        return response()->json($users);
    }
    
}

// middlewares (role) and policies

// admin can add categories

// admin can manage orders and order responses