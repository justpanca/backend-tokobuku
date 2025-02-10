<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;



class OrdersController extends Controller
{


    public function index()
    {
        $order = Order::with(["user", "product"])->get();

        return response()->json([
            'message' => 'Order berhasil diTampilkan semua.',
            'data' => $order,
        ], 200);
    }

    public function storeupdate(Request $request)
    {

        Log::info('OrderController store method called.', ['request' => $request->all()]);

        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',


        ], [
            'required' => 'input :attribute harus diisi!.',

        ]);
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;


        $user = auth()->user();

        $order = new Order();
        $orderId = uniqid();

        $order->product_id = $request->input('product_id');
        $order->first_name = $request->input('first_name');
        $order->last_name = $request->input('last_name');
        $order->user_id = $user->id;
        $order->order_id = $orderId;
        $order->total_price = $request->input('total_price');
        $order->quantity = $request->input('quantity');
        $order->address = $request->input('address');

        $order->save();



        $transactionDetails = [
            'order_id' => $order->order_id,
            'gross_amount' => $order->total_price,
        ];




        $itemDetails = [
            [
                'id' => $order->product_id,
                'name' => $order->product->name,
                'price' => $order->total_price,
                'quantity' => $order->quantity,

            ],
        ];



        $customerDetails = [
            'user_id' => $user->id,
            'first_name' => $order->first_name,
            'last_name' => $order->last_name,
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
