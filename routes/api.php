<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



use App\Http\Controllers\PoRequestController as porequest;

Route::POST('/porequest', [porequest::class, 'sendMail']);
Route::GET('/porequest/{status}/{doc_no}/{encrypt}', [porequest::class, 'reqpass']);
Route::POST('/porequest/updatestatus', [porequest::class, 'updateStatus']);

use App\Http\Controllers\StaffActionController as StaffAction;

Route::POST('/staffaction', [StaffAction::class, 'staffaction']);
Route::POST('/fileexist', [StaffAction::class, 'fileexist']);
