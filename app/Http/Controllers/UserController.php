<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertUserRequest;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Auth\Events\Registered;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /* function createAdmin(Request $request){
        $data = [
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'admin'=>true
        ];
        $sql = "INSERT INTO users (name, lastname, email, password, admin) values (:name, :lastname, :email, :password, :admin) ";
        DB::insert($sql, $data);

    } */
    function create(InsertUserRequest $request)
    {
        /*  return response()->json($request->input('name'),200); */
        /*         $data=[];
        $data['name']= $request->input('name');
        $data['lastname']= $request->input('lastname');
        $data['email']=$request->input('email');
        $data['password']=$request->input('password'); */

       /*  $data = [
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]; */

       /*  $data = $request->only(['name','lastname','email','password']); */


//in the header of the request must specify Accept:applicationsjson
      /*  $data = $request->validate(
       [
        'name'=>'required|alpha:ascii|max:50',
        'lastname'=>'required|alpha:ascii|max:50',
        'email'=>'required|email|unique:users',
        'password'=> 'required|min:8'
       ]); */
        $data=$request->validated();

        $data['password']=Hash::make($data['password']);

        DB::table("users")->insert($data);

       /*  $sql = "INSERT INTO users (name, lastname, email, password) values (:name, :lastname, :email, :password) ";
        DB::insert($sql, $data); */

        //this line was inserted for the purpose of trying email verification
        $user = User::where('email', $request->input('email'))->first();
        event(new Registered($user));


        return ["Message"=>"succeful creation for user",$data];
    }

    function login(Request $request)
    {

       /*  $email = $request->input('email');
        $password = $request->input('password'); */
        $loginUserData = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);

        /* $sql = "SELECT * FROM users WHERE email = :email";
        $user = DB::select($sql, ['email' => $email]); */
    /*     $user=DB::table("users")->select('*')->where('email','=',$email)->get(); */
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
        return response()->json([
            'message' => 'Login successful',
            'aceess_token'=> $token->plainTextToken
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
