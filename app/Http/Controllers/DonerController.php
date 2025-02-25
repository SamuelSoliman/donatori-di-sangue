<?php

namespace App\Http\Controllers;

use App\Models\Doner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonerController extends Controller
{
    function insertDoner(Request $request)
    {

      
        $data = $request->validate([
            "name"=>'required|alpha|max:55',
            "lastname"=>'required|alpha|max:55',
            "birthday"=>'required|date',
            "address"=>'required',
            "email"=>'required|email|unique:doners',
            "sex"=>'required|max:1|in:m,f',
            "job"=>'required|alpha'
        ]);
       
        DB::table('doners')->insert($data);
        return ["Message" => "successful creation for doner", 'data'=>$data];
    }

    function deleteDoner(Request $request){
        $data = $request -> validate([
            "id"=>'numeric|exists:doners,id',
            "email"=>'email|exists:doners,email'
        ]);
       
        if (array_key_exists("id",$data)){
            Doner::where('id','=',$data['id'])->delete();
            return response()->json(["Message"=>"successful delete for doner"],202);
        }
        else if (array_key_exists("email",$data)){
            Doner::where('email','=',$data['email'])->delete();
            return response()->json(["Message"=>"successful delete for doner"],202);

        }else {
            return response()->json(["error"=>"Must include doner's id or email"],400);
        }
    }

    function searchDoner(Request $request)
    {
        if ($request->has("email")) {
            $query = $request->query("email");
          

            $doner=DB::table('doners')->select('*')->where('email','=',$query)->get();
            if (empty($doner)) {
                return ["Message" => "this doner email not found "];
            }
            return ["Message" => "this doner email  was found ", "doner data" => $doner[0]];
        }
        elseif ($request->has("name")) {
            $query = $request->query("name");
          

            $doner= DB::table('doners')->select('*')->where('name','=',$query)->get();
            if (empty($doner)) {
                return ["Message" => "this doner or doners name not found "];
            }
            return ["Message" => "this doner or doners name was found ", "doner data" => $doner];
        }

        elseif ($request->has("lastname")) {
            $query = $request->query("lastname");
          

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


        /* $doners=DB::table('doners')->select()->get(); */
        $doners=Doner::with('donations')->get();
        
      

        return [$doners];
    }
    function showDoner($id){

        $doner=Doner::find($id);
        if (!$doner){
            return response()->json(["message"=>"doner not found",404]);
        }
        return response()->json(["data"=>$doner],200);
    }


    function updateDoner(Request $request){
        $data = $request->validate([
            'email'=>'required|email|exists:doners,email',
            'name'=>'alpha:ascii|max:50',
            'lastname' => 'alpha:ascii|max:50',
            'birthday' => 'date',
            'address'=>'max:225',
            'city' => 'alpha:ascii|max:50',
            'sex'=> 'max:1,in:m,f',
            'job'=>'alpha:ascii|max:50'
        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the email of the user that it is data needed to be modified and include new values  be modified"], 400);
        }
        DB::table('doners')->where('email', '=', $data['email'])->update($data);
        return response()->json(["Message" => 'doner update is done successfully'], 200);


    }
}
