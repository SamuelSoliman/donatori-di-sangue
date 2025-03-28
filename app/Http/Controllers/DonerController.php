<?php

namespace App\Http\Controllers;

use App\Http\Resources\DonerResource;
use App\Models\Doner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Scopes\DonationsScope;

class DonerController extends Controller
{
    function insertDoner(Request $request)
    {
        $data = $request->validate([
            "name" => 'required|alpha|max:55',
            "lastname" => 'required|alpha|max:55',
            "birthday" => 'required|date',
            "address" => 'required',
            "email" => 'required|email|unique:doners',
            "sex" => 'required|max:1|in:M,F',
            "job" => 'required|alpha'
        ]);
        $age = Carbon::parse($data['birthday'])->age;

        if ($age >= 18) {
            //$insertion = DB::table('doners')->insert($data);
            $insertion = Doner::insert($data);
            if ($insertion) {
                return response()->json(["Message" => "successful creation for doner", 'data' => $data], 201);
            } else {
                return response()->json(["Message" => "creation of doner failed"], 500);
            }
        } else {
            return response()->json(["Message" => "Age of doner must be equal or greater to 18"], 422);
        }
    }

    function deleteDoner(Request $request)
    {
        $data = $request->validate([
            "id" => 'numeric|exists:doners,id',
            "email" => 'email|exists:doners,email'
        ]);

        if (array_key_exists("id", $data)) {
            Doner::where('id', '=', $data['id'])->delete();
            return response()->json(["Message" => "successful delete for doner"], 202);
        } else if (array_key_exists("email", $data)) {
            Doner::where('email', '=', $data['email'])->delete();
            return response()->json(["Message" => "successful delete for doner"], 202);
        } else {
            return response()->json(["error" => "Must include doner's id or email"], 400);
        }
    }



    function showDoners(Request $request)
    {

        $per_page = request()->get('perpage', 3);
        $page = $request->get('page', 1);
        $sort_by = $request->query('sortBy', 'name');
        $sort_order= $request->query('sortDesc',true);
        $sort_order= $sort_order == "false"? false:true;
        $direction= $sort_order==true ?'desc':'asc';
        $doner = Doner::query();
        if ($request->has("email")) {
            $query = $request->query("email");
            $doner = $doner->where("email", "like", $query . '%');
        }
        if ($request->has("name")) {
            $query = $request->query("name");
            $doner = $doner->where("name", "like", $query . '%');
        }
        if ($request->has("lastname")) {
            $query = $request->query("lastname");
            $doner = $doner->where("lastname", "like", $query . '%');
        }

        if ($request->user()->tokenCan('admin')) {
            // $results = $center->with("donations")->withoutGlobalScope(DonationsScope::class)->get();
            if ($per_page == -1) {
                $results = $doner->with(["donations" => function ($query) {
                    $query->withoutGlobalScope(DonationsScope::class);
                }])->withCount(["donations" => function ($query) {
                    $query->withoutGlobalScope(DonationsScope::class);
                }])->orderBy($sort_by,$direction)->get();
            } else {
                $results = $doner->with(["donations" => function ($query) {
                    $query->withoutGlobalScope(DonationsScope::class);
                }])->withCount(["donations" => function ($query) {
                    $query->withoutGlobalScope(DonationsScope::class);
                }])->orderBy($sort_by,$direction)->paginate($per_page, ["*"], "page", $page);
            }
        } else {
            // $perpage = request()->get('perpage', 3);
            if ($per_page == -1) {
                $results = $doner->with("donations")->withCount("donations")->orderBy($sort_by,$direction)->get();
            } else {
                $results = $doner->with("donations")->withCount("donations")->orderBy($sort_by,$direction)->paginate($per_page, ["*"], "page", $page);
            }
        }
        return DonerResource::collection($results);
        // $center = Center::where("id", $request->id);

        // if ($filterX) {
        //     $center = $center->whereX();
        // }

        // if ($filterY) {
        //     $center = $center->whereY();
        // }

        // $result = $center->get();



        // $final_results = ["doner_data" => []];
        // $had_params = false;

        // if ($request->has("email")) {
        //     $query = $request->query("email");

        //     $had_params = true;
        //     $doner = Doner::where('email','like', $query . '%')->get();
        //     if (!$doner->isEmpty()) {

        //         $final_results['doner_data'] = array_merge($final_results["doner_data"], $doner->toArray());
        //     }
        // }

        // if ($request->has("name") && $request->has("lastname")) {
        //     $had_params = true;
        //     $query_name = $request->query("name");
        //     $query_lastname = $request->query("lastname");

        //    // $doner = DB::table('doners')->select('*')->where('name', 'like', $query_name . '%')->where('lastname', 'like', $query_lastname . '%')->get();
        //     $doner = Doner::where('name','like', $query_name . '%')
        //     ->where('lastname', 'like', $query_lastname.'%')->get();
        //     if (!$doner->isEmpty()) {

        //         $final_results['doner_data'] = array_merge($final_results["doner_data"], $doner->toArray());
        //         return ["Message" => "this doner or doners data were found ", "doner_data" => $final_results];
        //     }
        // }
        // if ($request->has("name")) {
        //     $had_params = true;
        //     $query = $request->query("name");
        //     //$doner = DB::table('doners')->select('*')->where('name', 'like', $query . '%')->get();
        //     $doner = Doner::where('name', 'like', $query . '%')->get();
        //     if (!$doner->isEmpty()) {

        //         $final_results['doner_data'] = array_merge($final_results["doner_data"], $doner->toArray());
        //     }
        // }
        // if ($request->has("lastname")) {
        //     $query = $request->query("lastname");
        //     $had_params = true;

        //     //$doner = DB::table('doners')->select('*')->where('lastname', 'like', $query . '%')->get();
        //     $doner = Doner::where('lastname', 'like', $query . '%')->get();
        //     if (!$doner->isEmpty()) {

        //         $final_results['doner_data'] = array_merge($final_results["doner_data"], $doner->toArray());
        //     }
        // }

        // if (!$had_params) {
        //     $doners = Doner::with('donations')->get();

        //     return [$doners];
        // } elseif ($had_params && empty($final_results['doner_data'])) {
        //     return response()->json(["Message" => "this doner or doners name or lastname or password wasnt found "], 404);
        // } else {
        //     return ["Message" => "this doner or doners data were found ", "doner_data" => $final_results];
        // }
    }




