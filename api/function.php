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
