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
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('currency-rates', [CurrencyApiController::class, 'index']);
    Route::get('fetch-currencies', [CurrencyApiController::class, 'store']);
    Route::get('currency-rates/{date}', [CurrencyApiController::class, 'getCurrencyRatesByDate']);
    Route::post('logout', [AuthController::class, 'logout']);
//    Route::get('excel-export', [CurrencyApiController::class, 'exportExcel']);
});

Route::middleware('auth.api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('excel-export', [CurrencyApiController::class, 'exportExcel']);
