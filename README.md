# Bachelorarbeit-API
A simple REST-API outputting electricity market, energy and weather data for european countries.

## Self-hosting Setup
1. Pull the repository
2. Run `$ composer install` to retrieve required packages
3. Copy the file `.env.example` to a file named `.env` and insert the URL and credentials of your database
4. Run a webserver and point its document root to the `/public` directory
5. Insert an access token (see Authentication) to get access to all routes

## Authentication
All requests must contain a bearer token to authenticate the host. This can be either inserted into the database table ***personal_access_tokens*** (when the data store is self hosted) or can be requested at the owner of the external data store. Type, name and abilities of the token can be freely chosen.

## Endpoints
All endpoints requiring the url prefix `/api` and are returning JSON responses. Only UTC dates and times are returned by the API. The following enpoints are available:
1. [All Available Countries](#all-available-countries)
2. [Value Search](#value-search)
3. [Retrieve live data](#retrieve-live-data)
4. [National Electricity Data](#national-electricity-data)
5. [International Electricity Data](#international-electricity-data)
6. [Export National Electricity Data](#export-national-electricity-data)
7. [Export International Electricity Data](#export-international-electricity-data)
8. [Weather Locations](#weather-locations)
9. [National Weather Data](#national-weather-data)
10. [Export Weather Data](#export-weather-data)



#### All Available Countries
Returns all countries or details of a certain one by passing an optional country code.
```
Endpoint:   /api/country/{?countryCode?}

Examples:   /api/country (Returns list of all countries)
            /api/country/CZ (Returns information of country code)

Sample Response:
{
    "countries": [
        {
            "short_name": "Czech Rep.",
            "official_name": "Czech Republic",
            "code": "CZ"
        },
        ...
    ]
}
```



#### Value Search
Search for past days by value combinations. A maximum of 30 result is returned.
```
Endpoint:   /api/search

Examples:   /api/search?period_start=2022-07-01&period_end=2022-07-05&wind_start=2&wind_end=3
            /api/search?country=DK&total_generation_start=8000&total_generation_end=30000

At least one of the following query parameters should be included to narrow the search. 
Define a search range by pass both the *_start and the respective *_end parameter.
- period_start, period_end (date with format yyyy-mm-dd)
- country (country Code)
- total_generation_start, total_generation_end (MW) 
- load_start, load_end (MW) 
- load_forecast_start, load_forecast_end (MW)
- price_start, price_end (€/MWh)
- net_position_start, net_position_end (MW)
- commercial_flow_start, commercial_flow_end (MW)
- physical_flow_start, physical_flow_end (MW)
- net_transfer_capacity_start, net_transfer_capacity_end (MW)
- temperature_start, temperature_end (°C)
- clouds_start, clouds_end (%)
- wind_start, wind_end (m/s)
- rain_start, rain_end (mm)
- snow_start, snow_end (mm)

Sample Response:
{
    "results": [
        {
            "country": "XX",
            "date": "yyy-mm-dd"
        },
        ...
    ]
}
````



#### Retrieve live data
Outputs the electricity and weather data of 2 hours ago (UTC).
```
Endpoint:   /api/current

Sample Response:
{
    "datetime": "yyyy-mm-dd hh:mm",
    "data": {
        "AT": {
            "net_position": x,
            "price": x,
            "generation": x,
            "load": x,
            "wind": x,
            "clouds": x,
            "temperature": x
        },
        "BA": { ... },
        "BE": { ... },
        "BG": { ... },
        "CH": { ... },
        "CZ": { ... },
        "DE": { ... },
        "DK": { ... },
        ...
    }
}
```



#### National Electricity Data
Returns all national electricity data of a time period determined by a given date. When a data series is not available, a property with an empty value will be returned. All values are in MW (electricity price in €/MWh).
```
Endpoint:   /api/electricity/national/{countryCode}/{timePeriod}/{date}

Examples:   /api/electricity/national/DE/day/2022-01-01     (returns hourly values of Jan 1st)
            /api/electricity/national/RO/week/2020-12-31    (returns daily values of week 52/2020)
            /api/electricity/national/IT/year/2021-02-28    (returns monthly values of 2021)

Sample Response:
{
    "country": "France",
    "time_period": "2022-07-01",
    "previous_step": "2022-06-30",
    "next_step": "2022-07-02",
    "data": {
        "total_generation": [
            {
                "dt": "2017-06-01 00:00",
                "value": x
            }, ...
        ],
        "installed_capacity": [
            {
                "psr_type": "Nuclear",
                "value": x
            }, ...
        ],
        "load": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "load_forecast": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "net_position": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "price": [
            {
                "dt": "222-076-01 00:00",
                "value": x
            }, ...
        ],
        "physical_flow": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "commercial_flow": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "net_transfer_capacity": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "mean_values": [
            {
                "name": "Generation",
                "value": x
            }, ...
        ],
        "generation": {
            "B01":  {
                "name": "Biomass",
                "hourly": [
                    {
                        "datetime": "2022-07-01 00:00",
                        "value": x
                    }
                ]
            }
        }
    }
}
```



#### International Electricity Data
Returns all electricity data between two countries of a time period determined by a given date. When a data series is not available, a property with an empty value will be returned. All values are in MW.
```
Endpoint:   /api/electricity/international/{startCountryCode}/{endCountryCode}/{timePeriod}/{date}

Examples:   /api/electricity/international/DE/AT/day/2022-01-01     (returns hourly values of Jan 1st)
            /api/electricity/international/RO/BG/week/2020-12-31    (returns daily values of week 52/2020)
            /api/electricity/international/FR/ES/month/2017-06-20   (returns weekly values of June 2017)
            /api/electricity/international/IT/CH/year/2021-02-28    (returns monthly values of 2021)

Sample Response:
{
    "start_country": "France",
    "end_country": "Switzerland",
    "time_period": "2022-07-01",
    "previous_step": "2022-06-30",
    "next_step": "2022-07-02",
    "data": {
        "commercial_flow": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ],
        "physical_flow": [
            {
                "dt": "2022-07-01",
                "value": x
            }, ...
        ],
        "net_transfer_capacity": [
            {
                "dt": "2022-07-01 00:00",
                "value": x
            }, ...
        ]
    }
}
```



#### Export National Electricity Data
Export hourly national electricity data of a given month in a CSV file. The file is returned by a streamed download response from the server. The month to export is determined by the passed date.
```
Endpoint:   /api/electricity/export/national/{countryCode}/{date}

Examples:   /api/electricity/export/national/DE/2022-07-01  (exports data of July 2022)
            /api/electricity/export/national/BG/2022-01-05  (exports data of January 2022)

Fields in CSV: [country, datetime, net_position, price, total_generation, load, load_forecast]
```



#### Export International Electricity Data
Export hourly international electricity data of a given month in a CSV file. The file is returned by a streamed download response from the server. The month to export is determined by the passed date.
```
Endpoint:   /api/electricity/export/international/{startCountryCode}/{endCountryCode}/{date}

Examples:   /api/electricity/export/national/DE/FR/2022-07-01   (exports data of July 2022)
            /api/electricity/export/national/BG/RO/2022-01-05   (exports data of January 2022)

Fields in CSV: start_country, end_country, datetime, commercial_flow, physical_flow, net_transfer_capacity
```



####  Weather Locations
Retrieve all weather locations or only those of a certain country by passing an optional countryCode.
```
Endpoint:   /api/weather/location/{?countryCode?}

Examples:   /api/weather/location       (Returns all locations)
            /api/weather/location/FI    (Returns all locations in Finland)

Sample Response:
{
    "locations": [
        {
            "id": 205,
            "country": "FI",
            "name": "Helsinki",
            "lat": 60.2003,
            "lng": 24.9161
        }, ...
    ]
}
```


#### National Weather Data
Outputs historic weather data and forecasts for a time period determined by a date. Values for the current and future days are forecasted. Units: Temperature=°C, Wind=m/s, Clouds=%, Precipitation(Rain/Snow)=mm. Deviation indicators are percentual.
```
Endpoint:   /api/weather/national/{countryCode}/{timePeriod}/{date}

Examples:   /api/weather/national/DE/day/2022-01-01     (returns hourly values of Jan 1st)
            /api/weather/national/RO/week/2020-12-31    (returns daily values of week 52/2020)
            /api/weather/national/FR/month/2017-06-20   (returns weekly values of June 2017)
            /api/weather/national/IT/year/2021-02-28    (returns monthly values of 2021)

Sample Response:
{
    "country": "France",
    "time_period": "2022-07-01",
    "previous_step": "2022-06-30",
    "next_step": "2022-07-02",
    "data": {
        "stations": [
            {
                "name": "Amiens",
                "latLng": [49.8946, 2.29958],
                "temperature": [ {dt: "00:00", "value": x}, ... ],
                "wind": [ {dt: "00:00", "value": x}, ... ],
                "clouds": [ {dt: "00:00", "value": x}, ... ],
                "rain": [ {dt: "00:00", "value": x}, ... ],
                "snow": [ {dt: "00:00", "value": x}, ... ],
            }
        ],
        "overall": {
            "temperature": [ {dt: "00:00", "value": x}, ... ],
            "wind": [ {dt: "00:00", "value": x}, ... ],
            "rain": [ {dt: "00:00", "value": x}, ... ],
            "clouds": [ {dt: "00:00", "value": x}, ... ],
            "snow": [ {dt: "00:00", "value": x}, ... ],
        }
    }
}
```



#### Export Weather Data
Export hourly weather data of a given month in a CSV file. The file is returned by a streamed download response from the server. The month to export is determined by the passed date.
```
Endpoint:   /api/weather/export/{countryCode}/{date}

Examples:   /api/weather/export/DE/2022-07-01   (exports data of July 2022)
            /api/weather/export/BG/2022-01-05   (exports data of January 2022)

Fields in CSV: station_name, lat, lng, country, datetime, temperature, clouds, wind, rain, snow
```