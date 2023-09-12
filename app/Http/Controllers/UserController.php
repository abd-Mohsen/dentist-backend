<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Image as ModelsImage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function profile(Request $request) : JsonResponse
    {
        $user = $request->user();
        return new JsonResponse([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'image' => $user->image->path,
            'role' => $user->role->title,
            'is_verified' => $user->hasVerifiedEmail(),
        ]);
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg|max:2048']);

        $user = $request->user();

        $imgData = Image::make($request->file('image'))->fit(480)->encode('jpg');
        $fileName = $user->id . '-' . uniqid() . '.jpg';
        Storage::put('public/profile/'.$fileName , $imgData);

        $image = ModelsImage::create([
            'path' => $fileName,
            'type' => 'profile'
        ]);

        $user->image_id = $image->id;
        $user->save();

        return response()->json(['image' => $image->path], 201);
    }
}

// middlewares (role) and policies

// admin can add categories

// admin can manage orders and order responses