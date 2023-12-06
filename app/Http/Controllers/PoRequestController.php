<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoRequestController extends Controller
{
    public function SendMail($data) {
        return response()->json(['message' => 'Data received successfully', 'data' => $data]);
    }
}
