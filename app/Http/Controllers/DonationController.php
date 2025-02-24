<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Donation;
use App\Models\Doner;
use Illuminate\Http\Request;

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
}
