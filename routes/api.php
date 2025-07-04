<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantController;
use App\Http\Controllers\DisabilityController;
use App\Http\Controllers\FileExportController;
use App\Http\Controllers\StatsController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('user.register');
    Route::post('email-verify', [AuthController::class, 'verifyEmail'])->name('email.verify');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('token/refresh', [AuthController::class, 'refreshToken'])->name('token.refresh');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');
    Route::post('set-new-password', [AuthController::class, 'setNewPassword'])->name('set.new.password');
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout',[AuthController::class,'logout'])->name('logout');
        Route::get('user/profile',[AuthController::class,'currentUserProfile'])->name('logged.in.profile');
    });
});


Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.list.create');
    Route::post('/', [UserController::class, 'store'])->name('user.store');
    Route::get('{id}', [UserController::class, 'show'])->name('user.detail');
    Route::get('blocked', [UserController::class, 'blockedUsers'])->name('blocked.users');
    Route::post('{id}/block', [UserController::class, 'blockUser'])->name('block.user');
    Route::delete('{id}/delete', [UserController::class, 'deleteUser'])->name('delete.user');
    Route::post('{id}/update-password', [UserController::class, 'updatePassword'])->name('update.password');
    Route::get('filter', [UserController::class, 'filter'])->name('user.filter');
});

Route::prefix('warrants')->group(function () {
    Route::get('/', [WarrantController::class, 'index'])->name('warrant.list.create');
    Route::post('/', [WarrantController::class, 'store'])->name('warrant.store');
    Route::get('{id}', [WarrantController::class, 'show'])->name('warrant.detail');
});

Route::prefix('disability-records')->group(function () {
    Route::get('/', [DisabilityController::class, 'index'])->name('disability.record.list.create');
    Route::post('/', [DisabilityController::class, 'store'])->name('disability.record.store');
    Route::get('{id}', [DisabilityController::class, 'show'])->name('disability.record.detail');
    Route::get('filter', [DisabilityController::class, 'filter'])->name('disability.record.filter');
    Route::get('export', [FileExportController::class, 'export'])->name('file.export');
});

Route::get('stats', [StatsController::class, 'index'])->name('stats');