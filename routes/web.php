<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DonerController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;




/* Route::get('/', function () {
    return view('welcome');
}); */
/* Route::post('/register-user', [UserController::class,'create'])->middleware(IsAdmin::class);
/* Route::post('/create-admin', [UserController::class,'createAdmin']); */


/* Route::get('/login', [UserController::class,'login'])->name('login');
Route::middleware('auth:sanctum')->get('/logout', [UserController::class, 'logout']);

Route::post('/insert-doner',[DonerController::class,'insertDoner'])->middleware(IsUser::class);
Route::get('/search-doner',[DonerController::class,'searchDoner'])->middleware(IsUser::class);
Route::get('/show-doners',[DonerController::class,'showDoners'])->middleware(IsUser::class);
Route::post('/forgot-password',[PasswordResetController::class,'forgotPassword'] )->middleware('guest')->name('password.email');
Route::post('/reset-password', [PasswordResetController::class,'resetPassword'])->middleware('guest')->name('password.reset'); */

/* Route::post('/forgot-password', [UserController::class, 'forgetPassword'])->middleware('guest')->name('password.email');
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PasswordReset
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update'); */
