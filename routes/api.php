<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantController;
use App\Http\Controllers\DisabilityController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\FileExportController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('user.register');
    Route::get('email-verify', [AuthController::class, 'verifyEmail'])->name('email.verify');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('token/refresh', [AuthController::class, 'refreshToken'])->name('token.refresh');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');
    Route::post('set-new-password', [AuthController::class, 'setNewPassword'])->name('set.new.password');
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout',[AuthController::class,'logout'])->name('logout');
    });
});


Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('profile', [AuthController::class, 'currentUserProfile'])->name('users.profile');
    Route::get('blocked', [UserController::class, 'blocked'])->name('users.blocked');
    Route::get('filter', [UserController::class, 'filter'])->name('users.filter');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::post('{id}/block', [UserController::class, 'block'])->name('users.block');
    Route::delete('{id}/delete', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('{id}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    Route::get('{id}', [UserController::class, 'show'])->name('users.show'); 
});



Route::prefix('warrants')->group(function () {
    Route::get('/', [WarrantController::class, 'index'])->name('warrants.index'); 
    Route::get('{id}', [WarrantController::class, 'show'])->name('warrants.show'); 
    Route::post('/', [WarrantController::class, 'store'])->name('warrants.store');
});


Route::prefix('equipment')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EquipmentController::class, 'index'])->name('equipment.index'); 
    Route::get('{id}', [EquipmentController::class, 'show'])->name('equipment.show'); 
    Route::post('/', [EquipmentController::class, 'store'])->name('equipment.store'); 
    Route::put('{id}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
});

Route::prefix('disabilities')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DisabilityController::class, 'index'])->name('disabilities.index'); 
    Route::get('{id}', [DisabilityController::class, 'show'])->name('disabilities.show'); 
    Route::post('/', [DisabilityController::class, 'store'])->name('disabilities.store'); 
    Route::put('{id}', [DisabilityController::class, 'update'])->name('disabilities.update');
    Route::patch('{id}', [DisabilityController::class, 'update'])->name('disabilities.partial_update'); 
    Route::delete('{id}', [DisabilityController::class, 'destroy'])->name('disabilities.destroy'); 

    Route::get('filter', [DisabilityController::class, 'filter'])->name('disabilities.filter'); 
    Route::get('search', [DisabilityController::class, 'search'])->name('disabilities.search');
    Route::post('export', [FileExportController::class, 'export'])->name('disabilities.export');

});

Route::prefix('admin')->middleware('auth:sanctum')->group(function(){
 Route::get('stats', [AdminController::class, 'stat'])->name('admin.stat');
 Route::get('users/search', [AdminController::class, 'userSearch'])->name('admin.user.search');
});