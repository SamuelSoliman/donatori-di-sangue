<?php

namespace App\Http\Controllers;


use App\Models\Donation;
use App\Models\Scopes\DonationsScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\DonationResource;

use function Laravel\Prompts\search;

class DonationController extends Controller
{
    function createDonation(Request $request)
    {
        $data = $request->validate([
            "doner_email" => 'required|email|exists:doners,email',
            "center" => 'required|alpha|exists:centers,location',
            "donation_date" => 'required|date'
        ]);

        $donation = Donation::insert(["doner_email" => $data["doner_email"], "center" => $data['center'], "donation_date" => $data["donation_date"]]);
        if ($donation) {
            return response()->json(["Message" => "donation creation is complete"], 200);
        } else {
            return response()->json(["Message" => "donation creation failed"], 500);
        }
    }

    function showDonations(Request $request)
    {
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

        $results = $donation->get();
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
            "center" => 'alpha|exists:centers,location'

        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the id of the donation that it's data needed to be modified and include new values for center or doner email or date or all to be modified"], 400);
        }

        //  DB::table('donations')->where('id', '=', $data['id'])->update($data);
        //    $updateData = collect($data)->except('id')->toArray();

        //    $update = Donation::where('id', $data['id'])->update($updateData);
        if ($request->user()->tokenCan("admin"))
            $update = Donation::withoutGlobalScope(DonationsScope::class)->where('id', '=', $data['id'])->update($data);
        else
            $update = Donation::where('id', '=', $data['id'])->update($data);
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
