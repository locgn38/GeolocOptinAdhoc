<?php

 # "http://www5.drcog.dev/REST/v1/ws_geo_bufferfeature.php?to_geotable=arapahoe_2006&from_geotable=bicycle_facility_inventory_2011_oct&fields=t.gid,t.loc_01,t.loc_02&distance=250&parameters=t.acctype=%2715%27and%20f.type_fx_no=%201&format=json";


   
   $base_url = "http://10.0.1.233/dev/REST/v1/ws_geo_bufferfeature.php?";
   $srid = '2232';
   $from_geotable = "bicycle_facility_inventory_2011_oct";
   $to_geotable = "arapahoe_crash_2006";
   $fields = "t.gid,t.loc_01,t.loc_02,ST_AsGeoJson (ST_Transform(t.the_geom, 900913)) as geojson";
   $encodefields = urlencode($fields);
   $distance = 250;
   $path_type = $_POST["path_type"];
   
   //$limit = '10';
   $format = 'json';
   $parameters = "t.acctype='15' and type_fx_no=".$path_type;//replace type_fx_no with post
   $urlparams = urlencode($parameters);
   $values =  "from_geotable=".$from_geotable."&to_geotable=".$to_geotable."&distance=".$distance."&format=".$format."&fields=".$encodefields."&srid=".    $srid."&parameters=".$urlparams;
   $str_values = (string)$values; 
   $url = $base_url.$str_values;
    
  
   $curl = curl_init();
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
   $data = curl_exec($curl);
   echo $data;
   //echo $data = $data;
   
   //echo $poly_results; 
   /*
   $sql = "select t.gid,t.loc_01,t.loc_02,ST_AsGeoJson(ST_Transform(t.the_geom, 900913)) from bicycle_facility_inventory_2011_oct as f, arapahoe_2006 as t where ST_DWithin(f.the_geom, t.the_geom, 250) and t.acctype='15' and type_fx_no=5";
   $get_poly_query = pg_query($db, $sql);  
   $poly_results = pg_fetch_all($get_poly_query); 
   print_r($poly_results);
   //echo $data;
   $crash_json = json_decode($data, true);
   $total = $crash_json['total_rows'];
   //echo $total;
   $first = "'".$crash_json['rows'][0]['row']['gid']."'";
   if (intval($total) == 1){
         $sql = "Select ST_AsGeoJson (ST_Transform(the_geom, 900913)) from arapahoe_2006 where gid = ".$first;
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
        $gid = $crash_json['rows'][$i]['row']['gid'];
        $or = " or gid = ";
        $string .= $or."'".$gid."'";  
       }      
       $querystring = $first." ".$string;
       $sql = "Select ST_AsGeoJson (ST_Transform(the_geom, 900913)) from arapahoe_2006 where gid = ".$querystring;
       //echo $sql;
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
    // echo $results;

*/
 
