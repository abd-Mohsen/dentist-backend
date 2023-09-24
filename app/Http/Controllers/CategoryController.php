<?php

namespace App\Http\Controllers;

use App\Models\Category;
//use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Image as ImageModel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{

    public function index() : JsonResponse //only parents btw
    {
        //add pagination
        $categories =Category::where('parent_id', null)->withCount('children')->get();
        return response()->json(CategoryResource::collection($categories));
    }



    public function childCategories() : JsonResponse //pagination
    {
        $categories = Category::whereNotNull('parent_id')->get();
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
                
        //to check if parent exists and eligible
        $parent = null;
        if($data['parent']){
            $parent = Category::where('title', $data['parent'])->first(); 
            if(!$parent) return response()->json(['message' => 'parent category does not exist'], 400);
            if($parent->parent) return response()->json(['message' => 'parent cannot be a child to another category'], 400);
        } 

        //to check if title is unique
        if(Category::where('title', $data['title'])->first())
         return response()->json(['message' => 'category already exists'], 400);

        $image = $this->uploadImage($request->file('image'), $data['title']); // try to not pass request
        
        Category::create([
            'title' => $data['title'],
            'parent_id' => $parent?->id,
            'image_id' => $image->id,
        ]);

        return response()->json(['message' => 'Category created successfully'], 201);
        //check if parent has a parent
    }



    public function show(string $id) : JsonResponse
    {
        $category = Category::with('parent')->withCount('children')->findOrFail($id);
        return response()->json(new CategoryResource($category));
    }



    public function categoryDetails(string $id) : JsonResponse //paginate on products (try on categories)
    {
        $category = Category::with('parent')->findOrFail($id);
        return response()->json([
            'products' => $category->products,
            'sub_categories' => $category->children,
        ]);
    }



    public function update(Request $request, string $id) : JsonResponse // in postman, request not working when sending image as a null file, send as null text 
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $data = $request->validate([
            'title' => 'nullable|string|max:50|min:4',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $image = $data['image'] ? $this->uploadImage($request->file('image'), $data['title']) : null;
        
        if($data['title']) $category->title = $data['title']; 
        if($image) $category->image_id = $image->id; 
        $category->save();
    
        return response()->json(['message' => 'Category updated successfully']);
    }


    public function destroy(string $id) : JsonResponse //check category_product relation
    {
        $this->authorize('delete', Category::class);
        $category = Category::withTrashed()->findOrFail($id);
        $category->products()->detach(); // to remove relationship
        //save category if neccessary
        $category->delete();
        return response()->json(null, 204);
    }



    private function uploadImage($imageFile, string $title) : ImageModel
    { 
        $imgData = Image::make($imageFile)->fit(1280, 720)->encode('jpg');
        $fileName = $title . '-' . uniqid() . '.jpg';
        Storage::put('public/category/' . $fileName , $imgData);

        return ImageModel::create([
            'path' => 'storage/category/' . $fileName,
            'type' => 'category'
        ]);
    }
}