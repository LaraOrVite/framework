<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| API Routes for LaraOrVite Framework
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Response::json(['status' => 'LaraOrVite Frontend is Ready!']);
});