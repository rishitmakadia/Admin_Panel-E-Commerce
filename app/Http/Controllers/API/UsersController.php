<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerification;
use App\Models\Users;
use App\Models\UsersAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = Users::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(100),
            'status' => 'Active',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'token' => $user->api_token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $user = Users::where('status', 'active')
            ->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        // regenerate token if needed (optional)
        $user->api_token = Str::random(100);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $user->api_token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->guard('api')->user();

        if ($user) {
            $user->api_token = null;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Already logged out or invalid token',
        ], 401);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'nullable|min:6|max:6',
            'password' => 'nullable|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $user = Users::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        $user->smtp_otp = rand(100000, 999999);
        $user->api_token = Str::random(100);
        $user->save();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'otp' => $user->smtp_otp,
        ];
        Mail::to($request->email)->send(new OtpVerification($data));
        return response()->json([
            'status' => true,
            'message' => 'OTP send successfully',
            'otp' => $user->smtp_otp,
            'api_token' => $user->api_token,
        ]);
    }

    public function setPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'nullable',
//            'email' => 'nullable|email|exists:users,email',
            'otp' => 'required|min:6|max:6',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $user = Users::where('status', 'active')
            ->where('api_token', $request->api_token)
            ->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        if ($user->smtp_otp == $request->otp) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Password same as old password']);
            }
            $user->password = Hash::make($request->password);
            $user->smtp_otp = null;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Password set successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP',
        ]);
    }

    public function setAddress(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            $validator = Validator::make($request->all(), [
                'api_token' => 'nullable',
                'address_id' => 'nullable',
                'pincode' => 'required|min:6|max:6',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'address_line_1' => 'required',
                'address_line_2' => 'nullable',
                'type' => 'nullable',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $user = Users::where('api_token', $token)->first();
//            $user = Users::where('api_token', $request->api_token)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ]);
            }
            if (isset($request->address_id)) {
                $dataAddress = UsersAddress::where('id', $request->address_id)->first();
                // Update only fields provided in request
                $dataAddress->update($request->only([
                    'pincode',
                    'address_line_1',
                    'address_line_2',
                    'city',
                    'state',
                    'country',
                    'type'
                ]));
                return response()->json([
                    'status' => true,
                    'message' => 'Address updated successfully',
                ]);
            }
            $data = UsersAddress::create([
                'user_id' => $user->id,
                'pincode' => $request->pincode,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'type' => '',
                'status' => 'active',
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User Address added successfully',
                'address_id' => $data->id,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid Request',
        ]);
    }

    public function showAddress(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            $validator = Validator::make($request->all(), [
                'address_id' => 'nullable',
            ]);
            $user = auth()->guard('api')->user();
            if(isset($request->address_id)){
                $data = UsersAddress::where('status', 'active')->where('id', $request->address_id)
                    ->where('user_id', $user->id)->first();
                return response()->json([
                    'status' => true,
                    'data' => $data,
                ]);
            }
            $data = UsersAddress::where('status', 'active')
                ->where('user_id', $user->id)->get();
            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function deleteAddress(Request $request){
        $token = $request->bearerToken();
        if ($token) {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',
            ]);
            $user = auth()->guard('api')->user();
            $data = UsersAddress::where('user_id', $user->id)->where('id', $request->address_id)->first();
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Address deleted successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }
}

//| Action       | Method | URL                   | Headers                             | Body Parameters                      |
//| ------------ | ------ | --------------------- | ----------------------------------- | ------------------------------------ |
//| Register     | POST   | `/api/users/register` | none                                | `name`, `email`, `phone`, `password` |
//| Login        | POST   | `/api/users/login`    | none                                | `email`, `password`                  |
//| Logout       | POST   | `/api/users/logout`   | `Authorization: Bearer <api_token>` | none                                 |
//| View Profile | GET    | `/api/users/profile`  | `Authorization: Bearer <api_token>` | none                                 |

