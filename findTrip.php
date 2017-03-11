<?php

include_once("function.php");

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

timeBus