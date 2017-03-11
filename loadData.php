<?php

include_once ("configDb.php");


/******************************************************************************/
/*                                                                            */
/* Fill table 'station' wich contain the informations about the bike stations */
/*                                                                            */
/******************************************************************************/

$tmp = my_get("https://api.tfl.lu/v1/BikePoint");
if ($tmp == false)
{
    http_response_code(444);
    die();
}
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
        echo "update bike station\n";
    }
    else
    {
        $request = $bdd->prepare("INSERT INTO `station` (`idStationApi`, `name`, `longitude`, `latitude`)
                          VALUES (:idStationApi, :nameStation, :longitude, :latitude)");

        $request->execute(array('idStationApi' => $data['properties']['id'],
            'nameStation' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "insert bike station\n";
    }
}


/*******************************************************************************/
/*                                                                             */
/* Fill table 'busRoute' wich contain the informations about all the bus lines */
/*                                                                             */
/*******************************************************************************/

$tmp = my_get("https://api.tfl.lu/v1/Line/Mode/bus/Route");
if ($tmp == false)
{
    http_response_code(444);
    die();
}
$infoRoute = json_decode($tmp, true);


foreach ($infoRoute as $data)
{
    $id = $bdd->prepare("SELECT count(*) AS 'nbId' FROM `busRoute` WHERE `idBusRoute` = :idBusRoute");
    $id->execute(array('idBusRoute' => $data['id']));
    $value = $id->fetch()['nbId'];
    if ($value > 0)
    {
        $request =  $bdd->prepare("UPDATE `busRoute` SET `idBusRouteApi`=:idBusRouteApi,`name`=:namebusRoute, 
                                   `stopPointList` = :stopPointList WHERE `idBusRoute` = :idBusRoute");
        $request->execute(array('idBusRoute' => $idBusRoute,
            'idBusRouteApi' => $data['id'],
            'namebusRoute' => $data['name'],
            'stopPointList' => serialize($data['stopPoints'])));
        echo "update busRoute\n";
    }
    else
    {
        $request = $bdd->prepare("INSERT INTO `busRoute` (`idBusRoute`, `name`, `stopPointList`)
                          VALUES (:idBusRoute, :namebusRoute, :stopPointList)");

        $request->execute(array('idBusRoute' => $data['id'],
            'namebusRoute' => $data['name'],
            'stopPointList' => serialize($data['stopPoints'])));
        echo "insert busRoute\n";
    }
}


/*******************************************************************************/
/*                                                                             */
/* Fill table 'stopPoint' wich contain the informations about stop points  */
/*                                                                             */
/*******************************************************************************/

$tmp = my_get("https://api.tfl.lu/v1/StopPoint");
if ($tmp == false)
{
    http_response_code(444);
    die();
}
$infoBus = json_decode($tmp, true);


foreach ($infoBus['features'] as $data)
{
    $id = $bdd->prepare("SELECT count(*) AS 'nbId' FROM `stopPoint` WHERE `idStopPoint` = :idStopPoint");
    $id->execute(array('idStopPoint' => $data['properties']['id']));
    $value = $id->fetch()['nbId'];
    if ($value > 0)
    {
        $request =  $bdd->prepare("UPDATE `stopPoint` SET `name`=:nameStopPoint, `longitude`=:longitude,
                                  `latitude`=:latitude WHERE `idStopPoint` = :idStopPoint");
        $request->execute(array('idStopPoint' => $data['properties']['id'],
            'nameStopPoint' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "update stopPoint\n";
    }
    else
    {
        $request = $bdd->prepare("INSERT INTO `stopPoint` (`idStopPoint`, `name`, `longitude`, `latitude`)
                          VALUES (:idStopPoint, :nameStopPoint, :longitude, :latitude)");

        $request->execute(array('idStopPoint' => $data['properties']['id'],
            'nameStopPoint' => $data['properties']['name'],
            'longitude' => $data['geometry']['coordinates'][0],
            'latitude' => $data['geometry']['coordinates'][1]));
        echo "insert stopPoint\n";
    }
}