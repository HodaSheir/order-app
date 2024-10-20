<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['jwt.auth']], function () {

  Route::post('/logout', [AuthController::class, 'logout']);

  Route::get('/user/{user}', [UserController::class, 'show']);

  Route::group(['middleware' => ['role:user']], function () {
      Route::post('/create-order', [OrderController::class, 'store']);
      Route::get('/orders', [OrderController::class, 'index']);
      Route::post('/update-order/{order}', [OrderController::class, 'update']);
      Route::post('getPayment', [OrderController::class, 'getPayment']);
  });

 
});
