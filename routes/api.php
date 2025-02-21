<?php

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
use Illuminate\Support\Facades\DB;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register-user', [UserController::class,'create'])->middleware(IsAdmin::class);
Route::post('/login', [UserController::class,'login'])->name('login');
Route::post('/logout',[UserController::class, 'logout'])->middleware(['auth:sanctum']);


Route::post('/insert-center',function(Request $request){
 
    $data=$request->input('location');
    DB::table('centers')->insert([
        'location'=>$data
    ]);

    return response()->json(["Message"=>"new center is added"],201);

})->middleware(IsAdmin::class);
Route::post('/create-admin', [UserController::class,'createAdmin']);


Route::post('/login', [UserController::class,'login'])->name('login');
/* Route::middleware('auth:sanctum')->get('/logout', [UserController::class, 'logout']); */
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/insert-doner',[DonerController::class,'insertDoner']);
    Route::get('/doner/{id}',[DonerController::class,'showDoner']);
    Route::get('/search-doner',[DonerController::class,'searchDoner']);
    Route::get('/show-doners',[DonerController::class,'showDoners']);
   
});
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/create-donation',[DonationController::class,'createDonation']);
   
});

Route::post('/forgot-password',[PasswordResetController::class,'forgotPassword'] )->middleware('guest')->name('password.email');
Route::post('/reset-password', [PasswordResetController::class,'resetPassword'])->middleware('guest')->name('password.reset');
