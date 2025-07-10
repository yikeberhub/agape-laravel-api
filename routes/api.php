<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantController;
use App\Http\Controllers\DisabilityController;
use App\Http\Controllers\EquipmentController;

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


Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'getUsers'])->name('user.list');
    Route::get('{details/id}', [UserController::class, 'showUserDetail'])->name('user.detail');
    Route::post('{id}/block', [UserController::class, 'blockUser'])->name('block.user');
    Route::delete('{id}/delete', [UserController::class, 'deleteUser'])->name('delete.user');
    Route::post('{id}/update-password', [UserController::class, 'updatePassword'])->name('update.password');
    Route::post('create', [UserController::class, 'createUser'])->name('user.create');
    Route::get('blocked', [UserController::class, 'blockedUsers'])->name('blocked.users');
    Route::get('filter', [UserController::class, 'filter'])->name('user.filter');

});



Route::prefix('warrants')->group(function () {
    Route::get('/', [WarrantController::class, 'index'])->name('warrants.index'); // List all warrants
    Route::get('{id}', [WarrantController::class, 'show'])->name('warrants.show'); // Show a specific warrant
    Route::post('/', [WarrantController::class, 'store'])->name('warrants.store'); // Create a new warrant
});


Route::prefix('equipment')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EquipmentController::class, 'index'])->name('equipment.index'); // List all equipment
    Route::get('{id}', [EquipmentController::class, 'show'])->name('equipment.show'); // Show specific equipment details
    Route::post('/', [EquipmentController::class, 'store'])->name('equipment.store'); // Create a new equipment
    Route::put('{id}', [EquipmentController::class, 'update'])->name('equipment.update'); // Update specific equipment
    Route::delete('{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy'); // Delete specific equipment
});

Route::prefix('disabilities')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DisabilityController::class, 'index'])->name('disabilities.index'); // List all disabilities
    Route::get('{id}', [DisabilityController::class, 'show'])->name('disabilities.show'); // Show specific disability details
    Route::post('/', [DisabilityController::class, 'store'])->name('disabilities.store'); // Create a new disability
    Route::put('{id}', [DisabilityController::class, 'update'])->name('disabilities.update'); // Update a specific disability
    Route::delete('{id}', [DisabilityController::class, 'destroy'])->name('disabilities.destroy'); // Delete a specific disability
    Route::get('filter', [DisabilityController::class, 'filter'])->name('disabilities.filter'); // Filter disabilities
    Route::get('export', [FileExportController::class, 'export'])->name('disabilities.export'); // Export disabilities
});

Route::get('stats', [StatsController::class, 'index'])->name('stats');