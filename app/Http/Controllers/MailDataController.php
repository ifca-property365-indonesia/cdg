<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailDataController extends Controller
{
    public function receive(Request $request)
    {
        if ($request->type == "Q" && $request->module == "PO") {
            return "PO REQUEST";
        } else if ($request->type == "A" && $request->module == "PO"){
            return "PO ORDER";
        } else {
            return "ELSE";
        }
    }
}
