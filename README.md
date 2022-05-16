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
All endpoints requiring the url prefix `/api` and are returning JSON responses. See a full list below:

__All Available Countries__
Returns all countries or details of a certain one.
```
Endpoint: /api/countries/{?code?}

Examples:   /api/countries (Returns list of all countries)
            /api/countries/CZ (Returns information of country code)

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


__National Electricity Data__
Returns all national electricity data of a time period determined by a given date. When a data series is not available, a property with an empty value will be returned. All values are in MW (electricity price in €/MWh), deviation indicators are percentual.
```
Endpoint: /api/electricity/national/{country}/{timePeriod}/{date}

Examples:   /api/electricity/national/DE/day/2022-01-01 (returns hourly values of Jan 1st)
            /api/electricity/national/RO/week/2020-12-31 (returns daily values of week 52/2020)
            /api/electricity/national/FR/month/2017-06-20 (returns weekly values of June 2017)
            /api/electricity/national/IT/year/2021-02-28 (returns monthly values of 2021)

Sample Response:
{
    "country": "France",
    "time_period": "June 2017",
    "data": {
        "generation": [
            {
                "dt": "2017-06-01 00:00",
                "psr_type": "Solar",
                "value": 21
            }, ...
        ],
        "installed_capacity": [
            {
                "psr_type": "Nuclear",
                "value": 10793
            }, ...
        ],
        "load": [
            {
                "dt": "2017-06-01 00:00",
                "forecasted_value": 40241,
                "actual_value": 47373
            }, ...
        ],
        "net_position": [
            {
                "dt": "2017-06-01 00:00",
                "value": -300
            }, ...
        ],
        "electricity_price": [
            {
                "dt": "2017-06-01 00:00",
                "value": 320
            }, ...
        ]
    },
    "indicators": {
        "load_deviation": 3,
        "generation_deviation": -5,
        "net_position_deviation": -7,
        "commercial_flow_deviation": 3,
        "physical_flow_deviation": -1,
        "electricity_price_deviation": 9,
        "total_ntc_deviation": -4
    }
}
```


__International Electricity Data__
Returns all electricity data between two countries of a time period determined by a given date. When a data series is not available, a property with an empty value will be returned. All values are in MW, deviation indicators are percentual.
```
Endpoint: /api/electricity/international/{country1}/{country2}/{timePeriod}/{date}

Examples:   /api/electricity/international/DE/AT/day/2022-01-01 (returns hourly values of Jan 1st)
            /api/electricity/international/RO/BG/week/2020-12-31 (returns daily values of week 52/2020)
            /api/electricity/international/FR/ES/month/2017-06-20 (returns weekly values of June 2017)
            /api/electricity/international/IT/CH/year/2021-02-28 (returns monthly values of 2021)

Sample Response:
{
    "country1": "France",
    "country2": "Switzerland",
    "time_period": "June 2017",
    "data": {
        "commercial_flow": [
            {
                "dt": "2017-06-01 00:00",
                "value": 200
            }, ...
        ],
        "physical_flow": [
            {
                "dt": "2017-06-01",
                "value": 180
            }, ...
        ],
        "net_transfer_capacity": [
            {
                "dt": "2017-06-01 00:00",
                "forecasted_value": 40241,
                "actual_value": 400
            }, ...
        ]
    },
    "indicators": {
        "commercial_flow_deviation": 3,
        "physical_flow_deviation": -1,
        "ntc_deviation": -4
    }
}
```


__Weather Locations__
Retrieve all weather locations or only those of a certain country.
```
Endpoint: /api/weather/locations/{?countryCode?}

Examples:   /api/weather/locations (Returns all locations)
            /api/weather/locations/FI (Returns all locations in Finland)

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

__National Weather Data__
Outputs historic weather data and forecasts for a time period determined by a date. Values for the current and future days are forecasted. Units: Temperature=°C, Wind=m/s, Clouds=%, Precipitation(Rain/Snow)=mm. Deviation indicators are percentual.
```
Endpoint: /api/weather/national/{country}/{timePeriod}/{date}

Examples:   /api/weather/national/DE/day/2022-01-01 (returns hourly values of Jan 1st)
            /api/weather/national/RO/week/2020-12-31 (returns daily values of week 52/2020)
            /api/weather/national/FR/month/2017-06-20 (returns weekly values of June 2017)
            /api/weather/national/IT/year/2021-02-28 (returns monthly values of 2021)

Sample Response:
{
    "country": "France",
    "time_period": "June 2017",
    "data": {
        "temperature": [
            {
                "dt": "2017-06-01 00:00",
                "value": 12
            }, ...
        ],
        "wind": [
            {
                "dt": "2017-06-01 00:00",
                "value": 1.2
            }, ...
        ],
        "clouds": [
            {
                "dt": "2017-06-01 00:00",
                "value": 87
            }, ...
        ],
        "rain": [
            {
                "dt": "2017-06-01 00:00",
                "value": 0
            }, ...
        ],
        "snow": [
            {
                "dt": "2017-06-01 00:00",
                "value": 0
            }, ...
        ]
    },
    "indicators": {
        "mean_sunrise": "05:47",
        "mean_sunset": "21:08",
        "temperature_deviation": 3,
        "wind_deviation": -4,
        "clouds_deviation": 27,
        "rain_deviation": 2,
        "snow_deviation": 0
    }
}
```