<?php

use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;


Route::get('/', function(){
    return response()->json(['run' => true, 'version' => config('app.version')], Response::HTTP_OK);
});

Route::post('/user', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'getAll']);
Route::get('/user/{userId}', [UserController::class, 'get']);
Route::patch('/user/{userId}', [UserController::class, 'update']);
Route::delete('/user/{userId}', [UserController::class, 'destroy']);

Route::post('/deposit', [DepositController::class, 'new']);
Route::post('/transfer', [TransactionController::class, 'transfer']);
