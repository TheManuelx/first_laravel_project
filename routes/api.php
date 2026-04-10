<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

//Route API Example
Route::get('/hello', function() {
    return response()->json(['message' => 'Hello World']);
});

//Register
Route::post('register', [ApiController::class, 'register']);

//Login
Route::post('login', [ApiController::class, 'login']);

Route::middleware(['auth:sanctum', 'abilities:access-api'])->group(function() {
    //Logout
    Route::post('logout', [ApiController::class, 'logout']);
    Route::get('users', [ApiController::class, 'index']);

    //Refresh Token
    Route::post('refresh', [ApiController::class, 'refresh']);

    Route::get('/users', function (Request $request) {
        return $request->user();
    });

});

Route::middleware('auth:sanctum')->get('/me', [ApiController::class, 'me']);

//Refresh
Route::middleware(['auth:sanctum', 'abilities:refresh-api'])->group(function () {
    Route::post('refresh', [ApiController::class, 'refresh']);
});

