<?php

include_once("getWeather.php");

//var_dump($res);
$res = getWeather();

sendData($res);