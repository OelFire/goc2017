<?php

define("APIKEY", "AIzaSyDWMtQAx6ocy8Z4lIcDh7FNOz9OOcJyc5k");

try
{
    $bdd = new PDO('mysql:host=localhost;dbname=goc2017', 'goc2017', 'goc2017');
    $bdd->exec("SET NAMES utf8");
}
catch(Exception $e)
{
    die('Error: '.$e->getMessage());
}