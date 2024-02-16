<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;


Route::get('/', function(){
    return response()->json(['run' => true, 'version' => config('app.version')], Response::HTTP_OK);
});
