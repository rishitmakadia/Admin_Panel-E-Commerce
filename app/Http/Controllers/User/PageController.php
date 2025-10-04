<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(){
        return view('user.home');
    }
    public function profile(){
        return view('user.pages.profile');
    }
    public function electronics(){
        return view('user.pages.electronics');
    }

    public function checkout(){
        return view('user.pages.checkout');
    }
}
