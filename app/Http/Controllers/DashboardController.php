<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Doner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
class DashboardController extends Controller
{
    function adminDahsboard(Request $request)
    {
        /** @var \Illuminate\Http\Request $request */
        
       if ($request->user()->tokenCan("admin")){
        if (Cache::get("count_centers")==null || Cache::get("num_doners")==null || Cache::get("num_donations")==null || Cache::get("avg_donations_per_doner")==null
        || Cache::get("avg_donations_per_center")==null || Cache::get("top_center")==null || Cache::get("top_doner")==null
        )
        {
        //calculation number of doners 
        $num_doners = DB::table('doners')->count(); //

        //calculation number of donations 
        $num_donations = DB::table('donations')->count(); //

        //calculation of avg donations per doner 

        $count_donations = DB::table('donations')->count();
        $count_doners = DB::table('doners')->count();
        $avg_donations_per_doner = ($count_donations / $count_doners); //

        //calculation of avg donations per center 
        //$avg_donations_per_center=DB::table('donations')->selectRaw('count(id)/count(distinct center) as avg_donations_per_center ')->get()->value('avg_donations_per_center');
        $count_centers = DB::table('centers')->count(); //
        $avg_donations_per_center = ($count_donations / $count_centers); //

        //calculation of center with most donations
        $top_center = DB::table('centers')->join('donations', 'location', '=', 'donations.center')
            ->select('centers.location', DB::raw('count(donations.id) as countd'))->groupBy('centers.location')
            ->orderByDesc('countd')->first(); //
        //calculation of doner with most donations 
        $top_doner = DB::table('doners')
            ->join('donations', 'email', '=', 'donations.doner_email')
            ->select('doners.email', DB::raw('count(donations.id) as countd'), DB::raw('concat(doners.name," ",doners.lastname) as fullname'))
            ->groupBy('doners.id')->orderByDesc('countd')->first(); //

        Cache::put("count_centers",$count_centers,1000);
        Cache::put("num_doners",$num_doners,1000);
        Cache::put("num_donations",$num_donations,1000);
        Cache::put("avg_donations_per_doner",$avg_donations_per_doner,1000);
        Cache::put("avg_donations_per_center",$avg_donations_per_center,1000);
        Cache::put("top_center",$top_center,1000);
        Cache::put("top_doner",$top_doner,1000);





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
        }else{
            //to be completed with data stored in the cache memory
            return response()
            ->json([
                "data" => [
                    "number_of_centers" =>Cache::get("count_centers") ,
                    "number_of_doners" => $num_doners,
                    "number_of_donations" => $num_donations,
                    "avg_of_donations_per_doner" => round((float)$avg_donations_per_doner, 2),
                    "avg_of_donations_per_center" => round((float)$avg_donations_per_center, 2),
                    "center_with_most_donations" => $top_center->location,
                    "doner_with_most_donations" => $top_doner->fullname
                ],
            ], 200);
        }
        }elseif ($request->user()->tokenCan("user")){
            // number of donations in user's center 
            $num_donations_per_center=Donation::where("center","=", $request->user()->center)->count();
            return response()->json([
                "data"=>[
                    "number_of_donations_per_user_center"=>$num_donations_per_center
                ]
                ],200);

        }
    }
}
