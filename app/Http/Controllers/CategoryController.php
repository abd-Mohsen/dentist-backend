<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use Illuminate\Http\JsonResponse;
use App\Models\Image as ImageModel;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{

    public function index()
    {
        //
    }


    public function store(Request $request) : JsonResponse
    {
        // check if user is admin (create policy later)
        $user = $request->user();
        if($user->role_id != 1) return response()->json(['message' => 'admins only'], 401);

        $data = $request->validate([
            'title' => 'required|string|max:50|min:4',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'parent' => 'string|min:4',
        ]);
        
        //handle image upload
        
        $imgData = (new Image)->make($request->file('image'))->fit(height:720, width:1280)->encode('jpg');
        $fileName = $data['title'] . '-' . uniqid() . '.jpg';
        Storage::put('public/category/'.$fileName , $imgData);

        $image = ImageModel::create([
            'path' => $fileName,
            'type' => 'category'
        ]);

        //check if parent exists
        $parent = null;
        
        if($data['parent']){
            $parent = Category::where('title', $data['parent']);
            if(!$parent)
             return response()->json(['message' => 'parent category does not exist'], 400);
        }

        //check if title is unique
        if(Category::where('title', $data['title']))
         return response()->json(['message' => 'category already exists'], 400);
        //check if 

        $category = Category::create([
            'title' => $data['title'],
            'parent_id' => $parent?->id,
            'image_id' => $image->path,
        ]);

        return response()->json(['message' => 'Category created successfully'], 201);
        
    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
