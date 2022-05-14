<?php

use App\Http\Controllers\AvailableCountriesController;
use App\Http\Controllers\Electricity\PeriodDataController as ElectricityPeriodDataController;
use App\Http\Controllers\Weather\PeriodDataController as WeatherPeriodDataController;
use App\Http\Controllers\Weather\StationController;
use Illuminate\Support\Facades\Route;


// Retrieve all available countries
Route::middleware('auth:sanctum')->get('/countries', AvailableCountriesController::class);


// Electricity routes
Route::middleware('auth:sanctum')->prefix('electricity')->group(function () {

    # Get all data of a time period
    Route::get('/{country}/{timePeriod}/{date}', ElectricityPeriodDataController::class);

});


// Weather routes
Route::middleware('auth:sanctum')->prefix('weather')->group(function () {

    # Get all data of a time period
    Route::get('/{country}/{timePeriod}/{date}', WeatherPeriodDataController::class);

    # Get all weather stations
    Route::get('/{country?}', StationController::class);

});