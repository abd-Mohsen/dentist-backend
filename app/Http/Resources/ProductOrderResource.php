<?php

namespace App\Http\Resources;

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
            'product' => new ProductResource($this->product), //make a simplified product resource
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
    }
}
