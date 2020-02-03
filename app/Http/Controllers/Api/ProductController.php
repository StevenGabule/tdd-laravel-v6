<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Product;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return new ProductCollection(Product::paginate());
    }
    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price
        ]);

        return response()->json(new ProductResource($product), 201);
    }

    public function show(int $product)
    {
        $product = Product::findOrFail($product);
        return response()->json(new ProductResource($product));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'price' => $request->price,
        ]);
        return response()->json(new ProductResource($product));
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id);
        return response(null, 204);
    }
}
