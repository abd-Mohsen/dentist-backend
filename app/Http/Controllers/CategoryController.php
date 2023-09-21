<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Image as ImageModel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{

    public function index() : JsonResponse //only parents
    {
        $categories = Category::where('parent_id', null)->get();
        return response()->json(CategoryResource::collection($categories));
    }



    public function childCategories() : JsonResponse
    {
        $categories = Category::whereNotNull('parent_id')->get();
        //if ($categories->isEmpty()) return response()->json([]);
        return response()->json(CategoryResource::collection($categories));
    }



    public function store(Request $request) : JsonResponse
    {
        $this->authorize('create', Category::class);

        $data = $request->validate([
            'title' => 'required|string|max:50|min:4',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'parent' => 'nullable|string|min:4',
        ]);
                
        //check if parent exists
        $parent = null;
        if($data['parent']){
            $parent = Category::where('title', $data['parent'])->first(); 
            if(!$parent) return response()->json(['message' => 'parent category does not exist'], 400);
        } 

        //check if title is unique
        if(Category::where('title', $data['title'])->first())
         return response()->json(['message' => 'category already exists'], 400);

        $image = $this->uploadImage($request, $data['title']);
        
        Category::create([
            'title' => $data['title'],
            'parent_id' => $parent?->id,
            'image_id' => $image->id,
        ]);

        return response()->json(['message' => 'Category created successfully'], 201);
    }



    public function show(string $id) : JsonResponse
    {
        $category = Category::with('parent')->findOrFail($id);
        return response()->json(new CategoryResource($category));
    }



    public function update(Request $request, string $id) : JsonResponse
    {
        $category = Category::findOrFail($id);
    
        $this->authorize('update', $category);
    
        $title = $request->input('title');
        $image = $request->file('image');
    
        if ($title) {
            $category->title = $title;
        }
    
        if ($image) {
            $category->image_id = $this->uploadImage($request, $title);
        }
    
        $category->save();
    
        return response()->json(['message' => 'Category updated successfully']);
    }



    public function destroy(string $id) : JsonResponse
    {
        $this->authorize('delete', Category::class);
        $category = Category::withTrashed()->findOrFail($id);
        $category->products()->detach(); // remove relationship
        $category->delete();
        return response()->json(null, 204);
    }



    private function uploadImage(Request $request, string $title) : ImageModel
    { 
        $imgData = Image::make($request->file('image'))->fit(1280, 720)->encode('jpg');
        $fileName = $title . '-' . uniqid() . '.jpg';
        Storage::put('public/category/' . $fileName , $imgData);

        return ImageModel::create([
            'path' => 'storage/category/' . $fileName,
            'type' => 'category'
        ]);
    }
}