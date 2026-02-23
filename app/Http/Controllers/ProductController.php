<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductList;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $search = request()->query('search', '');
        $perPage = request()->query('per_page', 10);
        $sortField = request()->query('sort_field', 'updated_at');
        $sortDirection = request()->query('sort_direction', 'desc');
        $query = Product::query()->where('name', 'like', "%{$search}%")->orderBy($sortField, $sortDirection)->paginate($perPage);

        return ProductList::collection($query);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $image = $data['image'] ?? null;
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = $relativePath;
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }
        $product = Product::create($data);
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;
        $image = $data['image'] ?? null;
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = $relativePath;
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }
        $product->update($data);
        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }


    private function saveImage(UploadedFile $image)
    {
        $path = 'images/' . Str::random();
        $filename = $image->getClientOriginalName();
        $relativePath = $image->storeAs($path, $filename, 'public');
        return $relativePath;
    }
}
