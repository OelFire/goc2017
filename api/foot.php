<?php

include_once "function.php";


if ((!isset($_GET["lat1"])) || (!isset($_GET["lng1"]))
		|| (!isset($_GET["lat2"])) || (!isset($_GET["lng2"])))
{
	http_response_code(400);
	die("error in getting lat and lng");
}
$center_lat = $_GET["lat1"];
$center_lng = $_GET["lng1"];
$other_lat = $_GET["lat2"];
$other_lng = $_GET["lng2"];

$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$center_lat.','.$center_lng.'&destinations='.$other_lat.','.$other_lng.'&mode=walking';
$dist = my_get($url);
$dist = json_decode($dist);

$send = new stdClass();

if (isset($dist->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'}))
{
		$send->WalkingTravelTime = $dist->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'};
} else {
	$send->WalkingTravelTime = NULL;
}

if (isset($dist->{'rows'}[0]->{'elements'}[0]->{''}->{'value'}))
{
	$send->WalkingTravelDist = $dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};
} else {
	$send->walkingTravelDist = NULL;
}

$send->ActualPriceWalking = 0;
$send->Pollution = 0;

sendData($send);
