<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubOrderResource2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            //'supplier' => new UserResource($this->supplier),
            'status' => $this->status, 
            'ordered_products' => ProductOrderResource::collection($this->products),
            'customer' => new UserResource($this->order->customer), //eager loaded
            'date' => $this->created_at,
        ];
    }
}
