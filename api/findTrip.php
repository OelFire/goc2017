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
$startPoint = array('long' => 6.114977680291502, 'lat' => 49.60185748029151);
$endPoint = array('long' => 6.144977680291502, 'lat' => 49.62185748029151);
*/

$startStation = getClosestStation($startPoint);
$endStation = getClosestStation($endPoint);
$startStopPoint = getClosestStopPoint($startPoint);
$endStopPoint = getClosestStopPoint($endPoint);

//get_line_from_stoppoint(array(0 => $startStopPoint, 1 => $endStopPoint));
$car = time2Points(array(0 => $startPoint, 1 => $endPoint), "driving");
$walk = time2Points(array(0 => $startPoint, 1 => $endPoint), "walking");

$res = array(0 => $car, 1 => $walk);

sendData($res);
