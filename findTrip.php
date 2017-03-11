<?php

include_once("function.php");

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

$startPoint = array('long' => 6.1319346/*$_GET["longStart"]*/, 'lat' => 49.61162100000001/*$_GET["latStart"]*/);
//$endPoint = array('long' => $_GET["longEnd"], 'lat' => $_GET["latEnd"]);

$startStation = getClosestStation($startPoint);
//$endStation = getClosestStation($endPoint);
//$startStopPoint = getClosestStopPoint($startPoint);
//$endStopPoint = getClosestStopPoint($endPoint);

