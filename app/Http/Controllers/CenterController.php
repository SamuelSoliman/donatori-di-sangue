<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CenterController extends Controller
{
    function insertCenter(Request $request){
        $data = $request->validate([
            "location"=>"required|unique:centers,location"
        ]);
        DB::table('centers')->insert($data);

        return response()->json(["Message" => "new center is added"], 201);
    }

    function deleteCenter(Request $request){
        $data = $request->validate([
            "location"=>"required|exists:centers,location"
        ]);
        DB::table('centers')->where("location","=",$data["location"])->delete();
        return response()->json(["Message" => "the center is deleted "], 200);

    }

    function listCenters(){
        $centers = DB::table('centers')->select()->get();
        return [$centers];
    }
    
}
