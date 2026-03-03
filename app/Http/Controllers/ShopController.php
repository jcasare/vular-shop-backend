<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ShopProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopController extends Controller
{
    public function products(Request $request): AnonymousResourceCollection
    {
        $query = Product::with(['category', 'activeDiscount']);

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        // Price range
        if ($minPrice = $request->query('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->query('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sorting
        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'popular' => $query->orderBy('reviews_count', 'desc'),
            default => $query->orderBy('created_at', 'desc'), // newest
        };

        $perPage = $request->query('per_page', 12);

        return ShopProductResource::collection($query->paginate($perPage));
    }

    public function product(string $idOrSlug): ShopProductResource
    {
        // Accept either numeric ID or slug
        $product = is_numeric($idOrSlug)
            ? Product::with(['category', 'activeDiscount'])->findOrFail($idOrSlug)
            : Product::with(['category', 'activeDiscount'])->where('slug', $idOrSlug)->firstOrFail();

        return new ShopProductResource($product);
    }

    public function featured(): AnonymousResourceCollection
    {
        $products = Product::with(['category', 'activeDiscount'])
            ->where('featured', true)
            ->limit(10)
            ->get();

        return ShopProductResource::collection($products);
    }

    public function categories(): AnonymousResourceCollection
    {
        $categories = Category::withCount('products')->get();

        return CategoryResource::collection($categories);
    }
}
