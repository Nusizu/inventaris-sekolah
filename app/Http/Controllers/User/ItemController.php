<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    public function index()
    {
        return view('user.items.index');
    }
}