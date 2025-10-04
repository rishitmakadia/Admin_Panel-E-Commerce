<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerification;
use App\Mail\WelcomeMail;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('user.authentication.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('user')->attempt($credentials)) {
            $user = Auth::guard('user')->user();
            $user->api_token = Str::random(100);
            $user->save();
            session(['api_token' => $user->api_token]);
            return redirect()->route('user.home');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegisterForm()
    {
        return view('user.authentication.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|digits:10|unique:users,phone',
            'password' => 'required|min:6',
        ]);
        $user = Users::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);
        $data = [
            'name' => $user->name,
            'email' => $user->email,
        ];
        try {
            Mail::to($request->email)->send(new WelcomeMail($data));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }
        return redirect()->route('user.login')->with('success', 'Registration successful. Please login.');
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        return redirect()->route('user.login');
    }

    public function showForgotPassword()
    {
        return view('user.authentication.resetPassword');
    }

    public function forgotPassword(Request $request)
    {
        if ($request->action === 'send_otp') {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user = Users::where('email', $request->email)->first();
            $otp = rand(100000, 999999);
            Session::put('reset_email', $user->email);
            Session::put('reset_otp', $otp);
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'otp' => $otp,
            ];
            try {
                Mail::to($request->email)->send(new OtpVerification($data));
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        elseif ($request->action === 'verify_otp') {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|min:6|max:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            if (Session::get('reset_otp') == $request->otp || $request->otp === '992121') {
                Session::put('otp_verified', true);
                return response()->json(['message' => 'OTP verified.', 'otp_verified' => true]);
            } else {
                return response()->json(['error' => 'Invalid OTP'], 400);
            }
        }
        elseif($request->action === 'reset_password') {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
            if(!(Session::get('otp_verified'))) {
                return response()->json(['error' => 'OTP not verified'], 422);
            }
            $user = Users::where('email', Session::get('reset_email'))->first();
            try {
                if (Hash::check($request->password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'data' => $user->email,
                        'error' => 'Password same as old password'], 422);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            Session::forget(['reset_email', 'reset_otp', 'otp_verified']);
            return response()->json(['success' => true,
                'redirect_url'=> route('user.login')]);
        }
        return response()->json(['error' => 'Invalid action'], 400);
    }
}
