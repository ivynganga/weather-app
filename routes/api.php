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

Route::get('/create/asset', 'App\Http\Controllers\AssetController@fetch');
Route::get('/get/asset/price', 'App\Http\Controllers\AssetPriceController@update');
Route::get('/get/currencies', [TestController::class, 'index']);
Route::fallback(function() { return response()->json(['code' => 404, 'message' => 'Endpoint does not exist'], 404);});