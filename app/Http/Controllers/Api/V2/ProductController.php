<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(9);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        // Si llegamos aquí, los datos ya están validados

        // Crear el producto
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price * 100,  // Guardamos el precio en centavos
        ]);

        // Retornar el producto creado usando ProductResource
        return new ProductResource($product);
    }
}
