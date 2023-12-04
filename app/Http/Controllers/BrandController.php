<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Image as ImageModel;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BrandResource;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{

    public function index() : JsonResponse//add pagination
    {
        $brands = Brand::all();
        return response()->json(BrandResource::collection($brands));
    }



    public function store(Request $request) : JsonResponse
    {
        $this->authorize('create', Brand::class);

        $data = $request->validate([
            'title' => 'required|string|max:50|min:4',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
                
        //to check if title is unique
        if(Brand::where('title', $data['title'])->first())
         return response()->json(['message' => 'brand already exists'], 400);

        $image = $this->uploadImage($request->file('image'), $data['title']); 
        
        Brand::create([
            'title' => $data['title'],
            'image_id' => $image->id,
        ]);

        return response()->json(['message' => 'Brand created successfully'], 201);
    }

   

    public function show(string $id) : JsonResponse
    {
        $brand = Brand::findOrFail($id);
        return response()->json(new BrandResource($brand));
    }



    public function getProducts(string $id) : JsonResponse //pagination
    {
        $brand = Brand::with('products')->findOrFail($id);
        return response()->json($brand->products);
    }

   

    public function update(Request $request, string $id) : JsonResponse //same problem as category
    {
        $brand = Brand::findOrFail($id);

        $this->authorize('update', $brand);

        $data = $request->validate([
            'title' => 'nullable|string|max:50|min:4',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $image = array_key_exists('image', $data) ? $this->uploadImage($request->file('image'), $data['title']) : null;
        
        if($data['title']) $brand->title = $data['title']; 
        if($image) $brand->image_id = $image->id; 
        $brand->save();
    
        return response()->json(['message' => 'Brand updated successfully']);
    }

 

    public function destroy(string $id) : JsonResponse //check what happens to its products (set null if necessary)
    {
        $this->authorize('delete', Brand::class);
        Brand::withTrashed()->findOrFail($id)->delete();
        return response()->json(null, 204);
    }



    public function search($query) : JsonResponse //to save bandwidth and memory, create a simpler resource 
    { 
        $brands =  BrandResource::collection(Brand::search($query)->get());
        return response()->json($brands);
    }



    private function uploadImage($imageFile, string $title) : ImageModel
    { 
        $imgData = Image::make($imageFile)->fit(720, 1280)->encode('jpg');
        $fileName = $title . '-' . uniqid() . '.jpg';
        Storage::put('public/brand/' . $fileName , $imgData);

        return ImageModel::create([
            'path' => 'storage/brand/' . $fileName,
            'type' => 'brand'
        ]);
    }
}
