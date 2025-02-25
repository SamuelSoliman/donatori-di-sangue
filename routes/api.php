<?php

use App\Http\Controllers\CenterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonerController;
use App\Models\Center;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use GuzzleHttp\Middleware;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


Route::post('/create-admin', [UserController::class, 'createAdmin']);
Route::middleware(IsAdmin::class)->group(function () {

    Route::post('/register-user', [UserController::class, 'createUser']);
    Route::delete('/delete-user', [UserController::class, 'deleteUser']);
    Route::put('/update-user', [UserController::class, 'updateUser']);
    Route::get('/list-users', [UserController::class, 'listUsers']);
    Route::delete('/delete-doner', [DonerController::class, 'deleteDoner']);
    Route::put('/update-doner', [DonerController::class, 'updateDoner']);
    Route::delete('/delete-center', [CenterController::class, 'deleteCenter']);
    Route::Post('/insert-center', [CenterController::class, 'insertCenter']);
    Route::get('/admin-dashboard', [DashboardController::class,'adminDahsboard']);
});





Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->middleware(['auth:sanctum']);

/* Route::middleware('auth:sanctum')->get('/logout', [UserController::class, 'logout']); */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',[UserController::class, 'user']);
    Route::post('/insert-doner', [DonerController::class, 'insertDoner']);
    Route::get('/doner/{id}', [DonerController::class, 'showDoner']);
    Route::get('/search-doner', [DonerController::class, 'searchDoner']);
    Route::get('/show-doners', [DonerController::class, 'showDoners']);
    Route::post('/create-donation', [DonationController::class, 'createDonation']);
    Route::get('/show-donations',[DonationController::class, 'showDonations']);
    RoutE::put('/update-donation',[DonationController::class, 'updateDonation']);
    Route::get('/list-centers', [CenterController::class,'listCenters']);
    
    
});

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
