<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', Wishlist::class);
        $userId = $request->user()->id;
        $wishlists = Wishlist::where('user_id', $userId)->get();
        return response()->json(WishlistResource::collection($wishlists));
    }



    public function store(Request $request)
    {
        $this->authorize('create', Wishlist::class);

        $data = $request->validate(['product_id' => 'required|numeric']);

        $userId = $request->user()->id;
        $productId = $data['product_id'];

        if(!Product::find($productId))
         return response()->json(['message' => 'product does not exist, how did you even do that?!'], 400); 

        $wishlist = Wishlist::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();
        
        if($wishlist) return response()->json(['message' => 'added successfully'], 201); 

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json(['message' => 'added successfully'], 201);
    }



    public function destroy(string $id, Request $request)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
                            ->where('product_id', $id)
                            ->firstOrFail();
                            
        //$this->authorize('delete', $wishlist);
        $wishlist->delete();
        return response()->json(null, 204);
    }
}
