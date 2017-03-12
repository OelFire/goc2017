<?php

include_once("configDb.php");
include_once("function.php");
include_once("timeTravel.php");
include_once("findClosest.php");

if (!isset($_GET["longStart"]) || !isset($_GET["latStart"]))
{
    http_response_code(400);
    die();
}
if (!isset($_GET["longEnd"]) || !isset($_GET["latEnd"]))
{
    http_response_code(400);
    die();
}

$startPoint = array('long' => $_GET["longStart"], 'lat' => $_GET["latStart"]);
$endPoint = array('long' => $_GET["longEnd"], 'lat' => $_GET["latEnd"]);

/*
/*$startPoint = array('long' => 6.114977680291502, 'lat' => 49.60185748029151);
$endPoint = array('long' => 6.144977680291502, 'lat' => 49.62185748029151);*/
*/

$startStation = getClosestStation($startPoint);
$endStation = getClosestStation($endPoint);
$startStopPoint = getClosestStopPoint($startPoint);
$endStopPoint = getClosestStopPoint($endPoint);

$tmp = get_line_from_stoppoint(array(0 => $startStopPoint, 1 => $endStopPoint));
if (count($tmp) == 1)
{
    $bus = timeBus(array(0 => $startPoint, 1 => $startStopPoint->coordinates, 2 => $endStopPoint->coordinates, 3 => $endPoint));
}
elseif (count($tmp) == 3)
{
    $inter = $tmp[1]->coordinates;
    $bus = time2Points(array(0 => $startPoint, 1 => $startStopPoint->coordinates), "walking");
    $bus1 = time2PointsKey(array(0 => $startStopPoint->coordinates, 1 => $inter), "transit");
    $bus2 = time2PointsKey(array(0 => $inter, 1 => $endStopPoint->coordinates), "transit");
    $bus3 = time2Points(array(0 => $endStopPoint->coordinates, 1 => $endPoint), "walking");
    $bus['dist'] = $bus['dist'] + $bus1['dist'] + $bus2['dist'] + $bus3['dist'];
    $bus['time'] = $bus['time'] + $bus1['time'] + $bus2['time'] + $bus3['time'];
}
else
{
    http_response_code(440);
    die();
}

$bike = timeVelo(array(0 => $startPoint, 1 => $startStation->coordinates, 2 => $endStation->coordinates, 3 => $endPoint));

$car = time2Points(array(0 => $startPoint, 1 => $endPoint), "driving");
$walk = time2Points(array(0 => $startPoint, 1 => $endPoint), "walking");

$bus['nextDeparture'] = findBusDeparture($startStopPoint);
$car['id'] = "car";
$bus['id'] = "bus";
$walk['id'] = 'walk';
$bike['id'] = 'bike';

$res = array(0 => $bus, 1 => $bike, 2 => $walk, 3 => $car);

sendData($res);
