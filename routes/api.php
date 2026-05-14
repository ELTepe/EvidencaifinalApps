<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\RecipeApiController;
use Illuminate\Support\Facades\Route;

Route::post('/tokens', [AuthTokenController::class, 'store']);
Route::get('/recipes', [RecipeApiController::class, 'index']);
Route::get('/recipes/{recipe}', [RecipeApiController::class, 'show']);
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{category}', [CategoryApiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('recipes', RecipeApiController::class)->except(['index', 'show']);
    Route::middleware('role:admin')->apiResource('categories', CategoryApiController::class)->except(['index', 'show']);
});
