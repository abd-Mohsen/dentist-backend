<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'title' => $this->title,
            'image' => $this->image->path,
            'parent' => $this->parent?->title,
            'children_count' => $this->children_count ?? 0, // will be null if not loaded with count ("children")
            //filter resource or create another one
        ];
    }
}
