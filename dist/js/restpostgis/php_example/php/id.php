<?php




  $lat = $_POST["lat"];
  $long = $_POST["lon"];
  //$lat = 1699213.268416112;
  //$long = 3138704.521018773;
  $base_url = "http://10.0.1.233/dev/REST/v1/ws_geo_identify.php?";
  $srid = '2232';
  $geotables = "muni_2010";
  $fields = "name";
  $distance = '50000';
  $format = 'json';
  $values = "distance=".$distance."&format=".$format."&fields=".$fields."&geotables=".$geotables."&srid=".$srid."&x=".$long."&y=".$lat;
  $str_values = (string)$values; 
  $url = $base_url.$str_values;
  $ci = curl_init();
  curl_setopt($ci, CURLOPT_URL,$url);
  //$get_data = curl_init($url);
  curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);     
  $data = curl_exec($ci);
  // echo $data;
  $data = $data;
  echo $data;



?>

 
 


