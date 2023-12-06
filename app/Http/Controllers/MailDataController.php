<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailDataController extends Controller
{
    public function receive(Request $request)
    {
        $dataFromExternal = $request->all();
        if ($request->type == "Q" && $request->module == "PO") {
            return ControllerB::processData($dataFromExternal);
        } else if ($request->type == "A" && $request->module == "PO"){
            return "PO ORDER";
        } else {
            return "ELSE";
        }
    }
}
