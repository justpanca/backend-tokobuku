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

        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $productId = Product::findOrFail($request->input('product_id'));
        $orderId = uniqid();
        $price = (int) $request->input('total_price');
        $quantity = (int) $request->input('quantity');
        $total_price = $price * $quantity;
        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $total_price,
        ];




        $itemDetails = [
            [
                'id' => $productId,
                'price' => (int) $request->input("total_price"),
                'quantity' => (int) $request->input("quantity"),
                'name' => $productId->name,
            ],
        ];


        $user = auth()->user();
        $customerDetails = [
            'user_id' => $user->id,
            'name' => $user->name,
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
