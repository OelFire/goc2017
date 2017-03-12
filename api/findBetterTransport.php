<?php

include_once("getWeather.php");

function findBetterTransport($car, $bus, $walk, $bike)
{
    $infoWeather = getWeather();
    $res = array();

    if (!(strpos($infoWeather->description, "pluie", 0) == false))
    {
        if ($infoWeather->pm10 > 50)
        {
            $bus['label'] = "rain, pollution";
            $res[0] = $bus;
            $res[1] = $car;
            $res[2] = $bike;
            $res[3] = $walk;
        }
        else
        {
            $car['label'] = "rain";
            $res[0] = $car;
            $res[1] = $bus;
            $res[2] = $bike;
            $res[3] = $walk;
        }
    }
    else if (!(strpos($infoWeather->description, "soleil", 0) == false))
    {
        if ($infoWeather->pm10 > 50)
        {
            $bus['label'] = "good weather, pollution";
            $res[0] = $bus;
            $res[1] = $bike;
            $res[2] = $car;
            $res[3] = $walk;
        }
        else
        {
            $bike['label'] = "good weather";
            $res[0] = $bike;
            $res[1] = $walk;
            $res[2] = $car;
            $res[3] = $bus;
        }
    }
    else
    {
        if ($infoWeather->pm10 > 50)
        {
            $bus['label'] = "pollution";
            $res[0] = $bus;
            $res[1] = $car;
            $res[2] = $bike;
            $res[3] = $walk;
        }
        else
        {
            $car['label'] = "fastest transport";
            $res[0] = $car;
            $res[1] = $bus;
            $res[2] = $bike;
            $res[3] = $walk;
        }
    }
    return $res;
}