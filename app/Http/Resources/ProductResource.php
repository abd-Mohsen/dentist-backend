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
            'title' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'quantity' => $this->quantity,
            'sell_quantity' => $this->sell_quantity,
            'max_purchase_qty' => $this->max_purchase_qty,
            'min_purchase_qty' => $this->min_purchase_qty,
            'active' => $this->active,
            'upc' => $this->upc,
            'sku' => $this->sku,
            'images' => $this->images,
            'categories' => $this->categories,
            'brand' => $this->brand,
        ];
    }
}
