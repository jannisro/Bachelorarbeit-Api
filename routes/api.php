<?php

use App\Http\Controllers\AvailableCountriesController;
use App\Http\Controllers\Electricity\InternationalDataController;
use App\Http\Controllers\Electricity\NationalDataController as NationalElectricityDataController;
use App\Http\Controllers\Weather\NationalDataController as NationalWeatherDataController;
use App\Http\Controllers\Weather\LocationController;
use Illuminate\Support\Facades\Route;



// Retrieve all available countries
Route::middleware('auth:sanctum')
    ->get('/countries/{code?}', AvailableCountriesController::class)
    ->where('code', '[A-Z][A-Z]');



// National Electricity Data
Route::middleware('auth:sanctum')
    ->get('/electricity/national/{country}/{timePeriod}/{date}', NationalElectricityDataController::class)
    ->where([
        'country' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ])
    ->whereAlpha('timePeriod');



// International Electricity Data
Route::middleware('auth:sanctum')
    ->get('/electricity/international/{country1}/{country2}/{timePeriod}/{date}', InternationalDataController::class)
    ->where([
        'country1' => '[A-Z][A-Z]',
        'country2' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ])
    ->whereAlpha('timePeriod');



// National Weather Data
Route::middleware('auth:sanctum')
    ->get('/weather/national/{country}/{timePeriod}/{date}', NationalWeatherDataController::class)
    ->where([
        'country' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ])
    ->whereAlpha('timePeriod');



// Weather Location Data
Route::middleware('auth:sanctum')
    ->get('/weather/locations/{country?}', LocationController::class)
    ->where('country', '[A-Z][A-Z]');