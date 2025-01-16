<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    public function storeUpdate(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'order_id' => 'required|unique:orders,order_id',
            'address' => 'required',
            'total_price' => "required|integer",
            'quantity' => "required|integer",
            'status' => 'required|in:pending,approved,rejected'

        ], [
            'required' => 'input :attribute harus diisi!.',
            'integer' => 'input :attribute harus berupa angka',
            'exists' => 'input :attribute tidak ditemukan di table movies!',
            'in' => 'Input :attribute tidak valid, harus salah satu dari pending, approved, atau rejected.'
        ]);



        $orderId = 'ORD-' . now()->format('Ymd') . '-' . Str::random(6);
        $user = auth()->user();
        $product = Product::find($request->input('product_id'));

        if (!$product) {
            return response()->json([
                'message' => "Product tidak ditemukan!",

            ], 404);
        };

        $order = Order::updateOrCreate(['user_id' => $user->id, 'product_id' => $product->id], [
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            'order_id' => $orderId,
            'address' => $request->input('address'),
            'total_price' => $request->input('total_price'),
            'quantity' => $request->input('quantity'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'message' => 'Order berhasil dibuat/diubah',
            'data' => $order,
        ], 201);
    }
}