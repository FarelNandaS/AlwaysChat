<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function userKeys(Request $request) {
        return response()->json([
            'encrypted_private_key' => $request->user()->encrypted_private_key,
            'salt' => $request->user()->salt,
            'iv' => $request->user()->iv,
        ]);
    }
}
