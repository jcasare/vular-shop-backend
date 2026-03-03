<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = CartItem::with('product.activeDiscount')
            ->where('user_id', $request->user()->id)
            ->get();

        $data = $items->map(fn (CartItem $item) => [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'product' => $item->product ? [
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'price' => $item->product->activeDiscount
                    ? $item->product->activeDiscount->discount_price
                    : $item->product->price,
                'image_url' => $item->product->image,
            ] : null,
        ]);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $item = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $item->update(['quantity' => $request->quantity]);

        return response()->json(['data' => $item]);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json([], 204);
    }

    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $userId = $request->user()->id;

        foreach ($request->items as $incoming) {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $incoming['product_id'])
                ->first();

            if ($existing) {
                // Keep the higher quantity — don't lose items
                $existing->update([
                    'quantity' => max($existing->quantity, $incoming['quantity']),
                ]);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $incoming['product_id'],
                    'quantity' => $incoming['quantity'],
                ]);
            }
        }

        return response()->json(['message' => 'Cart synced']);
    }

    public function clear(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([], 204);
    }
}
