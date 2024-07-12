<?php

use App\Http\Api\V1\Controllers\EventsController;
use App\Http\Api\V1\Controllers\VenuesController;
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

Route::get('/venues/list', [VenuesController::class, 'list']);

Route::resources([
    'events' => EventsController::class,
    'venues' => VenuesController::class,
], ['only' => ['index', 'show', 'store', 'update', 'destroy']]);


