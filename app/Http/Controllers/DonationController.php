<?php

namespace App\Http\Controllers;


use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        /*         $doner=Doner::where('email','=',$data['doner_email'])->first();
        $center=Center::where('location','=',$data['center'])->first(); */
        $donation = Donation::insert(["doner_email" => $data["doner_email"], "center" => $data['center'], "donation_date" => $data["donation_date"]]);
        if ($donation) {
            return response()->json(["Message" => "donation insertion complete"], 200);
        } else {
            return response()->json(["Message" => "donation insertion failed"], 500);
        }
    }

    function showDonations(Request $request)
    {
        $final_results = ["donations_data" => []];
        $had_params = false;
        $search_data = $request->validate([
            "doner_email" => "email|exists:doners,email",
            "donation_date" => "date|exists:donations,donation_date",
            "center" => "alpha|exists:centers,location",
        ]);
        if (array_key_exists('doner_email', $search_data)) {
            $query = $search_data["doner_email"];
            $had_params = true;
            $donation = DB::table('donations')->select('*')->where('doner_email', '=', $query)->get();

            if (!$donation->isEmpty()) {
                $final_results['donations_data'] = array_merge($final_results["donations_data"], $donation->toArray());
            }
        }
        if (array_key_exists('donation_date', $search_data)) {
            $query = $search_data["donation_date"];
            $had_params = true;
            $donation = DB::table('donations')->select('*')->where('donation_date', '=', $query)->get();

            if (!$donation->isEmpty()) {
                $final_results['donations_data'] = array_merge($final_results["donations_data"], $donation->toArray());
            }
        }
        if (array_key_exists('center', $search_data)) {
            $query = $search_data["center"];
            $had_params = true;
            $donation = DB::table('donations')->select('*')->where('center', '=', $query)->get();

            if (!$donation->isEmpty()) {
                $final_results['donations_data'] = array_merge($final_results["donations_data"], $donation->toArray());
            }
        }
        if (!$had_params) {
            $donations = DB::table('donations')->select('*')->get();

            return [$donations];
        } elseif ($had_params && empty($final_results)) {
            return response()->json(["Message" => "donations with the defined search parameters doesnt exist"], 404);
        } else {

            return ["Message" => "donation or donations data were found ", "data" => $final_results];
        }
    }

    function updateDonation(Request $request)
    {
        $data = $request->validate([
            "id" => 'required|exists:donations,id',
            "doner_email" => 'email|exists:doners,email',
            "center" => 'alpha|exists:centers,location',
            "donation_date" => 'date'
        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the id of the donation that it is data needed to be modified and include new values for center or doner email or date or all to be modified"], 400);
        }

        DB::table('donations')->where('id', '=', $data['id'])->update($data);
        return response()->json(["Message" => 'doner update is done successfully'], 200);
    }

    function showDonation(int $id)
    {
        $donation = Donation::find($id);
        if (!$donation) {
            return response()->json(["message" => "donation not found"], 404);
        }
        return response()->json(["data" => $donation], 200);
    }
}
