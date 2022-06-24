<?php

use App\Http\Controllers\AvailableCountriesController;
use App\Http\Controllers\BorderRelationsController;
use App\Http\Controllers\CurrentDataController;
use App\Http\Controllers\Electricity\InternationalDataController;
use App\Http\Controllers\Electricity\NationalDataController as NationalElectricityDataController;
use App\Http\Controllers\Electricity\ExportController as ElectricityExportController;
use App\Http\Controllers\Weather\ExportController as WeatherExportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Weather\NationalDataController as NationalWeatherDataController;
use App\Http\Controllers\Weather\LocationController;
use Illuminate\Support\Facades\Route;



// Retrieve all available countries
Route::/*middleware('auth:sanctum')
    ->*/get('/country/{code?}', AvailableCountriesController::class)
    ->where('code', '[A-Z][A-Z]');



// Retrieve all border relations of a country
Route::/*middleware('auth:sanctum')
    ->*/get('/country/{code}/borders', BorderRelationsController::class)
    ->where('code', '[A-Z][A-Z]');



// National Electricity Data
Route::/*middleware('auth:sanctum')
    ->*/get('/electricity/national/{country}/{timePeriodName}/{date}', NationalElectricityDataController::class)
    ->where([
        'country' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ])
    ->whereAlpha('timePeriod');



// International Electricity Data
Route::/*middleware('auth:sanctum')
    ->*/get('/electricity/international/{country1}/{country2}/{timePeriodName}/{date}', InternationalDataController::class)
    ->where([
        'country1' => '[A-Z][A-Z]',
        'country2' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ])
    ->whereAlpha('timePeriod');



// Export electricity data to CSV
Route::/*middleware('auth:sanctum')
    ->*/get('/electricity/export/{areaName}/{countryCode}/{date}', ElectricityExportController::class)
    ->where([
        'areaName' => 'national|international',
        'countryCode' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ]);



// National Weather Data
Route::/*middleware('auth:sanctum')
    ->*/get('/weather/national/{country}/{timePeriodName}/{date}', NationalWeatherDataController::class)
    ->where([
        'country' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ])
    ->whereAlpha('timePeriod');



// Weather Location Data
Route::/*middleware('auth:sanctum')
    ->*/get('/weather/location/{country?}', LocationController::class)
    ->where('country', '[A-Z][A-Z]');



// Export weather data to CSV
Route::/*middleware('auth:sanctum')
    ->*/get('/weather/export/{countryCode}/{date}', WeatherExportController::class)
    ->where([
        'areaName' => 'national|international',
        'countryCode' => '[A-Z][A-Z]',
        'date' => '\d\d\d\d-\d\d-\d\d'
    ]);



// Search for past days by attribute values
Route::/*middleware('auth:sanctum')->*/get('/search', SearchController::class);



// Current Data of all countries to display on map
Route::/*middleware('auth:sanctum')->*/get('/current', CurrentDataController::class);