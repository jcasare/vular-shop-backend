<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ShopProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $activeDiscount = $this->whenLoaded('activeDiscount');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $activeDiscount?->discount_price,
            'discount_ends_at' => $activeDiscount?->ends_at,
            'category' => $this->whenLoaded('category', fn () => $this->category->slug),
            'category_id' => $this->category_id,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'quantity' => $this->quantity,
            'featured' => $this->featured,
            'image_url' => $this->resolveImageUrl($this->image),
            'images' => $this->images ?? [],
        ];
    }

    private function resolveImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        // If it's already a full URL (e.g. unsplash), return as-is
        if (str_starts_with($image, 'http')) {
            return $image;
        }

        return URL::to(Storage::url($image));
    }
}
