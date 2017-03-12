<?php

include_once("function.php");

$tmp = my_get("https://api.tfl.lu/v1/Weather");
if ($tmp == false)
{
    http_response_code(444);
    die();
}

$infoWeather = json_decode($tmp, true);

$res = new stdClass();

$res->temp = $infoWeather['main']['temp'];
$res->description = $infoWeather['weather'][0]['description'];
$res->windSpeed = $infoWeather['wind']['speed'];

//var_dump($res);

sendData($res);