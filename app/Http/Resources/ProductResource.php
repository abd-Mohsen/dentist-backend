<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'title' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'weight' => (float) $this->weight,
            'length' => (double) $this->length,
            'width' => (float) $this->width,
            'height' => (float) $this->height,
            'quantity' => (int) $this->quantity,
            'sell_quantity' => (int) $this->sell_quantity,
            'max_purchase_qty' => (int) $this->max_purchase_qty,
            'min_purchase_qty' => (int) $this->min_purchase_qty,
            'active' => (bool) $this->active,
            'upc' => $this->upc,
            'sku' => $this->sku,
            'rating' => [
                'value' => (float) $this->reviews()->avg('rate'),
                'count' => (float) $this->reviews()->count('rate'),
            ],
            'my_review' => new ReviewResource($this->reviews()->where('user_id', $request->user()->id)->first()),
            'reviews' => ReviewResource::collection($this->reviews),
            'images' => ImageResource::collection($this->images),
            'categories' => CategoryResource::collection($this->categories),
            'brand' => new BrandResource($this->brand),
        ];
    }
}
