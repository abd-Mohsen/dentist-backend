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
            'product' => new SimpleProductResource(Product::find($this->pivot->product_id)), //make a simplified product resource
            'quantity' => $this->pivot->quantity,
            'price' => $this->pivot->price,
        ];
    }
}
