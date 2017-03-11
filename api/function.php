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

function get_info_station($id)
{
  $dataJ = my_get('https://api.jcdecaux.com/vls/v1/stations/'.$id.'?contract=Nancy&apiKey=58a7596376f3ae8c4af270a5abc6b7c04ecff44c');
  $data = json_decode($dataJ);
  $new_data = array('banking' => $data->{'banking'},
		    'status' => $data->{'status'},
		    'nb_place' => $data->{'bike_stands'},
		    'nb_libre' => $data->{'available_bike_stands'},
		    'nb_dispo' => $data->{'available_bikes'},
		    'longitude' => $data->{'position'}->{'lng'},
		    'latitude' => $data->{'position'}->{'lat'},
		    'nom' => $data->{'name'});
  return ($new_data);
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

function get_closest_station($coordinate)
{
  $key = "AIzaSyAiht4VjPTI9bMut7kT0fe6WYx-67Xv9GQ";
  global $bdd;
  $data = $bdd->prepare('select `id`, `latitude`, `longitude`, SQRT( ABS(`latitude`-'.$coordinate['lat'].'))+SQRT(ABS(`longitude`-'.$coordinate['long'].')) AS `test` from stations_velo where SQRT( ABS(`latitude`-'.$coordinate['lat'].'))+SQRT(ABS(`longitude`-'.$coordinate['long'].')) is not null order by test asc limit 3');
  $data->execute();

  $tab_data = array();
  while ($data2 = $data->fetch())
    array_push($tab_data, $data2);
  $stop = 1;
  while ($stop == 1)
    {
      $stop = 0;
      foreach($tab_data as $data2)
	{
	    $dist = my_get('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$coordinate['lat'].','.$coordinate['long'].'&destinations='.$data2["latitude"].','.$data2["longitude"].'&mode=walking');
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
	      $id_min_dist = $data2['id'];
	      $min_dist = $cur_dist;
	    }
	  elseif ($min_dist >= $cur_dist)
	    {
	      $id_min_dist = $data2['id'];
	      $min_dist = $cur_dist;
	    }
	}
      $info = get_info_station($id_min_dist);
      /* if ($info['nb_dispo'] == 0)
	 {
	 $i = 0;
	 $stop = 1;
	 while($tab_data[$i])
	 {
	 if ($tab_data[$i]['id'] == $id_min_dist)
	 unset($tab_data[$i]);
	 $i++;
	 }
	 }*/
    }
  array_push($info, $min_dist);
  //var_dump($info);
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
