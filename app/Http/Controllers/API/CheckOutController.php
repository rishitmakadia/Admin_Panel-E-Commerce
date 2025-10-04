<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OrderList;
use App\Models\Payment;
use App\Models\Users;
use App\Models\UsersAddress;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade
use Illuminate\Support\Facades\Log; // <-- Import Log Facade for debugging
use App\Models\UsersCart;
use App\Models\UsersPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class CheckOutController extends Controller
{
    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'address_id' => 'nullable',
            'subtotal' => 'nullable',
            'shipping' => 'nullable',
            'tax' => 'nullable',
            'discount' => 'nullable',
            'totalAmount' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Users::where('api_token', $request->api_token)->where('status', 'active')->first();
        $cartItems = UsersCart::where('user_id', $user->id)->where('status', 'pending')->get();
        DB::beginTransaction();
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $orderRazorpay = $api->order->create([
                'receipt' => 'rcpt_' . uniqid(),
                'amount' => $request->totalAmount * 100,
                'currency' => 'INR',
            ]);
            $payment = Payment::create([
                'razorpay_order_id' => $orderRazorpay->id,
                'status' => 'pending',
                'amount' => $request->totalAmount,
                'currency' => 'INR',
                'receipt' => $orderRazorpay->receipt,
            ]);
            $order = OrderList::create([
                'receipt' => $orderRazorpay->receipt,
                'user_id' => $user->id,
                'payment_id' => $payment->id, // ✅ Correctly link to the payment record's primary key
                'user_address' => $request->address_id,
                'totalAmount' => $request->totalAmount,
                'subTotal' => $request->subtotal,
                'shipping' => $request->shipping,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'status' => 'pending'
            ]);

            foreach ($cartItems as $cartItem) {
                UsersPurchase::create([
                    'order_id' => $order->id,
                    'user_id' => $cartItem->user_id,
                    'product_id' => $cartItem->item_id,
                    'product_type' => $cartItem->item_type,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total_price' => $cartItem->price * $cartItem->quantity,
                    'status' => 'checked_out'
                ]);
                $cartItem->status = 'checked_out';
                $cartItem->save();
            }
            DB::commit(); // All good, save the changes
            return response()->json([
                'id' => $orderRazorpay->id,
                'amount' => $orderRazorpay->amount,
                'currency' => $orderRazorpay->currency,
            ]);

        } catch (\Exception $exception) {
            DB::rollBack(); // Something went wrong, undo all database changes
            Log::error('Payment Creation Failed: ' . $exception->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not process your order.'], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $signature = $request->razorpay_signature;
        $paymentId = $request->razorpay_payment_id;
        $orderId = $request->razorpay_order_id;

        $payment = Payment::where('razorpay_order_id', $orderId)->first();
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Invalid Order ID'], 404);
        }

        DB::beginTransaction();
        try {
            $generatedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, env('RAZORPAY_SECRET'));

            if ($generatedSignature !== $signature) {
                throw new \Exception('Invalid Razorpay Signature');
            }

            $payment->payment_id = $paymentId;
            $payment->signature = $signature;
            $payment->status = 'paid';
            $payment->save();

            $orderList = OrderList::where('payment_id', $payment->id)->firstOrFail();
            $orderList->status = 'success';
            $orderList->save();

            UsersPurchase::where('order_id', $orderList->id)->update(['status' => 'success']);

            $purchasedItems = UsersPurchase::where('order_id', $orderList->id)->get();
            foreach($purchasedItems as $item) {
                UsersCart::where('user_id', $item->user_id)
                    ->where('item_id', $item->product_id)
                    ->where('item_type', $item->product_type)
                    ->where('status', 'checked_out')
                    ->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment successful!']);

        } catch (\Exception $e) {
            DB::rollBack();

            $payment->status = 'failed';
            $payment->save();

            $orderList = OrderList::where('payment_id', $payment->id)->first();
            if ($orderList) {
                $orderList->status = 'failed';
                $orderList->save();
                UsersPurchase::where('order_id', $orderList->id)->update(['status' => 'failed']);

                $failedItems = UsersPurchase::where('order_id', $orderList->id)->get();
                foreach($failedItems as $item) {
                    UsersCart::where('user_id', $item->user_id)
                        ->where('item_id', $item->product_id)
                        ->where('item_type', $item->product_type)
                        ->where('status', 'checked_out')
                        ->update(['status' => 'pending']);
                }
            }
            Log::error("Verification Failed: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
