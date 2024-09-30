<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    // Post routes
    Route::get('posts', [PostController::class, 'index']);
    Route::post('post/new', [PostController::class, 'store']);
    Route::get('post/{post_id}', [PostController::class, 'show']);
    Route::post('post/{post_id}/update', [PostController::class, 'update']);
    Route::delete('post/{post_id}/delete', [PostController::class, 'destroy']);

    // Comment routes
    Route::post('post/{post_id}/comments', [CommentController::class, 'store']);
    Route::delete('post/{post_id}/comments/{comment_id}/delete', [CommentController::class, 'destroy']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/search', [PostController::class, 'search']);