    function showDoner($id, Request $request)
    {

        // $doner = Doner::where('id', $id)->with('donations')->first();
        $doner = Doner::where("id", '=', $id);
        if ($request->user()->tokenCan('admin')) {
            // $results = $center->with("donations")->withoutGlobalScope(DonationsScope::class)->get();
            $doner = $doner->with(["donations" => function ($query) {
                $query->withoutGlobalScope(DonationsScope::class);
            }]);
        } else {
            $doner = $doner->with("donations");
        }
        $doner = $doner->first();
        if (!$doner) {
            return response()->json(["message" => "doner not found"], 404);
        }
        return new DonerResource($doner);
        // $doner_donations_count = Doner::find($id)->donations()->count();
        // $doner_last_donations_date = Doner::find($id)->donations()->orderByDesc('donation_date')->first();
        // $final_results["doner"] = $doner->toArray();
        // $final_results["donations_count"] = $doner_donations_count;
        // if ($doner_last_donations_date) {
        //     $final_results["last_donation_date"] = $doner_last_donations_date->donation_date;
        // }
        // return response()->json([$final_results], 200);
    }


    function updateDoner(Request $request)
    {

        $data = $request->validate([
            "id" => 'required|numeric|exists:doners,id',
            "name" => 'alpha:ascii|max:50',
            "lastname" => 'alpha:ascii|max:50',
            "birthday" => 'date',
            "address" => 'max:225',
            "email" => "email",
            "sex" => "max:1,in:M,F",
            "job" => "alpha:ascii|max:50",
            // "created_at" => 'date',
            // "updated_at" => 'date',
        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the id of the user that it's data needed to be modified and include new values be modified"], 422);
        }
        // DB::table('doners')->where('id', '=', $data['id'])->update($data);
        Doner::where('id', '=', $data['id'])->update($data);
        return response()->json(["Message" => 'doner update is done successfully'], 200);
    }
}
