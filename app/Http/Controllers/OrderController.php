<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Listar todos los pedidos
    public function index()
    {
        return Order::with('user')->get();
    }

    // Crear un nuevo pedido
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'total' => 'required|numeric',
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => $validated['status'],
            'total' => $validated['total'],
        ]);

        return response()->json($order, 201);
    }

    // Mostrar un pedido específico
    public function show(Order $order)
    {
        return $order->load('user');
    }

    // Actualizar un pedido
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order); // Protege con políticas, si es necesario

        $validated = $request->validate([
            'status' => 'string',
            'total' => 'numeric',
        ]);

        $order->update($validated);

        return response()->json($order);
    }

    // Eliminar un pedido
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order); // Protege con políticas, si es necesario

        $order->delete();

        return response()->json(['message' => 'Order deleted'], 200);
    }

    // Obtener los pedidos del usuario autenticado
    public function getUserOrders(Request $request)
    {
        $orders = $request->user()->orders; // Relación en el modelo User
        return response()->json($orders);
    }
}

