<?php

function sendData($response) {
  header('Content-Type: application/json');
  echo json_encode($response, JSON_PRETTY_PRINT);
}

function my_get($URL)
{
  $c = curl_init();
  curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($c, CURLOPT_URL, $URL);
  $contents = curl_exec($c);
  curl_close($c);

  if ($contents) return $contents;
  else return FALSE;
}

function get_total_station()
{
  global $bdd;
  $req = $bdd->prepare('SELECT id FROM `stations_velo`');
  $req->execute();
  $nb = 0;
  while ($donne = $req->fetch())
    {
      $temp = get_info_station($donne['id']);
      if ($temp['status'] == "OPEN")
	$nb++;
    }
  return ($nb);
}

function get_all_velo()
{
  global $bdd;
  $req = $bdd->prepare('SELECT id FROM `stations_velo`');
  $req->execute();
  $total = 0;
  while ($donne = $req->fetch())
    {
      $temp = get_info_station($donne['id']);
      if ($temp['status'] == "OPEN")
	$total += $temp['nb_dispo'];
    }
  return ($total);
}

function get_address($coord)
{
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $coord['lat'] . ',' . $coord['long'] . '&sensor=true';
    $data = my_get($url);
    $jsondata = json_decode($data);
    $rue = $jsondata->{'results'}[0]->{'address_components'}[1]->{'long_name'};
    $ville = $jsondata->{'results'}[0]->{'address_components'}[2]->{'long_name'};
    $departement = $jsondata->{'results'}[0]->{'address_components'}[3]->{'long_name'};
    $pays = $jsondata->{'results'}[0]->{'address_components'}[5]->{'long_name'};
    $adress = $rue . " " . $ville . " " . $departement . " " . $pays;
    echo $adress;
}

function get_coordinate($adresse)
{
  $adresse = str_replace(" ", "+", $adresse);
  $adresse = str_replace(",", "", $adresse);
  $coord =  my_get('http://maps.google.com/maps/api/geocode/json?address='.$adresse.'&sensor=false');
  $coord = json_decode($coord);
  $real_cord = array('long' => $coord->{'results'}[0]->{'geometry'}->{'location'}->{'lng'},
		     'lat' => $coord->{'results'}[0]->{'geometry'}->{'location'}->{'lat'});
  return ($real_cord);
}

function get_line_from_stoppoint($stopPoint){
  global $bdd;

//  print_r($stopPoint);
  $req = $bdd->prepare("SELECT `idBusRoute`, `name` FROM `busRoute` WHERE `stopPointList` LIKE :stopPoint1 AND `stopPointList` LIKE :stopPoint2");
  $req->execute(array('stopPoint1' => "%{$stopPoint[0]->id}%", 'stopPoint2' => "%{$stopPoint[1]->id}%"));
  if ($req->rowCount() > 0){
    return array(0 => $req->fetch());
  }
  $first_line = $bdd->prepare("SELECT * FROM `busRoute` WHERE `stopPointList` LIKE :stopPoint1 LIMIT 1");
  $first_line->execute(array('stopPoint1' => "%{$stopPoint[0]->id}%"));
  $a = $first_line->fetch();

  $seconde_line = $bdd->prepare("SELECT * FROM `busRoute` WHERE `stopPointList` LIKE :stopPoint1 LIMIT 1");
  $seconde_line->execute(array('stopPoint1' => "%{$stopPoint[1]->id}%"));
  $b = $seconde_line->fetch();
  $c = array_intersect(unserialize($a['stopPointList']), unserialize($b['stopPointList']));

  $d = array_shift($c);
  $req = $bdd->prepare("SELECT `idBusRoute`, `name` FROM `busRoute` WHERE `stopPointList` LIKE :stopPoint1 AND `stopPointList` LIKE :stopPoint2");
  $req->execute(array('stopPoint1' => "%{$stopPoint[0]->id}%", 'stopPoint2' => "%{$d}%"));
  $e = $req->fetch(PDO::FETCH_ASSOC);

  $req = $bdd->prepare("SELECT `idBusRoute`, `name` FROM `busRoute` WHERE `stopPointList` LIKE :stopPoint1 AND `stopPointList` LIKE :stopPoint2");
  $req->execute(array('stopPoint1' => "%{$stopPoint[1]->id}%", 'stopPoint2' => "%{$d}%"));
  $f = $req->fetch(PDO::FETCH_ASSOC);

  $d = getInfoStopPoint($d);

  return array(0 => $e, 1 => $d, 2 => $f);
}

function one_var_coord($longitude, $latitude)
{
  $real_cord = array('long' => $longitude,
                     'lat' => $latitude);
  return ($real_cord);
}

function findBusDeparture($stopPoint)
{
    $coordinates = $stopPoint->coordinates;
    $long1 = str_replace(".", "", $coordinates['long']);
    $lat1 = str_replace(".", "", $coordinates['lat']);

    $long = "0000000";
    $lat = "00000000";
    $i = 0;
    while ($i < 7)
    {
        if ($i < strlen($long1))
            $long[$i] = $long1[$i];
        if ($i < strlen($lat1))
            $lat[$i] = $lat1[$i];
        $i += 1;
    }

    $idBus = my_get("http://travelplanner.mobiliteit.lu/hafas/query.exe/dot?performLocating=2&tpl=stop2csv&stationProxy=yes&look_maxdist=150&look_x={$long}&look_y={$lat}");
    if ($idBus == false)
    {
        http_response_code(444);
        die();
    }

    $idBus = rtrim($idBus);
    $idBus = substr($idBus, 3, strlen($idBus) - 4);
    var_dump($idBus);
    $idBus = urlencode($idBus);
    $tmp = my_get("http://travelplanner.mobiliteit.lu/restproxy/departureBoard?accessId=cdt&format=json&id={$idBus}");
    if ($tmp == false)
    {
        http_response_code(444);
        die();
    }
    $infoHour = json_decode($tmp, true);

    $res = array();
    for ($i = 0 ; $i < 4 && count($infoHour['Departure']) > $i; $i++)
    {
        $res[$i] = $infoHour['Departure'][$i]['rtTime'];
    }
    //var_dump($res);
    return ($res);
}
