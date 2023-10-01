<?php


use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('register', [AuthController::class, 'register']);

//Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('currency-rates', [CurrencyApiController::class, 'index']);
    Route::get('fetch-currencies', [CurrencyApiController::class, 'store']);
    Route::get('currency-rates/{date}', [CurrencyApiController::class, 'getCurrencyRatesByDate']);
//});



