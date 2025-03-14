<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Scopes\DonationsScope;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CenterResource;

class CenterController extends Controller
{
    function insertCenter(Request $request)
    {
        $data = $request->validate([
            "location" => "required|unique:centers,location"
        ]);
        // DB::table('centers')->insert($data);
        Center::insert($data);
        return response()->json(["Message" => "new center is added"], 201);
    }


    function deleteCenter(Request $request)
    {
        $data = $request->validate([
            "location" => "required|exists:centers,location"
        ]);
        //DB::table('centers')->where("location", "=", $data["location"])->delete();
        Center::where("location", "=", $data["location"])->delete();
        return response()->json(["Message" => "the center is deleted "], 200);
    }


    function listCenters(Request $request)
    {
        $per_page = request()->get('perpage', 3);
        $page = $request->get('page', 1);
        $center = Center::query();
        if ($request->has("location")) {
            $query = $request->query("location");
            $center = $center->where("location", "like", $query . '%');
        }
        $results = null;
        if ($request->user()->tokenCan('admin')) {
            // $results = $center->with("donations")->withoutGlobalScope(DonationsScope::class)->get();
            if ($per_page==-1){
                $results = $center->with(["donations" => function ($query) {
                    $query->withoutGlobalScope(DonationsScope::class);
                }])->get();
            }else {
            $results = $center->with(["donations" => function ($query) {
                $query->withoutGlobalScope(DonationsScope::class);
            }])->paginate($per_page, ["*"], "page", $page);
            }
        } else {
            if ($per_page== -1){
                $results = $center->with("donations")->get();
            } else {
                $results = $center->with("donations")->paginate($per_page, ["*"], "page", $page);
            }
        }
        foreach ($results as $result) {
            $searched_center = $result->location;
            $result->count_of_users_per_center = User::where('Center', '=', $searched_center)->count();
        }
        return CenterResource::collection($results);

        // $final_results = ["center_data" => []];
        // $had_params = false;
        // if ($request->has("location")) {
        //     $query = $request->query("location");
        //     $had_params = true;
        //     //$center = DB::table('centers')->select('*')->where('location', '=', $query)->get();
        //     $center = Center::where('location', 'like', $query.'%')->with('donations')->get();
        //     if ($center) {
        //         $final_results['center_data'] = array_merge($final_results["center_data"], $center->toArray());
        //     }
        // }
        // if (!$had_params) {
        // // $centers = DB::table('centers')->select()->with('donations');
        //     $final_results = [];   
        //     $centers =Center::with('donations')->get();
        //     foreach ($centers as $center) {
        //         $searched_center = $center->location;
        //         $count_users = User::where('Center','=',$searched_center)->count();
        //         $center_arr = $center ->toArray();
        //         $center_arr["count_of_users_per_center"]=$count_users;
        //         $final_results[]= $center_arr;
        //     }
        //     return $final_results;
        // } elseif ($had_params && empty($final_results["center_data"])) {
        //     return response()->json(["Message" => "this center isn't found "], 404);
        // } else {
        //     return ["Message" => "this center was found", "data" => $final_results];
        // }
    }

    function showCenter(int $id, Request $request)
    {
        $center = Center::where('id', '=', $id);
        if ($request->user()->tokenCan('admin')) {
            $center = $center->with(["donations" => function ($query) {
                $query->withoutGlobalScope(DonationsScope::class);
            }]);
        } else {
            $center = $center->with('donations');
        }
        $center = $center->first();
        if (!$center) {
            return response()->json(["message" => "center not found"], 404);
        }
        // return response()->json(["data" => $center], 200);
        return new CenterResource($center);
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
        //   DB::table('centers')->where('id', '=', $data['id'])->update($data);
        Center::where('id', '=', $data['id'])->update($data);
        return response()->json(["Message" => 'center update is done successfully'], 200);
    }
}
