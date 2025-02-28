<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function adminDahsboard()
    {

        //calculation number of doners 
        $num_doners = DB::table('doners')->count();

        //calculation number of donations 
        $num_donations = DB::table('donations')->count();

        //calculation of avg donations per doner 

        $count_donations = DB::table('donations')->count();
        $count_doners = DB::table('doners')->count();
        $avg_donations_per_doner = ($count_donations / $count_doners);

        //calculation of avg donations per center 
        //$avg_donations_per_center=DB::table('donations')->selectRaw('count(id)/count(distinct center) as avg_donations_per_center ')->get()->value('avg_donations_per_center');
        $count_centers = DB::table('centers')->count();
        $avg_donations_per_center = ($count_donations / $count_centers);

        //calculation of center with most donations
        $top_center = DB::table('centers')->join('donations', 'location', '=', 'donations.center')
            ->select('centers.location', DB::raw('count(donations.id) as countd'))->groupBy('centers.location')
            ->orderByDesc('countd')->first();
        //calculation of doner with most donations 
        $top_doner = DB::table('doners')
            ->join('donations', 'email', '=', 'donations.doner_email')
            ->select('doners.email', DB::raw('count(donations.id) as countd'), DB::raw('concat(doners.name," ",doners.lastname) as fullname'))
            ->groupBy('doners.id')->orderByDesc('countd')->first();



        return response()
            ->json([
                "data" => [
                    "number_of_centers" => $count_centers,
                    "number_of_doners" => $num_doners,
                    "number_of_donations" => $num_donations,
                    "avg_of_donations_per_doner" => round((float)$avg_donations_per_doner, 2),
                    "avg_of_donations_per_center" => round((float)$avg_donations_per_center, 2),
                    "center_with_most_donations" => $top_center->location,
                    "doner_with_most_donations" => $top_doner->fullname
                ],
            ], 200);
    }
}
