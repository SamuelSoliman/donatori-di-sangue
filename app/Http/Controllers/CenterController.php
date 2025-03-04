<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CenterController extends Controller
{
    function insertCenter(Request $request)
    {
        $data = $request->validate([
            "location" => "required|unique:centers,location"
        ]);
        DB::table('centers')->insert($data);

        return response()->json(["Message" => "new center is added"], 201);
    }


    function deleteCenter(Request $request)
    {
        $data = $request->validate([
            "location" => "required|exists:centers,location"
        ]);
        DB::table('centers')->where("location", "=", $data["location"])->delete();
        return response()->json(["Message" => "the center is deleted "], 200);
    }


    function listCenters(Request $request)
    {
        $final_results = ["center_data" => []];
        $had_params = false;
        if ($request->has("location")) {
            $query = $request->query("location");
            $had_params = true;
            //$center = DB::table('centers')->select('*')->where('location', '=', $query)->get();
            $center = Center::where('location', 'like', $query.'%')->with('donations')->first();
            if ($center) {
                $final_results['center_data'] = array_merge($final_results["center_data"], $center->toArray());
            }
        }
        if (!$had_params) {
            // $centers = DB::table('centers')->select()->with('donations');
            $centers =Center::with('donations')->get();
            return [$centers];
        } elseif ($had_params && empty($final_results["center_data"])) {
            return response()->json(["Message" => "this center isn't found "], 404);
        } else {
            return ["Message" => "this center was found", "data" => $final_results];
        }
    }

    function showCenter(int $id)
    {
        $center = Center::where('id',$id)->with('donations')->first();
        if (!$center) {
            return response()->json(["message" => "center not found"], 404);
        }
        return response()->json(["data" => $center], 200);
    }

    function updateCenter(Request $request)
    {
        $data = $request->validate([
            "id" => 'required|numeric|exists:centers,id',
            "location" => ''
        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the id of the center that it's location needed to be modified and include new values to be modified"], 422);
        }
        DB::table('centers')->where('id', '=', $data['id'])->update($data);
        return response()->json(["Message" => 'center update is done successfully'], 200);
    }
}
