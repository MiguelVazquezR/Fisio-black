<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Service */
class ServiceResource extends JsonResource
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
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'price' => $this->price !== null ? (float) $this->price : null,
            'duration_minutes' => $this->duration_minutes,
            'description' => $this->description,
            'benefits' => $this->benefits ?? [],
            'is_active' => $this->is_active,
        ];
    }
}
