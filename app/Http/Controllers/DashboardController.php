<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function adminDahsboard (){

        //calculation number of doners 
        $num_doners=DB::table('doners')->count();

        //calculation number of donations 
        $num_donations=DB::table('donations')->count();

        //calculation of avg donations per doner 
        
        $count_donations=DB::table('donations')->count();
        $count_doners=DB::table('doners')->count();
        $avg_donations_per_doner=($count_donations/$count_doners)*100;

        //calculation of avg donations per center 
        //$avg_donations_per_center=DB::table('donations')->selectRaw('count(id)/count(distinct center) as avg_donations_per_center ')->get()->value('avg_donations_per_center');
        $count_centers=DB::table('centers')->count();
        $avg_donations_per_center=($count_donations/$count_centers)*100;

        //calculation of center with most donations
        // DB::table('donations')->selectRaw();



        return response()
        ->json(["data"=>["number_of_doners"=>$num_doners,"number_of_donations"=>$num_donations, 
        "avg_of_donations_per_doner_percent"=>round((float)$avg_donations_per_doner,2),
         "avg_of_donations_per_center_percent"=>round((float)$avg_donations_per_center,2)]],200);
    }
}
