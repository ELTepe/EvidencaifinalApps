<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class, 'index'])->name('home');

Route::get('/dashboard', [RecipeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('recipes', RecipeController::class)->only(['index', 'show']);

Route::middleware('auth')->group(function () {
    Route::resource('recipes', RecipeController::class)->except(['index', 'show']);
    Route::post('recipes/{recipe}/comments', [CommentController::class, 'store'])->name('recipes.comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
});

require __DIR__.'/auth.php';
