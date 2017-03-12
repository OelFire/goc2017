<?php

include_once("configDb.php");
include_once("function.php");
include_once("timeTravel.php");
include_once("findClosest.php");

/*if (!isset($_GET["longStart"]) || !isset($_GET["latStart"]))
{
    http_response_code(400);
    die();
}
if (!isset($_GET["longEnd"]) || !isset($_GET["latEnd"]))
{
    http_response_code(400);
    die();
}*/

$startPoint = array('long' => 6.114977680291502/*$_GET["longStart"]*/, 'lat' => 49.60185748029151/*$_GET["latStart"]*/);
$endPoint = array('long' => 6.144977680291502/*$_GET["longEnd"]*/, 'lat' => 49.62185748029151/*$_GET["latEnd"*/);

$startStation = getClosestStation($startPoint);
$endStation = getClosestStation($endPoint);
$startStopPoint = getClosestStopPoint($startPoint);
$endStopPoint = getClosestStopPoint($endPoint);

$res = array('')