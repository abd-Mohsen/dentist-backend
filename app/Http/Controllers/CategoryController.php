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

    public function index() : JsonResponse
    {
        $categories = Category::all();
        return response()->json($categories);
    }


    public function store(Request $request) : JsonResponse
    {
        // $user = $request->user();
        // if($user->role->title != 'admin') return response()->json(['message' => 'admins only'], 403);
        if($request->user->cannot('create')){
            return response()->json(['message' => 'admins only'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:50|min:4',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'parent' => 'string|min:4',
        ]);
                
        $image = $this->uploadImage($request, $data['title']);

        //check if parent exists
        $parent = null;

        if($data['parent']){
            $parent = Category::where('title', $data['parent'])->first(); //or fail
            if(!$parent) return response()->json(['message' => 'parent category does not exist'], 400);
        }

        //check if title is unique
        if(Category::where('title', $data['title'])->first())
         return response()->json(['message' => 'category already exists'], 400);
        
        Category::create([
            'title' => $data['title'],
            'parent_id' => $parent?->id,
            'image_id' => $image->id,
        ]);

        return response()->json(['message' => 'Category created successfully'], 201);
    }


    public function show(string $id) : JsonResponse
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }


    public function update(Request $request, string $id) : JsonResponse
    {
        $category = Category::findOrFail($id);

        if($request->user->cannot('update')){ // add second parameter $category if there is an error
            return response()->json(['message' => 'admins only'], 403);
        }

        $data = $request->validate([
            'title' => 'string|max:50|min:4',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'parent' => 'string|min:4',
        ]);

        $image = $data['image'] ? $this->uploadImage($request, $data['title']) : null;
        // do the same for parent
    
        $category->fill($request->only([
            'title',
            'parent_id',
             $image?->id,
        ])); //should parent be edited by admin (if so, dont let it be null)
    
        $category->save();
    
        return response()->json(['message' => 'Category updated successfully']);
    }


    public function destroy(string $id) : JsonResponse
    {
        // check if user is admin (create policy later)
        $user = auth()->user();
        if($user->role->title != 'admin') return response()->json(['message' => 'admins only'], 403);
        Category::withTrashed()->findOrFail($id)->delete();
        return response()->json(['message' => 'category deleted successfully'], 204);
        // return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function uploadImage(Request $request, string $title) : ImageModel
    { 
        $imgData = (new Image)->make($request->file('image'))->fit(height:720, width:1280)->encode('jpg');
        $fileName = $title . '-' . uniqid() . '.jpg';
        Storage::put('public/category/'.$fileName , $imgData);

        return ImageModel::create([
            'path' => $fileName,
            'type' => 'category'
        ]);
    }
}
