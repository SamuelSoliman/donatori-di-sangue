<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertUserRequest;
use App\Models\Center;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Auth\Events\Registered;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function createAdmin(Request $request){
        $data = [
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'admin'=>true
        ];
        $sql = "INSERT INTO users (name, lastname, email, password, admin) values (:name, :lastname, :email, :password, :admin) ";
        DB::insert($sql, $data);

    }
    function create(InsertUserRequest $request)
    {
       
        $data=$request->validated();
       // $center=Center::where("location","=",$data['center'])->first();

        $data['password']=Hash::make($data['password']);

    DB::table("users")->insert(["name"=>$data['name'],"lastname"=>$data['lastname'],"email"=>$data['email'],"password"=>$data["password"],"center"=>$data['center']]);

        $user = User::where('email', $request->input('email'))->first();
        event(new Registered($user));


        return ["Message"=>"succeful creation for user","data"=>["name"=>$data['name'],"lastname"=>$data["lastname"],"email"=>$data["email"],"center"=>$data['center']]];
    }

    function login(Request $request)
    {

        $loginUserData = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);

       
    $user= User::where('email', $loginUserData['email'])->first();
        if (empty($user)) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if ( Hash::check($loginUserData['password'],$user->password)!=true) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

/*
        Auth::loginUsingId($user[0]->id); */
        $token =$user->createToken($user->name);
        $role='';
        if ($user->admin==true){
            $role="admin";
        }else {
            $role="user";
        }
        return response()->json([
            'message' => 'Login successful',
            'aceess_token'=> $token->plainTextToken,
            'data'=>['name'=>$user->name,"lastname"=>$user->lastname,"email"=>$user->email,"role"=>$role]
        ], 200);
    }

    function logout(User $user)
    {
        $user = auth()->user()->tokens()->delete();;

        return response()->json([
            'message'=>'Logout successful',
        ], 200);
    }

   /*  function forgetPassword(Request $request)
    {

        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);;
    } */
}
