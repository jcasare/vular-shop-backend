<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductDiscount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductDiscountController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $discounts = $product->discounts()->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $discounts]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'discount_price' => 'required|numeric|min:0|lt:' . $product->price,
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $data['product_id'] = $product->id;
        $data['created_by'] = $request->user()->id;

        $discount = ProductDiscount::create($data);

        return response()->json(['data' => $discount], 201);
    }

    public function update(Request $request, Product $product, ProductDiscount $discount): JsonResponse
    {
        $data = $request->validate([
            'discount_price' => 'nullable|numeric|min:0|lt:' . $product->price,
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $discount->update($data);

        return response()->json(['data' => $discount]);
    }

    public function destroy(Product $product, ProductDiscount $discount): JsonResponse
    {
        $discount->delete();

        return response()->json([], 204);
    }
}
