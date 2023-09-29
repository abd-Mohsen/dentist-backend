<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Image as ImageModel;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()//pagination
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
            'price' => 'required|numeric|min:0|max:999999',
            'weight' => 'required|numeric|min:0|max:9999',
            'length' => 'required|numeric|min:0|max:9999',
            'width' => 'required|numeric|min:0|max:9999',
            'height' => 'required|numeric|min:0|max:9999',
            'quantity' => 'required|numeric|min:0',
            'sell_quantity' => 'required|numeric|min:0',
            'max_purchase_qty' => 'required|numeric|min:0',
            'min_purchase_qty' => 'required|numeric|min:0',
            'active' => 'required|bool',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric|min:1',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($data['min_purchase_qty'] > $data['max_purchase_qty'] ||
           $data['max_purchase_qty'] > $data['sell_quantity'] ||
           $data['sell_quantity'] > $data['quantity'] )
            return response()->json(['message' => 'quantities are not valid, please recheck them'], 400);

        $brand = Brand::where('title', $data['brand'])->first();
        if(!$brand) return response()->json(['message' => 'Brand does not exist'], 400);

        $imagesIds = $this->uploadImages($request->file('images'), $data['name']);
        if(!count($imagesIds)) return response()->json(['message' => 'choose some images first'], 400);

        $categoriesIds = $data['categories'];
        if(!count($categoriesIds)) return response()->json(['message' => 'choose some categories first'], 400);
        
        foreach ($categoriesIds as $categoryId) {
            $category = Category::find($categoryId);
            if(!$category) return response()->json(['message' => 'Category does not exist'], 400);
            if (!$category->parent_id) 
                return response()->json(['message' => 'Category ' . $category->title . ' is a parent.. so cannot be used'], 400);
        }
        
        $product = Product::create([
            'name' => $data['name'], 
            'description' => $data['description'],
            'price' => $data['price'],
            'weight' => $data['weight'],
            'length' => $data['length'],
            'width' => $data['width'],
            'height' => $data['height'],
            'upc' => $data['upc'],
            'sku' => Str::random(6),
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
        $product->images()->attach($imagesIds); //fill image_product table
        $product->save();

        return response()->json([
            'message' => 'Product created successfully',
            'product' => new ProductResource($product)
        ], 201);
    }



    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json(new ProductResource($product));
    }



    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $data = $request->validate([
            'name' => 'nullable|string|max:50|min:4',
            'description' => 'nullable|string|max:400|min:8',
            'upc' => 'nullable|string',
            'brand' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:999999',
            'weight' => 'required|numeric|min:0|max:9999',
            'length' => 'required|numeric|min:0|max:9999',
            'width' => 'required|numeric|min:0|max:9999',
            'height' => 'required|numeric|min:0|max:9999',
            'quantity' => 'nullable|numeric|min:0',
            'sell_quantity' => 'nullable|numeric|min:0',
            'max_purchase_qty' => 'nullable|numeric|min:0',
            'min_purchase_qty' => 'nullable|numeric|min:0',
            'active' => 'nullable|bool',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|numeric|min:1',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($data['min_purchase_qty'] > $data['max_purchase_qty'] ||
           $data['max_purchase_qty'] > $data['sell_quantity'] ||
           $data['sell_quantity'] > $data['quantity'] )
            return response()->json(['message' => 'quantities are not valid, please recheck them'], 400);

        if($data['brand']){
            $brand = Brand::where('title', $data['brand'])->first();
            if(!$brand) return response()->json(['message' => 'Brand does not exist'], 400);
        }
        
        $data = array_filter($data, fn($value) => $value != null);
    
        $imagesIds = $this->uploadImages($request->file('images'), $data['name']);
        $categoriesIds = $data['categories'];

        if($categoriesIds){
            foreach ($categoriesIds as $categoryId) {
                $category = Category::find($categoryId);
                if(!$category) return response()->json(['message' => 'Category does not exist'], 400);
                if (!$category->parent_id) 
                    return response()->json(['message' => 'Category ' . $category->title . ' is a parent.. so cannot be used'], 400);
            }
            if(count($categoriesIds)) $product->categories()->sync($categoriesIds);
        }

        if(count($imagesIds)) $product->images()->sync($imagesIds);
        
        $product->fill($data);

        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => new ProductResource($product)
        ]);
    }



    public function destroy(string $id)//remove 'with trashed' if necessary
    {
        $product =  Product::withTrashed()->findOrFail($id);
        $this->authorize('delete', $product);
        $product->delete();
        return response()->json(null, 204);
        // when making orders. make sure that deleting product doesnt delete orders
    }



    public function search($query) : JsonResponse //to save bandwidth and memory, create a simpler resource 
    { 
        $products =  ProductResource::collection(Product::search($query)->get());
        return response()->json($products);
    }


    // todo: for all images , delete the old ones when updating 
    private function uploadImages(array $imageFiles, string $title): array
    {
        $uploadedImages = [];

        foreach ($imageFiles as $imageFile) {
            $imgData = Image::make($imageFile)->fit(1280, 720)->encode('jpg');
            $fileName = $title . '-' . uniqid() . '.jpg';
            Storage::put('public/product/' . $fileName, $imgData);

            $createdImage = ImageModel::create([
                'path' => 'storage/product/' . $fileName,
                'type' => 'product'
            ]);

            $uploadedImages[] = $createdImage->id;
        }

        return $uploadedImages;
    }
}
