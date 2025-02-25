<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Donation;
use App\Models\Doner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    function createDonation(Request $request){
        $data = $request->validate([
            "doner_email"=>'required|email|exists:doners,email',
            "center"=>'required|alpha|exists:centers,location',
            "donation_date"=>'required|date'
        ]);
/*         $doner=Doner::where('email','=',$data['doner_email'])->first();
        $center=Center::where('location','=',$data['center'])->first(); */
        $donation=Donation::insert(["doner_email"=>$data["doner_email"],"center"=>$data['center'],"donation_date"=>$data["donation_date"]]);
        if ($donation){
        return response()->json(["Message"=>"donation insertion complete"],200);
        }else {
            return response()->json(["Message"=>"donation insertion failed"], 500);
        }
    }

    function showDonations(){
       return [ Donation::all()];
    }

    function updateDonation(Request $request){
        $data = $request->validate([
            "id"=>'required|exists:donations,id',
            "doner_email"=>'email|exists:doners,email',
            "center"=>'alpha|exists:centers,location',
            "donation_date"=>'date'
        ]);
        if (sizeof($data) < 2) {
            return response()->json(["error" => "you must choose the id of the donation that it is data needed to be modified and include new values for center or doner email or date or all to be modified"], 400);
        }

        DB::table('donations')->where('id', '=', $data['id'])->update($data);
        return response()->json(["Message" => 'doner update is done successfully'], 200);
    }

    
}
