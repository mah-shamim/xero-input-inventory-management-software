<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }
    public function homePage()
    {
        return view('welcome');
    }
}
