<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(9);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        abort_if(! auth()->user()->tokenCan('products-create'), 403, 'No tienes permisos para crear un producto');

        $data = $request->validated();

        // Manejo de la foto (si existe)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = Str::uuid() . '.' . $file->extension();
            $file->storeAs('categories', $name, 'public');
            $data['photo'] = $name;
        }

        // Crear el producto
        $product = Product::create($data);

        // Retornar el producto creado usando ProductResource, incluyendo la URL completa de la foto
        return new ProductResource($product);
    }
}
