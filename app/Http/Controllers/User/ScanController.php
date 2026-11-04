<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index(Request $request)
    {
        $code = is_string($request->query('code')) ? trim($request->query('code')) : null;
        $item = $code ? Item::with('category')->where('code', $code)->first() : null;

        return view('user.scan.index', compact('code', 'item'));
    }
}