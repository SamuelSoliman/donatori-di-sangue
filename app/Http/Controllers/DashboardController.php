<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Donation;
use App\Models\Doner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    function adminDahsboard(Request $request)
    {
        /** @var \Illuminate\Http\Request $request */

        if ($request->user()->tokenCan("admin")) {

            $cached_data = Cache::remember('all_data_admin', 90, function () {
                //calculation number of doners 
                $num_doners = Doner::count(); //2
                //calculation number of donations 
                $num_donations = Donation::withoutGlobalScopes()->count(); //3
                //calculation of avg donations per doner 
                $count_donations = Donation::count();
                $count_doners = Doner::count();
                $avg_donations_per_doner = round(((float)$count_donations / $count_doners), 2);
                //calculation of avg donations per center 
                $count_centers = Center::count(); //1
                $avg_donations_per_center = round(((float)$count_donations / $count_centers), 2);
                //calculation of center with most donations               
                $top_center = Center::withoutGlobalScopes()->withCount([
                    'donations' => function ($query) {
                        $query->withoutGlobalScopes();
                    }
                ])->orderByDesc('donations_count')->first(); //6
                //calculation of doner with most donations 
                $top_doner = Doner::withoutGlobalScopes()->select(DB::raw('concat(name," ",lastname) as fullname'))->withCount([
                    'donations' => function ($query) {
                        $query->withoutGlobalScopes();
                    }
                ])->orderByDesc('donations_count')->first(); //7

                return [
                    "num_doners" => $num_doners,
                    "num_donations" => $num_donations,
                    "avg_donations_per_doner" => $avg_donations_per_doner,
                    "count_centers" => $count_centers,
                    "avg_donations_per_center" => $avg_donations_per_center,
                    "top_center" => $top_center->location,
                    "top_doner" => $top_doner->fullname
                ];
            });


            return response()
                ->json([
                    "data" => $cached_data
                ], 200);
        } elseif ($request->user()->tokenCan("user")) {
            $user_center = $request->user()->center;
            $cached_data = Cache::remember("all_data_user_center_" . $user_center, 90, function () use ($user_center) {
                $num_donations_per_center_user = Donation::where("center", "=", $user_center)->count();
                $num_of_doners_per_center_user = Doner::join('donations', 'doners.email', '=', 'donations.doner_email')
                    ->where('donations.center', '=', $user_center)
                    ->distinct('doners.id')
                    ->count('doners.id');
                return [
                    'num_donations_per_center_user' => $num_donations_per_center_user,
                    'num_of_doners_per_center_user' => $num_of_doners_per_center_user
                ];
            });
            return response()
                ->json([
                    "data" => $cached_data
                ], 200);
        }
    }
}
