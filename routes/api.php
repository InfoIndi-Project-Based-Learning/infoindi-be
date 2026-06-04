<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostLikeController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\OAuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminNotificationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/health', [HealthController::class, 'index']);
Route::get('/statistics', [HealthController::class, 'statistics']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);

// Public posts routes
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{post}', [PostController::class, 'show']);
    Route::get('/{post}/comments', [CommentController::class, 'index']);
});

// Auth routes
Route::prefix('auth')->group(function () {
   Route::post('/register', [AuthController::class, 'register']); 
   Route::post('/login', [AuthController::class, 'login']);
   
   // Google OAuth Routes
   Route::get('/google/redirect', [OAuthController::class, 'redirectToGoogle']);
   Route::get('/google/callback', [OAuthController::class, 'handleGoogleCallback']);
   
   
   // Password Reset Routes
   Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
   Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

   Route::middleware('jwt.auth')->group(function () {
       Route::get('/me', [AuthController::class, 'me']);
       Route::post('/logout', [AuthController::class, 'logout']);
       
       // Email Verification Routes
       Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend']);
       Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
   });
});

// Protected routes (authenticated + must be active)
Route::middleware(['jwt.auth', 'active'])->group(function(){
    
    // === Notifications ===
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // === User-facing routes ===
    Route::prefix('users')->group(function (){
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{user}/profile', [UserController::class, 'profile']);
        Route::get('/{user}/followers', [UserController::class, 'followers']);
        Route::get('/{user}/following', [UserController::class, 'following']);
        Route::get('/{user}/liked-posts', [UserController::class, 'likedPosts']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::post('/{user}/follow/{targetUser}', [UserController::class, 'follow']);
        Route::post('/{user}/unfollow/{targetUser}', [UserController::class, 'unfollow']);
        Route::put('/{user}/profile', [UserController::class, 'updateProfile']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::put('/{user}/password', [UserController::class, 'updatePassword']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    Route::resource('categories', CategoryController::class)
    ->only(['store', 'update', 'destroy']);

    // Posts CRUD (Protected)
    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'store']);
        Route::put('/{post}', [PostController::class, 'update']);
        Route::delete('/{post}', [PostController::class, 'destroy']);

        // Comments
        Route::post('/{post}/comments', [CommentController::class, 'store']);
        Route::delete('/{post}/comments/{comment}', [CommentController::class, 'destroy']);

        // Like
        Route::post('/{post}/like', [PostLikeController::class, 'toggle']);
        Route::get('/{post}/likes', [PostLikeController::class, 'index']);

        // Report (user creates report)
        Route::post('/{post}/report', [ReportController::class, 'store']);

        // Delete additional image
        Route::delete('/{post}/images/{image}', [PostController::class, 'deleteImage']);
    });

    // === Admin-only routes ===
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'overview']);

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index']);
            Route::get('/{user}', [AdminUserController::class, 'show']);
            Route::put('/{user}', [AdminUserController::class, 'update']);
            Route::delete('/{user}', [AdminUserController::class, 'destroy']);
            Route::post('/{user}/ban', [AdminUserController::class, 'ban']);
            Route::post('/{user}/unban', [AdminUserController::class, 'unban']);
        });

        // Report Management
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/{report}', [ReportController::class, 'show']);
            Route::put('/{report}', [ReportController::class, 'update']);
        });

        // Notifications
        Route::post('/notifications/broadcast', [AdminNotificationController::class, 'broadcast']);
    });
});
