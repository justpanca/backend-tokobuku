<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Midtrans;

class PaymentController extends Controller
{
    public function createTransaction(Request $request)
    {
        // Set konfigurasi Midtrans
        Midtrans\Config::$serverKey = config('app.midtrans.server_key');
        Midtrans\Config::$isProduction = config('app.midtrans.is_production');
        Midtrans\Config::$isSanitized = true;
        Midtrans\Config::$is3ds = true;

        // Membuat order ID unik
        $orderId = uniqid();

        // Data transaksi
        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $request->input("price") * $request->input("quantity"), // Harga total
        ];

        $itemDetails = [
            [
                'id' => $request->input("idProduct"),
                'price' => $request->input("price"),
                'quantity' => $request->input("quantity"),
                'name' => $request->input("name"),
            ],
        ];

        $customerDetails = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'customer@example.com',
            'phone' => '081234567890',
        ];

        $transaction = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        try {
            $snapToken = Midtrans\Snap::getSnapToken($transaction);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
