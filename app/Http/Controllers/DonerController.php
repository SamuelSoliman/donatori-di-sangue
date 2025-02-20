<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonerController extends Controller
{
    function insertDoner(Request $request)
    {

        /* $data = [
            "name" => $request->input('name'),
            "lastname" => $request->input('lastname'),
            "birthday" => $request->input('birthday'),
            "address" => $request->input('address'),
            "email" => $request->input('email'),
            "sex" => $request->input('sex'),
            "job" => $request->input('job')

        ]; */
        $data = $request->validate([
            "name"=>'required|alpha|max:55',
            "lastname"=>'required|alpha|max:55',
            "birthday"=>'required|date',
            "address"=>'required',
            "email"=>'required|email|unique:doners',
            "sex"=>'required|max:1',
            "job"=>'required|alpha'
        ]);
/*         $data = $request->only(['name','lastname','birthday','address','email','sex','job']); */

       /*  $sql = "INSERT INTO doners (name, lastname, birthday, address, email, sex, job) values (:name, :lastname, :birthday, :address,  :email, :sex, :job)";
        DB::insert($sql, $data); */
        DB::table('doners')->insert($data);
        return ["Message" => "successful creation for doner", 'data'=>$data];
    }

    function searchDoner(Request $request)
    {
        if ($request->has("email")) {
            $query = $request->query("email");
            /* $data = ["email" => $query]; */

            /* $sql = "SELECT * FROM doners WHERE email=:email";
            $doner = DB::SELECT($sql, $data); */

            $doner=DB::table('doners')->select('*')->where('email','=',$query)->get();
            if (empty($doner)) {
                return ["Message" => "this doner email not found "];
            }
            return ["Message" => "this doner email  was found ", "doner data" => $doner[0]];
        }
        elseif ($request->has("name")) {
            $query = $request->query("name");
           /*  $data = ["name" => $query]; */

            /* $sql = "SELECT * FROM doners WHERE name=:name";
            $doner = DB::SELECT($sql, $data); */

            $doner= DB::table('doners')->select('*')->where('name','=',$query)->get();
            if (empty($doner)) {
                return ["Message" => "this doner or doners name not found "];
            }
            return ["Message" => "this doner or doners name was found ", "doner data" => $doner];
        }

        elseif ($request->has("lastname")) {
            $query = $request->query("lastname");
            /* $data = ["lastname" => $query]; */

            /* $sql = "SELECT * FROM doners WHERE lastname=:lastname";
            $doner = DB::SELECT($sql, $data); */

            $doner = DB::table('doners')->select('*')->where('lastname', $query)->get();
            if (empty($doner)) {
                return ["Message" => "this doner or doners lastname not found "];
            }
            return ["Message" => "this doner or doners name was found ", "doner data" => $doner];
        }
        else {
            return ["Message"=> "please specify parameters for search email or name or last name"];
        }


    }

    function showDoners(){
        /* $doners = DB::select("SELECT * FROM doners"); */

        $doners=DB::table('doners')->select()->get();

        if (empty($doners)){
            return ["Message"=>"the doners list is empty"];
        }

        return [$doners];
    }
}
