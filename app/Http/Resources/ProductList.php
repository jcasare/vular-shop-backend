<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProductList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $imageUrl = $this->image
            ? (str_starts_with($this->image, 'http') ? $this->image : URL::to(Storage::url($this->image)))
            : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_url' => $imageUrl,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'category_id' => $this->category_id,
            'featured' => $this->featured,
            'updated_at' => (new DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
