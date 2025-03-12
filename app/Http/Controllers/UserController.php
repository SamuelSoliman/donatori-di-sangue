<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

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


    function user(Request $request)
    {
        $role = $request->user()->admin == true ? "admin" : "user";

        return [
            // 'data' => $request->user(),
            'data' => [
                "id" => $request->user()->id,
                "name" => $request->user()->name,
                "lastname" => $request->user()->lastname,
                "email" => $request->user()->email,
                "email_verified_at" => $request->user()->email_verified_at,
                "role" => $role,
                "created_at" => $request->user()->created_at,
                "updated_at" => $request->user()->updated_at,
                "center" => $request->user()->center
            ]

        ];
    }



    function createUser(InsertUserRequest $request)
    {

        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        if (array_key_exists("admin", $data)) {
          //  DB::table("users")->insert(["name" => $data['name'], "lastname" => $data['lastname'], "email" => $data['email'], "password" => $data["password"], "center" => $data['center'], "admin" => $data['admin']]);
            User::insert(["name" => $data['name'], "lastname" => $data['lastname'], "email" => $data['email'], "password" => $data["password"], "center" => $data['center'], "admin" => $data['admin']]);
        } else {
          //  DB::table("users")->insert(["name" => $data['name'], "lastname" => $data['lastname'], "email" => $data['email'], "password" => $data["password"], "center" => $data['center']]);
            User::insert(["name" => $data['name'], "lastname" => $data['lastname'], "email" => $data['email'], "password" => $data["password"], "center" => $data['center']]);
        }

       // $user = User::where('email', $request->input('email'))->first();

        $role = isset($data["admin"]) && $data["admin"] == true ? "admin" : "user";
        return response()->json(["Message" => "successful creation for user", "data" => ["name" => $data['name'], "lastname" => $data["lastname"], "email" => $data["email"], "center" => $data['center'], "role" => $role]], 201);
    }



    function listUsers(Request $request)
    {
        $user=User::query();
        if ($request->has("role")){
            $query = $request->query("role");
            if ($query =="admin"){
                $user=$user->where("admin", '=',1);
            }elseif ($query == "user"){
                $user=$user->where("admin","=",0);
            }
        }
        if ($request->has("email")){
            $query = $request->query("email");
            $user=$user->where("email",'like', $query.'%');
        }
        if ($request->has('name')){
            $query = $request->query('name');
            $user=$user->where('name','like', $query.'%');
        }
        if ($request->has('lastname')){
            $query = $request->query('lastname');
            $user=$user->where('lastname','like', $query.'%');
        }
        $results=$user->get();
        return UserResource::collection($results);
        // $final_results = ["user_data" => []];
        // $had_params = false;
        // $user=collect();
        // if ($request->has("role")) 
        // {
        //     $query= $request->query("role");
        //     $had_params = true;
        //     if ($query=="admin"){
        //         $user= User::where("admin","=", 1)->get()->map(function ($user) {
        //             return [
        //                 'id' => $user->id,
        //                 'name' => $user->name,
        //                 'lastname' => $user->lastname,
        //                 'email' => $user->email,
        //                 'role' => $user->admin == 1 ? "admin" : "user",
        //                 'center' => $user->center
        //             ];
        //         });;
        //     }elseif ($query== "user"){
        //         $user= User::where("admin","=",0)->get()
        //         ->map(function ($user) {
        //             return [
        //                 'id' => $user->id,
        //                 'name' => $user->name,
        //                 'lastname' => $user->lastname,
        //                 'email' => $user->email,
        //                 'role' => $user->admin == 1 ? "admin" : "user",
        //                 'center' => $user->center
        //             ];
        //         });
        //     }
        //     if (!$user->isEmpty()){
        //         $final_results['user_data'] = array_merge($final_results["user_data"], $user->toArray());
        //     }


        // }
        // if ($request->has("email")) {
        //     $query = $request->query('email');
        //     $had_params = true;
        //     $user = DB::table('users')
        //         ->select('id', 'name', 'lastname', 'email', 'admin', 'center')
        //         ->where('email', 'like', $query . '%')
        //         ->get()->map(function ($user) {
        //             return [
        //                 'id' => $user->id,
        //                 'name' => $user->name,
        //                 'lastname' => $user->lastname,
        //                 'email' => $user->email,
        //                 'role' => $user->admin == 1 ? "admin" : "user",
        //                 'center' => $user->center
        //             ];
        //         });
        //     if (!$user->isEmpty()) {
        //         $final_results['user_data'] = array_merge($final_results["user_data"], $user->toArray());
        //     }
        // }
        // if ($request->has("name") && $request->has("lastname")) {
        //     $query_name = $request->query('name');
        //     $query_lastname = $request->query('lastname');
        //     $had_params = true;
        //     $user = DB::table('users')->select('id', 'name', 'lastname', 'email', 'admin', 'center')->where('name', 'like', $query_name . '%')->where('lastname', 'like', $query_lastname . '%')->get()->map(function ($user) {
        //         return [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'lastname' => $user->lastname,
        //             'email' => $user->email,
        //             'role' => $user->admin == 1 ? "admin" : "user",
        //             'center' => $user->center
        //         ];
        //     });
        //     if (!$user->isEmpty()) {
        //         $final_results['user_data'] = array_merge($final_results["user_data"], $user->toArray());
        //         return ["Message" => "this user or users data were found ", "data" => $final_results];
        //     }
        // }

        // if ($request->has("name")) {
        //     $query = $request->query('name');
        //     $had_params = true;
        //     $user = DB::table('users')->select('id', 'name', 'lastname', 'email', 'admin', 'center')->where('name', 'like', $query . '%')->get()->map(function ($user) {
        //         return [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'lastname' => $user->lastname,
        //             'email' => $user->email,
        //             'role' => $user->admin == 1 ? "admin" : "user",
        //             'center' => $user->center
        //         ];
        //     });
        //     if (!$user->isEmpty()) {
        //         $final_results['user_data'] = array_merge($final_results["user_data"], $user->toArray());
        //     }
        // }

        // if ($request->has("lastname")) {
        //     $query = $request->query('lastname');
        //     $had_params = true;
        //     $user = DB::table('users')->select('id', 'name', 'lastname', 'email', 'admin', 'center')->where('lastname', 'like', $query . '%')->get()->map(function ($user) {
        //         return [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'lastname' => $user->lastname,
        //             'email' => $user->email,
        //             'role' => $user->admin == 1 ? "admin" : "user",
        //             'center' => $user->center
        //         ];
        //     });
        //     if (!$user->isEmpty()) {
        //         $final_results['user_data'] = array_merge($final_results["user_data"], $user->toArray());
        //     }
        // }
        // if (!$had_params) {
        //     $users = DB::table('users')->select('id', 'name', 'lastname', 'email', 'admin', 'center')->get()
        //         ->map(function ($user) {
        //             return [
        //                 'id' => $user->id,
        //                 'name' => $user->name,
        //                 'lastname' => $user->lastname,
        //                 'email' => $user->email,
        //                 'role' => $user->admin == 1 ? "admin" : "user",
        //                 'center' => $user->center
        //             ];
        //         });
        //     return [$users];
        // } elseif ($had_params && empty($final_results["user_data"])) {
        //     return response()->json(["Message" => "this user or users name or lastname or email or role wasnt found "], 404);
        // } else {
        //     return ["Message" => "this user or users data were found ", "data" => $final_results];
        // }
    }



    function deleteUser(Request $request)
    {
        $deletedUserEmail = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

       // $deletedUser = DB::table('users')->where('email', '=', $deletedUserEmail)->delete();
        $deletedUser = User::where('email', '=', $deletedUserEmail)->delete();

        if ($deletedUser) {

            return response()->json(["Message" => "User is deleted successfully"], 200);
        } else {
            return response()->json(["Message" => "User delete failed"], 500);
        }
    }


    function UpdateUser(Request $request)
    {
        $data = $request->validate([
            "id" => 'required|exists:users,id',
            "name" => "alpha:ascii|max:50",
            "lastname" => "alpha:ascii|max:50",
            "email" => "email",
            "role" => "alpha|in:user,admin",
            "center" => 'alpha|exists:centers,location',
            'password' => 'min:8'
        ]);
        if (sizeof($data) < 2) {
            return response()
                ->json(["error" => "you must choose the id of the user that it is data needed to be modified and include new values for some camps or all to be modified"], 400);
        }
        $updateData = [];

        if (array_key_exists('name', $data)) {
            $updateData['name'] = $data['name'];
        }
        if (array_key_exists('lastname', $data)) {
            $updateData['lastname'] = $data['lastname'];
        }
        if (array_key_exists('email', $data)) {
            $user = DB::table('users')->where('id', '=', $data["id"])->first();
            if ($user->email != $data['email']) {
                $updateData['email'] = $data['email'];
            }
        }
        if (array_key_exists('role', $data)) {
            $role = $data["role"] == "admin" ? true : false;
            $updateData['admin'] = $role;
        }
        if (array_key_exists('center', $data)) {
            $updateData['center'] = $data['center'];
        }
        if (array_key_exists('password', $data)) {
            $updateData['password'] = Hash::make($data['password']);
        }
      

       // $updated = DB::table('users')->where('id', '=', $data['id'])->update($updateData);
        $updated = User::where('id', '=', $data['id'])->update($updateData);
        if ($updated) {
            return response()->json(["Message" => 'user update is done successfully'], 200);
        } else {
            return response()->json(["Message" => 'user update failed '], 500);
        }
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

        if ($user->admin == 1) {
            $token = $user->createToken($user->email, ['admin']);
        } else {
            $token = $user->createToken($user->email, ['user']);
        }


        return response()->json([
            'token' => $token->plainTextToken,
        ], 200);
    }



    function logout(User $user)
    {
        $user = auth()->user()->tokens()->delete();;

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }




    function showUser(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "user not found"], 404);
        }
        // $data = [];
        // $data['id'] = $user->id;
        // $data['name'] = $user->name;
        // $data['lastname'] = $user->lastname;
        // $data['email'] = $user->email;
        // $role = $user->admin == 1 ? "admin" : "user";
        // $data['role'] = $role;
        // $data['center'] = $user->center;

        // return response()->json(["data" => $data], 200);
        return new UserResource($user);
    }


    function changePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['errors' => 'current password is wrong'], 422);
        }
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);


        return response()->json(['message' => 'Password changed successfully'], 200);
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
