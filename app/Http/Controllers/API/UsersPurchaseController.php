<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ElectronicsPurchase;
use App\Models\ElectronicsSubCategory;
use App\Models\Users;
use App\Models\UsersCart;
use App\Models\UsersPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersPurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
        ]);
        try {
            $user = Users::where('api_token', $request->api_token)->first();
            $data = UsersCart::where('user_id', $user->id)
                ->where('status', 'pending')
                ->get();
            if (empty($data)) {
                return response()->json([
                    'message' => 'No Item in Cart for User'
                ]);
            }
            foreach ($data as $item) {
                UsersPurchase::create([
                    'user_id' => $item->user_id,
                    'product_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
//                    'status' => ''
                ]);
                $item->status = 'purchased';
                $item->save();
            }
            return response()->json([
                'message' => 'Purchased Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function addCartItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'nullable',
            'item_type' => 'required',
            'itemID' => 'required',
            'api_token' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $user = Users::where('api_token', $request->api_token)->first();
        if ($request->item_type == 'Electronics') {
            $data = ElectronicsSubCategory::where('id', $request->itemID)->first();
        }
        if (empty($data)) {
            return response()->json([
                'message' => 'Sorry! Item Not Found'
            ]);
        }
        $cartItem = UsersCart::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('item_id', $data->id)
            ->first();
        if ($cartItem) {
            if (isset($request->quantity)) {
                if($request->quantity == 0){
                    $cartItem->delete();
//                    $this->showCartItems($request);
                    return response()->json([
                        'message' => 'Item Deleted Successfully'
                    ]);
                }
                $cartItem->quantity = $request->quantity;
            } else {
                $cartItem->quantity = 1;
            }
            $cartItem->save();
            $data2 = $cartItem;
        } else {
            $data2 = UsersCart::create([
                'user_id' => $user->id,
                'item_id' => $data->id,
                'item_type' => $request->item_type,
                'quantity' => $request->quantity ?? 1,
                'price' => $data->subCategory_price,
                'status' => 'pending',
            ]);
        }
        return response()->json([
            'data' => $data2
        ]);
    }

    public function showCartItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ]);
        }
        $user = Users::where('api_token', $request->api_token)->first();
        $data = UsersCart::where('user_id', $user->id)
            ->where('status', 'pending')
            ->join('electronics_sub_category', 'electronics_sub_category.id', '=', 'users_cart.item_id')
            ->select('users_cart.*', 'electronics_sub_category.subCategory_name', 'electronics_sub_category.subCategory_photo')
            ->get();
        $data->transform(function ($item) {
            if ($item->subCategory_photo) {
                $item->subCategory_photo = asset('storage/' . $item->subCategory_photo);
            }
            return $item;
        });
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'USER! Item Not Found',
                'count' => 0
            ]);
        }
        return response()->json([
            'data' => $data,
            'count' => count($data)
        ]);
    }
}
