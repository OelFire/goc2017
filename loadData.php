<?php

include_once ("configDb.php");


/******************************************************************************/
/*                                                                            */
/* Fill table 'station' wich contain the informations about the bike stations */
/*                                                                            */
/******************************************************************************/

$tmp = file_get_contents("https://api.tfl.lu/v1/BikePoint");
$infoBike = json_decode($tmp, true);


foreach ($infoBike['features'] as $data)
{
    $id = $bdd->prepare("SELECT `idStation` FROM `station` WHERE `idStationApi` = :idStationApi");
    $id->execute(array('idStationApi' => $data['properties']['id']));
    if ($id->rowCount() > 0)
    {
        $idStation = $id->fetch()['idStation'];
        $request =  $bdd->prepare("UPDATE `station` SET `idStationApi`=:idStationApi,`name`=:nameStation,
                                   `longitude`=:longitude,`latitude`=:latitude WHERE `idStation` = :idStation");
        $request->execute(array('idStation' => $idStation,
            'idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "update\n";
    }
    else
    {
        $request = $bdd->prepare("INSERT INTO `station` (`idStationApi`, `name`, `longitude`, `latitude`)
                          VALUES (:idStationApi, :nameStation, :longitude, :latitude)");

        $request->execute(array('idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "insert\n";
    }
}


/*******************************************************************************/
/*                                                                             */
/* Fill table 'busRoute' wich contain the informations about all the bus lines */
/*                                                                             */
/*******************************************************************************/

$tmp = file_get_contents("https://api.tfl.lu/v1/Line/Mode/bus/Route");
$infoRoute = json_decode($tmp, true);


foreach ($infoRoute['features'] as $data)
{
    $id = $bdd->prepare("SELECT `idStation` FROM `station` WHERE `idStationApi` = :idStationApi");
    $id->execute(array('idStationApi' => $data['properties']['id']));
    if ($id->rowCount() > 0)
    {
        $idStation = $id->fetch()['idStation'];
        $request =  $bdd->prepare("UPDATE `station` SET `idStationApi`=:idStationApi,`name`=:nameStation,
                                   `longitude`=:longitude,`latitude`=:latitude WHERE `idStation` = :idStation");
        $request->execute(array('idStation' => $idStation,
            'idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "update\n";
    }
    else
    {
        $request = $bdd->prepare("INSERT INTO `station` (`idStationApi`, `name`, `longitude`, `latitude`)
                          VALUES (:idStationApi, :nameStation, :longitude, :latitude)");

        $request->execute(array('idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "insert\n";
    }
}

/*$tmp = file_get_contents("https://api.tfl.lu/v1/StopPoint");
$infoBus = json_decode($tmp, true);


foreach ($infoBus['features'] as $data)
{
    $id = $bdd->prepare("SELECT count(*) FROM `stopPoint` WHERE `idStopPoint` = :idStopPoint");
    $id->execute(array('idStopPoint' => $data['properties']['id']));
    if ($id->rowCount() > 0)
    {
        $idStation = $id->fetch()['idStation'];
        $request =  $bdd->prepare("UPDATE `stopPoint` SET `idStopPoint`=:idStopPoint,`name`=:nameStation,
                                   `longitude`=:longitude,`latitude`=:latitude WHERE `idStation` = :idStation");
        $request->execute(array('idStation' => $idStation,
            'idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1],
            'busStation' => 0,
            'busLines' => NULL));
        echo "update\n";
    }
    else
    {
        $request = $bdd->prepare("INSERT INTO `stopPoint` (`idStationApi`, `name`, `longitude`, `latitude`)
                          VALUES (:idStationApi, :nameStation, :longitude, :latitude)");

        $request->execute(array('idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1],
            'busStation' => 0,
            'busLines' => NULL));
        echo "insert\n";
    }
}*/