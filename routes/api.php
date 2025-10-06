<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/anime/search', [AnimeController::class, 'search']);    // ?q=naruto&page=1
Route::get('/anime/top', [AnimeController::class, 'top']);          // ?page=1
Route::get('/anime/{mal_id}', [AnimeController::class, 'show']);
