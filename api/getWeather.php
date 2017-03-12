<?php

include_once("function.php");

function getWeather()
{
    $tmp = my_get("https://api.tfl.lu/v1/Weather");
    if ($tmp == false) {
        http_response_code(444);
        die();
    }

    $infoWeather = json_decode($tmp, true);

    $res = new stdClass();

    $res->temp = $infoWeather['main']['temp'];
    $res->description = $infoWeather['weather'][0]['description'];
    $res->windSpeed = $infoWeather['wind']['speed'];

    $tmp = my_get("https://api.tfl.lu/v1/Weather/Airquality");
    if ($tmp == false) {
        http_response_code(444);
        die();
    }
    $infoAir = json_decode($tmp, true);


    $res->pm10 = 0.0;
    $res->no2 = 0.0;
    $res->o3 = 0.0;
    $res->so2 = 0.0;
    $res->co = 0.0;


    foreach ($infoAir['features'] AS $data) {
        if ($data['properties']['pm10'] > $res->pm10)
            $res->pm10 = $data['properties']['pm10'];

        if ($data['properties']['no2'] > $res->no2)
            $res->no2 = $data['properties']['no2'];

        if ($data['properties']['o3'] > $res->o3)
            $res->o3 = $data['properties']['o3'];

        if ($data['properties']['so2'] > $res->so2)
            $res->so2 = $data['properties']['so2'];

        if ($data['properties']['co'] > $res->co)
            $res->co = $data['properties']['co'];
    }
    return $res;
}