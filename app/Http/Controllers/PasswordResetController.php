<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    function forgotPassword (Request $request){
       $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email'=> __($status)]);

    }

    function resetPassword (Request $request){
        $status = Password::reset(
            $request->only('email', 'password','token'),
            function (User $user, string $password){
                $user->forceFill([
                    'password'=> Hash::make($password)
                ])->setRememberToken((Str::random(60)));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);


    }
}
