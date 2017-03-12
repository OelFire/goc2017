<?php

function  getDist($things)
{
    if (isset($things->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'}))
    {
        return ($things->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'});
    } else {
        return (NULL);
    }
}

function  getTime($things)
{
    if (isset($things->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'}))
    {
        return ($things->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'});
    } else {
        return (NULL);
    }
}

function  timeVelo($array)
{
    $time = 0;
    $dist = 0;
    for ($i=0; $i <= 2; $i++) {
        if ($i == 3)
            break;
        if ($i == 1)
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$array[$i]['lat']},{$array[$i]['long']}&destinations={$array[$i + 1]['lat']},{$array[$i + 1]['long']}&mode=bicycling";
        else
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$array[$i]['lat']},{$array[$i]['long']}&destinations={$array[$i + 1]['lat']},{$array[$i + 1]['long']}&mode=walking";

        $things = my_get($url);
        $things = json_decode($things);

        $time += getTime($things);
        $dist += getDist($things);
    }

    $final = [
        "dist" => $dist,
        "time" => $time
    ];
    return $final;
}

function  timeBus($array)
{
    $time = 0;
    $dist = 0;
    for ($i = 0; $i <= 2; $i++)
    {
        if ($i == 1)
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$array[$i]['lat']},{$array[$i]['long']}&destinations={$array[$i + 1]['lat']},{$array[$i + 1]['long']}&mode=transit";
        else
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$array[$i]['lat']},{$array[$i]['long']}&destinations={$array[$i + 1]['lat']},{$array[$i + 1]['long']}&mode=walking";
        $things = my_get($url);
        $things = json_decode($things);

        $time += getTime($things);
        $dist += getDist($things);
    }

    $final = [
        "dist" => $dist,
        "time" => $time
    ];
    return $final;
}

function  time2Points($array, $methodTravel)
{
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$array[0]['lat']},{$array[0]['long']}&destinations={$array[1]['lat']},{$array[1]['long']}&mode={$methodTravel}";
    $things = my_get($url);
    $things = json_decode($things);

    $final = [
        "dist" => getDist($things),
        "time" => getTime($things)
    ];
    return ($final);
}