<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            //these properties are for the original product, not the cart product
            'product' => new ProductResource(Product::find($this->product_id)), //make a simplified product resource
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }
}
