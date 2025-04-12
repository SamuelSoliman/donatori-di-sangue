<?php

namespace App\Http\Controllers;


use App\Models\Donation;
use App\Models\Scopes\DonationsScope;
use Illuminate\Http\Request;
use App\Http\Resources\DonationResource;



class DonationController extends Controller
{
    function createDonation(Request $request)
    {
        $data = $request->validate([
            "doner_email" => 'required|email|exists:doners,email',
            "center" => 'required|exists:centers,location',
            "donation_date" => 'required|date'
        ]);
        if ($request->user()->tokenCan('admin')) {
            $donation = Donation::insert($data);
        } else {
            if ($request->user()->center != $data["center"]) {
                return response()->json(["Message" => "User cant insert donations in center doesnt belong to him"], 401);
            } else {
                $donation = Donation::insert($data);
            }
        }
        if ($donation) {
            return response()->json(["Message" => "donation creation is complete"], 200);
        } else {
            return response()->json(["Message" => "donation creation failed"], 500);
        }
    }

    function showDonations(Request $request)
    {
        $per_page = request()->get('perpage', 3);
        $page = $request->get('page', 1);
        $sort_by = $request->query('sortBy', 'doner_email');
        $sort_order= $request->query('sortDesc',true);
        $sort_order= $sort_order == "false"? false:true;
        $direction= $sort_order==true ?'desc':'asc';
        $donation = Donation::query();
        if ($request->user()->tokenCan("admin")) {
            $donation = $donation->withoutGlobalScope(DonationsScope::class);
        }
        if ($request->has("doner_email")) {
            $query = $request->query("doner_email");
            $donation = $donation->where("doner_email", "LIKE", $query . "%");
        }
        if ($request->has("donation_date")) {
            $query = $request->query("donation_date");
            $donation = $donation->where("donation_date", "=", $query);
        }
        if ($request->has("center")) {
            $query = $request->query("center");
            $donation = $donation->where("center", "LIKE", $query . '%');
        }
        if ($per_page == -1) {
            $results = $donation->orderBy($sort_by,$direction)->get();
        } else {
            $results = $donation->orderBy($sort_by,$direction)->paginate($per_page, ["*"], "page", $page);
        }
        return DonationResource::collection($results);

        // $final_results = ["donations_data" => []];
        // $had_params = false;
        // $search_data = $request->validate([
        //     "doner_email" => "email",
        //     "donation_date" => "date",
        //     "center" => "alpha"
        // ]);
        // if (array_key_exists('doner_email', $search_data)) {
        //     $query = $search_data["doner_email"];
        //     $had_params = true;
        //     //  $donation = DB::table('donations')->select('*')->where('doner_email', 'like', $query.'%')->get();
        //     $donation= Donation::where('doner_email', 'like', $query.'%')->get();

        //     if (!$donation->isEmpty()) {
        //         $final_results['donations_data'] = array_merge($final_results["donations_data"], $donation->toArray());
        //     }
        // }
        // if (array_key_exists('donation_date', $search_data)) {
        //     $query = $search_data["donation_date"];
        //     $had_params = true;
        //     //$donation = DB::table('donations')->select('*')->where('donation_date', '=', $query)->get();
        //     $donation = Donation::where("donations_date", "=", $query)->get();
        //     if (!$donation->isEmpty()) {
        //         $final_results['donations_data'] = array_merge($final_results["donations_data"], $donation->toArray());
        //     }
        // }
        // if (array_key_exists('center', $search_data)) {
        //     $query = $search_data["center"];
        //     $had_params = true;
        //   //  $donation = DB::table('donations')->select('*')->where('center', 'like', $query.'%')->get();
        //     $donation = Donation::where("center", "like",$query.'%')->get();
        //     if (!$donation->isEmpty()) {
        //         $final_results['donations_data'] = array_merge($final_results["donations_data"], $donation->toArray());
        //     }
        // }
        // if (!$had_params) {
        //     $donations = DB::table('donations')->select('*')->get();
        //     $donations = Donation::all();

        //     return [$donations];
        // } elseif ($had_params && empty($final_results["donations_data"])) {
        //     return response()->json(["Message" => "donations with the defined search parameters doesnt exist"], 404);
        // } else {

        //     return ["Message" => "donation or donations data were found ", "data" => $final_results];
        // }
    }

    function updateDonation(Request $request)
    {
        $data = $request->validate([
            "id" => 'required|exists:donations,id',
            "doner_email" => 'email|exists:doners,email',
            "donation_date" => 'date',
            "center" => 'exists:centers,location'

        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the id of the donation that it's data needed to be modified and include new values for center or doner email or date or all to be modified"], 400);
        }

        //  DB::table('donations')->where('id', '=', $data['id'])->update($data);
        //    $updateData = collect($data)->except('id')->toArray();

        //    $update = Donation::where('id', $data['id'])->update($updateData);
        if ($request->user()->tokenCan("admin"))
            $update = Donation::withoutGlobalScope(DonationsScope::class)->where('id', '=', $data['id'])->update($data);
        else {
            if ($request->user()->center != $data["center"]) {
                return response()->json(["Message" => "User cant insert donations in center doesnt belong to him"], 401);
            } else {
                $update = Donation::where('id', '=', $data['id'])->update($data);
            }
        }
        if ($update)
            return response()->json(["Message" => 'donation  update is done successfully'], 200);
        else
            return response()->json(['message' => 'update failed'], 500);
    }

    function showDonation(int $id, Request $request)
    {
        $donation = Donation::query();
        if ($request->user()->tokenCan('admin')) {
            $donation = $donation->withoutGlobalScope(DonationsScope::class)->find($id);
        } else {
            $donation = $donation->find($id);
        }

        if (!$donation) {
            return response()->json(["message" => "donation not found"], 404);
        }
        // return response()->json(["data" => $donation], 200);
        return new DonationResource($donation);
    }
}
