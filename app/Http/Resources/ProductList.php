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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_url' => $this->image ? URL::to(Storage::url($this->image)) : null,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'updated_at' => (new DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
