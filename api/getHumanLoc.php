<?php

include_once "function.php";


if (!isset($_GET['lat']) || !isset($_GET['long'])){
  http_response_code(400);
  die();
}
$lat =  $_GET['lat'];
$long = $_GET['long'];
$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&sensor=true";

$loc_raw = my_get($url);
$loc = json_decode($loc_raw , true);

$ret = new stdClass();

if ($loc['status'] == "OK"){
  $ret->num = $loc['results'][0]['address_components'][0]['long_name'];
  $ret->rue = $loc['results'][0]['address_components'][1]['long_name'];
  $ret->ville = $loc['results'][0]['address_components'][2]['long_name'];
  $ret->departement = $loc['results'][0]['address_components'][3]['long_name'];
  $ret->pays = $loc['results'][0]['address_components'][5]['long_name'];
}

sendData($ret);
