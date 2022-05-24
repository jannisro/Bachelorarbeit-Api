<?php

use App\Models\Electricity\NationalHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/harmonize', function() {
    foreach (DB::table('electricity_load')->get() as $row) {
        $netPos = DB::table('electricity_net_positions')->where('country', $row->country)->where('datetime', $row->datetime)->get();
        $generation = DB::table('electricity_generation')->select(DB::raw('SUM(`value`) as sum'))->where('country', $row->country)->where('datetime', $row->datetime)->get();
        $price = DB::table('electricity_prices')->where('country', $row->country)->where('datetime', $row->datetime)->get();
        NationalHistory::insert([
            'country' => $row->country,
            'datetime' => $row->datetime,
            'net_position' => ($netPos && $netPos->count() > 0) ? $netPos->first()->value : 0,
            'price' => ($price && $price->count() > 0) ? $price->first()->value : 0,
            'total_generation' => ($generation && $generation->count() > 0 && $generation->first()->sum) ? $generation->first()->sum : 0,
            'load' => $row->value,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    echo 'Done';
});