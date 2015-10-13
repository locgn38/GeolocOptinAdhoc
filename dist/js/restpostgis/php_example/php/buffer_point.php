<?php

   $lat = $_POST["lat"];
   //$long ='3144319.662024238';
   //$lat = '1696359.661483313';
   $long = $_POST["lon"];
   $base_url = "http://10.0.1.233/dev/REST/v1/ws_geo_bufferpoint.php?";
   $srid = '2232';
   $geotable = "census_change_2000_2010_tracts";
   $fields = "tractid,pop_2010,pop_2000,pop_change,ST_AsGeoJson (ST_Transform(the_geom, 900913)) as geojson";
   $encodefields = urlencode($fields);
   $distance = $_POST["distance"];
 
   $limit = '10';
   $format = 'json';
   $parameters = NULL;
   $values =  "parameters=".$parameters."&distance=".$distance."&format=".$format."&fields=".$encodefields."&geotable=".$geotable."&srid=".$srid."&y=".$lat."&x=".$long."&limit=".$limit;
   $str_values = (string)$values; 
   $url = $base_url.$str_values;
   //echo $url;
   $curl = curl_init();
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
   $data = curl_exec($curl);
  
   $data = $data;
   echo $data;
 /*
   $tract_json = json_decode($data, true);
   $total = $tract_json['total_rows'];
   $first = "'".$tract_json['rows'][0]['row']['tractid']."'";
   if (intval($total) == 1){
         $sql = "Select ST_AsGeoJson (ST_Transform(the_geom, 4326)) from census_change_2000_2010_tracts where tractid = ".$first;
         //echo $sql;
         $get_poly_query = pg_query($db, $sql);
         $results = pg_fetch_all($poly);
         $poly_results = pg_fetch_all($get_poly_query); 
         $json_results = json_encode($results);
         $poly_json_encode = json_encode($poly_results);
         $poly_json_decode = json_decode($poly_json_encode, true);
         $coord_row = '[ { "geometry":  '.(string)$poly_json_decode[0]['st_asgeojson']."}] ";  
         $final_coord_rows = $coord_row;
   }   
   else {
      for ($i = 1; $i < $total; $i++){
        $tractid = $tract_json['rows'][$i]['row']['tractid'];
        $or = " or tractid = ";
        $string .= $or."'".$tractid."'";  
       }      
       $querystring = $first." ".$string;
       $sql = "Select ST_AsGeoJson (ST_Transform(the_geom, 4326)) from census_change_2000_2010_tracts where tractid = ".$querystring;
       $get_poly_query = pg_query($db, $sql);
       $num_poly = pg_num_rows($get_poly_query);
       $num_poly = intval($num_poly);
       $poly_results = pg_fetch_all($get_poly_query); 
       $poly_json_encode = json_encode($poly_results);
       $poly_json_decode = json_decode($poly_json_encode, true);
       $last_row = $num_poly - 1;
       $lastpoly = ' { "geometry":  '.(string)$poly_json_decode[$last_row]['st_asgeojson']."} ";
       //echo $last;
       for ($i = 0; $i <= $num_poly-2; $i++){
            $coord_row .= ' { "geometry":  '.(string)$poly_json_decode[$i]['st_asgeojson']."}, ";               
          }   
       $final_coord_rows = "[ ".$coord_row.$lastpoly." ]";   
 }
     $results = "{\"response1\": ".$data.",\"response2\": ".$final_coord_rows."}";
     $results = $results;
     echo $results;
*/
 
