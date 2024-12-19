<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(9);

        return ProductResource::collection($products);
    }
    public function search(Request $request)
    {
        $query = Product::query();

        // Filtro por nombre
        if ($request->has('name') && $request->input('name') !== '') {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filtro por categoría
        if ($request->has('category') && $request->input('category') !== '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('category') . '%');
            });
        }

        // Filtro por precio
        if ($request->has('price_min') && $request->has('price_max')) {
            $query->whereBetween('price', [$request->input('price_min'), $request->input('price_max')]);
        } elseif ($request->has('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        } elseif ($request->has('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Paginación de resultados
        $products = $query->paginate(9);

        // Retorno de los productos con el recurso adecuado
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        abort_if(!auth()->user()->tokenCan('products-show'), 403);
        $product = Product::with('category')->findOrFail($id);

        return new ProductResource($product);
    }


}
