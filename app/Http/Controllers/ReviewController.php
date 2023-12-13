<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    //all user's comments
    public function index(Request $request)
    {
        $this->authorize('viewAny', Review::class);
        $userId = $request->user()->id;
        $reviews = Review::where('user_id', $userId)->get();
        return response()->json(ReviewResource::collection($reviews));
    }

    

    public function store(Request $request)
    {
        $this->authorize('create', Review::class);

        $data = $request->validate([
            'product_id' => 'required|numeric',
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|min:4|max:500'
        ]);

        $userId = $request->user()->id;
        $productId = $data['product_id'];

        if(!Product::find($productId))
         return response()->json(['message' => 'product does not exist, how did you even do that?!'], 400); 

        $review = Review::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->first();
        
        if($review){ // to avoid duplicates
            $review->fill($data); //test if user_id remains
            $review->save();
            return response()->json([
                'message' => 'updated successfully',
                'review' => new ReviewResource($review)
            ]); 
        }

        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'comment' => $data['comment'],
            'rate' => $data['rate'],
        ]);

        return response()->json([
            'message' => 'added successfully',
            'review' => new ReviewResource($review)
        ], 201);
    }



    public function destroy(string $id, Request $request)
    {
        $review = Review::where('user_id', $request->user()->id)
                        ->where('product_id', $id)
                        ->firstOrFail();
                            
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(null, 204);
    }
}
