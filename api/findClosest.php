<?php

function getInfoStation($id)
{
    global $bdd;

    $request = $bdd->prepare("SELECT * FROM `station` WHERE `idStationApi` = :id");
    $request->execute(array('id' => $id));
    $infoStation = $request->fetch(PDO::FETCH_ASSOC);

    $tmp = my_get("https://api.tfl.lu/v1/BikePoint/{$id}");
    $info = json_decode($tmp, true);
    $data = new stdClass();
    $data->elecBike = $info['properties']['available_ebikes'];
    $data->manualBike = $info['properties']['available_bikes'];
    $data->freeDocks = $info['properties']['available_docks'];
    $data->totalDocks = $info['properties']['docks'];
    $data->picture = $info['properties']['photo'];
    $data->address = $info['properties']['address'];
    $data->city = $info['properties']['city'];
    $data->open = $info['properties']['open'];
    $data->name = $infoStation['name'];
    $data->id = $infoStation['idStationApi'];
    $data->coordinates = array('long' => $infoStation['longitude'], 'lat' => $infoStation['latitude']);
    //var_dump($data);
    return ($data);
}

function getClosestStation($coordinate)
{
    global $bdd;
    $data = $bdd->prepare("SELECT `idStationApi`, `latitude`, `longitude`, SQRT( ABS(`latitude`-{$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) AS 'test' FROM `station` WHERE SQRT( ABS(`latitude` - {$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) IS NOT null ORDER BY test ASC limit 3");
    $data->execute();

    $tab_data = array();
    while ($data2 = $data->fetch(PDO::FETCH_ASSOC)) {
        array_push($tab_data, $data2);
    }
    foreach ($tab_data as $data2) {
        $dist = my_get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $coordinate['lat'] . ',' . $coordinate['long'] . '&destinations=' . $data2["latitude"] . ',' . $data2["longitude"] . '&mode=walking');
        $dist = json_decode($dist);
        if (!isset($dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'})) {
            var_dump($dist);
            die("error");
        }
        $cur_dist = $dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};
        if (!isset($min_dist)) {
            $id_min_dist = $data2['idStationApi'];
            $min_dist = $cur_dist;
        } elseif ($min_dist >= $cur_dist) {
            $id_min_dist = $data2['idStationApi'];
            $min_dist = $cur_dist;
        }
    }
    $info = getInfoStation($id_min_dist);
    $info->distToStation = $min_dist;
    return ($info);
}

function getInfoStopPoint($id)
{
    global $bdd;

    $request = $bdd->prepare("SELECT * FROM `stopPoint` WHERE `idStopPoint` = :id");
    $request->execute(array('id' => $id));
    $infoStation = $request->fetch(PDO::FETCH_ASSOC);

    $data = new stdClass();
    $data->name = $infoStation['name'];
    $data->id = $infoStation['idStopPoint'];
    $data->coordinates = array('long' => $infoStation['longitude'], 'lat' => $infoStation['latitude']);
    //var_dump($data);
    return ($data);
}

function getClosestStopPoint($coordinate)
{
    global $bdd;
    $data = $bdd->prepare("SELECT `idStopPoint`, `latitude`, `longitude`, SQRT( ABS(`latitude`-{$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) AS 'test' FROM `stopPoint` WHERE SQRT( ABS(`latitude` - {$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) IS NOT null ORDER BY test ASC limit 3");
    $data->execute();

    $tab_data = array();
    while ($data2 = $data->fetch(PDO::FETCH_ASSOC)) {
        array_push($tab_data, $data2);
    }
    foreach ($tab_data as $data2) {
        $dist = my_get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $coordinate['lat'] . ',' . $coordinate['long'] . '&destinations=' . $data2["latitude"] . ',' . $data2["longitude"] . '&mode=walking');
        $dist = json_decode($dist);
        if (!isset($dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'})) {
            var_dump($dist);
            die("error");
        }
        $cur_dist = $dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};
        if (!isset($min_dist)) {
            $id_min_dist = $data2['idStopPoint'];
            $min_dist = $cur_dist;
        } elseif ($min_dist >= $cur_dist) {
            $id_min_dist = $data2['idStopPoint'];
            $min_dist = $cur_dist;
        }
    }
    $info = getInfoStopPoint($id_min_dist);
    $info->distToStation = $min_dist;
    return ($info);
}
