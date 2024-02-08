<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPhotoController;
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

Route::resource('/users', UserController::class);
Route::post('/users/login', [UserController::class, 'login']);
Route::post('/users/token-validation', [UserController::class, 'tokenValidation']);
Route::resource('/user-photo', UserPhotoController::class);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
