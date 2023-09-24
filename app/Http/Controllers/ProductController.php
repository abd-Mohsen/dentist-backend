<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Image as ImageModel;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json(ProductResource::collection($products));
    }



    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:50|min:4',
            'description' => 'required|string|max:400|min:8',
            'upc' => 'required|string',
            'brand' => 'required|string',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'sell_quantity' => 'required|numeric|min:0',
            'max_purchase_qty' => 'required|numeric|min:0',
            'min_purchase_qty' => 'required|numeric|min:0',
            'active' => 'required|boolean',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric|min:1',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $brand = Brand::where('title', $data['brand']);
        if(!$brand) return response()->json(['message' => 'Brand does not exist'], 400);
                
        $images = $this->uploadImages($request->file('image'), $data['title']); 
        
        $product = Product::create([
            'name' => $data['name'], 
            'description' => $data['description'],
            'price' => $data['price'],
            'weight' => $data['weight'],
            'length' => $data['length'],
            'width' => $data['width'],
            'height' => $data['height'],
            'upc' => $data['upc'],
            'quantity' => $data['quantity'],
            'sell_quantity' => $data['sell_quantity'],
            'max_purchase_qty' => $data['max_purchase_qty'],
            'min_purchase_qty' => $data['min_purchase_qty'],
            'active' => $data['active'],
            'owner_id' => $user->id,
            'brand_id' => $brand->id,
        ]);

        $product->sku = 'PRD-' . $product->id . $user->id . '-' . Str::random(6); 
        $product->categories()->attach($data['categories']); //fill category_product table
        $product->images()->attach($images); //fill image_product table
        $product->save();

        return response()->json(['message' => 'Product created successfully'], 201);
    }



    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json(new ProductResource($product));
    }



    public function update(Request $request, string $id)
    {
        //
    }



    public function destroy(string $id)
    {
        $this->authorize('delete', Product::class);
        Product::withTrashed()->findOrFail($id)->delete();
        return response()->json(null, 204);
        // when making orders. make sure that deleting product doesnt delete orders
    }



    private function uploadImages(array $imageFiles, string $title): array
    {
        $uploadedImages = [];

        foreach ($imageFiles as $imageFile) {
            $imgData = Image::make($imageFile)->fit(1280, 720)->encode('jpg');
            $fileName = $title . '-' . uniqid() . '.jpg';
            Storage::put('public/product/' . $fileName, $imgData);

            $uploadedImages[] = ImageModel::create([
                'path' => 'storage/product/' . $fileName,
                'type' => 'product'
            ])->id;
        }

        return $uploadedImages;
    }
}
