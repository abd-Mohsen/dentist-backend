<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
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
            'sku' => $this->sku,
            'images' => ImageResource::collection($this->images),
            'categories' => CategoryResource::collection($this->categories),
            'brand' => new BrandResource($this->brand),
        ];
    }
}
