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

function get_adresse($coord)
{
$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$coord['lat'].','.$coord['long'].'&sensor=true';
$data = my_get($url);
$jsondata = json_decode($data);
$rue = $jsondata->{'results'}[0]->{'address_components'}[1]->{'long_name'};
$ville = $jsondata->{'results'}[0]->{'address_components'}[2]->{'long_name'};
$departement = $jsondata->{'results'}[0]->{'address_components'}[3]->{'long_name'};
$pays = $jsondata->{'results'}[0]->{'address_components'}[5]->{'long_name'};
$adress = $rue." ".$ville." ".$departement." ".$pays;
echo $adress;
}

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
    $data->coordinates = array('long' => $infoStation['longitude'],'lat' => $infoStation['latitude']);
    //var_dump($data);
    return ($data);
}

function getClosestStation($coordinate)
{
  global $bdd;
  $data = $bdd->prepare("SELECT `idStationApi`, `latitude`, `longitude`, SQRT( ABS(`latitude`-{$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) AS 'test' FROM `station` WHERE SQRT( ABS(`latitude` - {$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) IS NOT null ORDER BY test ASC limit 3");
  $data->execute();

  $tab_data = array();
  while ($data2 = $data->fetch(PDO::FETCH_ASSOC))
  {
      array_push($tab_data, $data2);
  }
  foreach ($tab_data as $data2)
  {
      $dist = my_get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $coordinate['lat'] . ',' . $coordinate['long'] . '&destinations=' . $data2["latitude"] . ',' . $data2["longitude"] . '&mode=walking');
      $dist = json_decode($dist);
      //var_dump($dist);
      if (!isset($dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'}))
      {
          var_dump($dist);
          die("error");
      }
      $cur_dist = $dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};
      if (!isset($min_dist))
      {
          $id_min_dist = $data2['idStationApi'];
          $min_dist = $cur_dist;
      }
      elseif ($min_dist >= $cur_dist)
      {
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
    $data->coordinates = array('long' => $infoStation['longitude'],'lat' => $infoStation['latitude']);
    //var_dump($data);
    return ($data);
}

function getClosestStopPoint($coordinate)
{
    global $bdd;
    $data = $bdd->prepare("SELECT `idStopPoint`, `latitude`, `longitude`, SQRT( ABS(`latitude`-{$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) AS 'test' FROM `stopPoint` WHERE SQRT( ABS(`latitude` - {$coordinate['lat']}))+SQRT(ABS(`longitude` - {$coordinate['long']})) IS NOT null ORDER BY test ASC limit 3");
    $data->execute();

    $tab_data = array();
    while ($data2 = $data->fetch(PDO::FETCH_ASSOC))
    {
        array_push($tab_data, $data2);
    }
    foreach ($tab_data as $data2)
    {
        $dist = my_get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $coordinate['lat'] . ',' . $coordinate['long'] . '&destinations=' . $data2["latitude"] . ',' . $data2["longitude"] . '&mode=walking');
        $dist = json_decode($dist);
        //var_dump($dist);
        if (!isset($dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'}))
        {
            var_dump($dist);
            die("error");
        }
        $cur_dist = $dist->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};
        if (!isset($min_dist))
        {
            $id_min_dist = $data2['idStopPoint'];
            $min_dist = $cur_dist;
        }
        elseif ($min_dist >= $cur_dist)
        {
            $id_min_dist = $data2['idStopPoint'];
            $min_dist = $cur_dist;
        }
    }
    $info = getInfoStopPoint($id_min_dist);
    $info->distToStation = $min_dist;
    return ($info);
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

function one_var_coord($longitude, $latitude)
{
  $real_cord = array('long' => $longitude,
                     'lat' => $latitude);
  return ($real_cord);
}
