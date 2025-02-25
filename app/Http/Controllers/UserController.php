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
    function createAdmin(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'admin' => true
        ];
        $sql = "INSERT INTO users (name, lastname, email, password, admin) values (:name, :lastname, :email, :password, :admin) ";
        DB::insert($sql, $data);
    }
    function user (Request $request){
        return [
            'data' => $request->user()
        ];
    }
    function createUser(InsertUserRequest $request)
    {

        $data = $request->validated();
        // $center=Center::where("location","=",$data['center'])->first();

        $data['password'] = Hash::make($data['password']);
        if (array_key_exists("admin", $data)) {
            DB::table("users")->insert(["name" => $data['name'], "lastname" => $data['lastname'], "email" => $data['email'], "password" => $data["password"], "center" => $data['center'], "admin" => $data['admin']]);
        }else {
            DB::table("users")->insert(["name" => $data['name'], "lastname" => $data['lastname'], "email" => $data['email'], "password" => $data["password"], "center" => $data['center']]);
        }

        $user = User::where('email', $request->input('email'))->first();
        event(new Registered($user));

        $role = isset($data["admin"]) && $data["admin"] == true ? "admin" : "user";
        return ["Message" => "succeful creation for user", "data" => ["name" => $data['name'], "lastname" => $data["lastname"], "email" => $data["email"], "center" => $data['center'], "role" => $role]];
    }
    function listUsers(Request $request){
        $users=DB::table('users')->select('id', 'name', 'lastname', 'email', 'admin')->get();
        return [$users];
    }
    function deleteUser(Request $request)
    {
        $deletedUserEmail = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $deletedUser = DB::table('users')->where('email', '=', $deletedUserEmail)->delete();

        if ($deletedUser) {

            return response()->json(["Message" => "User is deleted successfully"], 200);
        } else {
            return response()->json(["Message" => "User delete failed"], 500);
        }
    }
    function UpdateUser(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'name' => 'alpha:ascii|max:50',
            'lastname' => 'alpha:ascii|max:50',
            'center' => 'string:exists:centers,location',
            'password' => 'min:8'
        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the email of the user that it is data needed to be modified and include new values for center or password or name or lastname or all to be modified"], 400);
        }
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        DB::table('users')->where('email', '=', $data['email'])->update($data);
        return response()->json(["Message" => 'user update is done successfully'], 200);
    }

    function login(Request $request)
    {

        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);


        $user = User::where('email', $loginUserData['email'])->first();
        if (empty($user)) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if (Hash::check($loginUserData['password'], $user->password) != true) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        /*
        Auth::loginUsingId($user[0]->id); */
        $token = $user->createToken($user->name);
        $role = '';
        if ($user->admin == true) {
            $role = "admin";
        } else {
            $role = "user";
        }
        return response()->json([
            // 'message' => 'Login successful',
            'token' => $token->plainTextToken,
            // 'data' => ['name' => $user->name, "lastname" => $user->lastname, "email" => $user->email, "role" => $role]
        ], 200);
    }

    function logout(User $user)
    {
        $user = auth()->user()->tokens()->delete();;

        return response()->json([
            'message' => 'Logout successful',
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
