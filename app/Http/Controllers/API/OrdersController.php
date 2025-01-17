<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Midtrans\Config;
use Midtrans\Snap;

class OrdersController extends Controller
{
    public function storeupdate(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'quantity' => "required|integer",
            'total_price' => 'required|integer'

        ], [
            'required' => 'input :attribute harus diisi!.',
            'in' => 'Input :attribute tidak valid, harus salah satu dari pending, approved, atau rejected.',
            'integer' => 'input :attribute harus berupa angka',
        ]);
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $productId = $request->input('product_id');
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }


        $orderId = uniqid();
        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => (float) ($request->input('total_price') * $request->input('quantity')),
        ];




        $itemDetails = [
            [
                'id' => $productId,
                'price' => (float) $request->input("price"),
                'quantity' => (int) $request->input("quantity"),
                'name' => $product->name,
            ],
        ];


        $user = auth()->user();
        $customerDetails = [
            'user_id' => $user->id,
            'first_name' => $request->input("first_name"),
            'last_name' => $request->input('last_name')

        ];

        $transaction = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($transaction);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}