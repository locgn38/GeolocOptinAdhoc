<?php
/**
 * Uber Search
 * This is the uber search. It's so cool it had to be German.
 * 
 * @param 		string 		$query 	  		query string
 * @return 		string		- resulting json or xml string
 */

# Includes
require_once("../inc/error.inc.php");
require_once("../inc/database.inc.php");
require_once("../inc/security.inc.php");


# Set array for search types
$searchType[0] = "Address";
$searchType[1] = "Library";
$searchType[2] = "School";
$searchType[3] = "Park";
$searchType[4] = "GeoName";
$searchType[5] = "Road";
$searchType[6] = "CATS";
$searchType[7] = "Intersection";
$searchType[8] = "PID";

# array for POI
$poi = array("Library","School","Park","GeoName","CATS","Road");

$sql = "";


# Retrive URL arguments
try {
	$query = preg_replace('/\s\s+/', ' ', trim($_REQUEST['query']));
	$searchTypes = explode(",",$_REQUEST["searchtypes"]);
	if (strlen($query) < 3) echo returnEmpty($query);
} 
catch (Exception $e) {
    trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
}


# Set header type
# header("Content-Type: application/json");

// Generate SQL Call
try {

	// ParcelID
	if (is_numeric($query) and in_array("PID", $searchTypes)) {  // probably a parcel id
		if (strlen($query) == 8) {
			//echo returnEmpty($query);
			// Replace with PID handler
			$sql .= sanitizeSQL("(select 'PID' as responsetype, 'master_address_table' as responsetable, 'objectid' as getfield, objectid as getid, num_parent_parcel as displaytext from master_address_table where num_parent_parcel = '" . $query . "' and num_x_coord > 0 and cde_status='A' order by getid, displaytext)");
		}
		else {
			//echo returnEmpty($query); 		
		}
	}
	else { 
		// If it's an int and a space it's an address
		$query_array = explode(' ', $query);
		$pos = strpos($query, "&");
		
		// if the first element is numeric it's an address
		if (is_numeric($query_array[0]) and in_array("Address", $searchTypes)) {
			// run full street name query		
			$sql .= sanitizeSQL("(select 'Address' as responsetype, 'master_address_table' as responsetable, 'objectid' as getfield, objectid as getid, full_address as displaytext from master_address_table where txt_street_number = '" . $query_array[0] . "' and full_address like '" . strtoupper($query) . "%'  and num_x_coord > 0 and cde_status='A' order by getid, displaytext)");
		}
		
		// if the first element isn't numeric and it has an ampersand it's an intersection
		else if ($pos != false and in_array("Intersection", $searchTypes )) { 
			// get string before &
			$firstStreet = strtoupper(trim(substr($query, 0, $pos)));
			// get string after &
			$secondStreet = strtoupper(trim(substr($query,$pos + 1, strlen($query) - $pos)));
			
			if (strlen($secondStreet) > 0) { $secondClause = " where streetname like '$secondStreet%' "; }
			else  { $secondClause = ""; }
			
			$sql = "select distinct 'Intersection' as responsetype, 'roads' as responsetable, 'streetname' as getfield, '$firstStreet' || ' & ' || b.streetname as getid, '$firstStreet' || ' & ' || b.streetname as displaytext  from (select streetname, the_geom from roads where streetname = '$firstStreet') a, (select streetname,the_geom from roads $secondClause) b where a.the_geom && b.the_geom and intersects(a.the_geom, b.the_geom) and b.streetname <> '$firstStreet' ";
			
						
		}
		
		else if (array_intersect($poi, $searchTypes)) {
			// make sql array

			$poiSQL["Library"] = "(select 'Library' as responsetype, 'libraries' as responsetable, 'gid' as getfield, gid as getid, name as displaytext from libraries where name ~* '$query' )";
			$poiSQL["School"] = "(select 'School' as responsetype, 'schools_1011' as responsetable, 'gid' as getfield, gid as getid, schlname as displaytext from schools_1011 where schlname ~* '$query' )";
			$poiSQL["Park"] = "(select 'Park' as responsetype, 'parks' as responsetable, 'gid' as getfield, gid as getid, prkname as displaytext from parks where prkname ~* '$query' )";
			$poiSQL["GeoName"] = "(select 'GeoName' as responsetype, 'geonames' as responsetable, 'geonameid' as getfield, geonameid as getid, name as displaytext from geonames where name ~* '$query' )";
			$poiSQL["Road"] = "(select 'Road' as responsetype, 'roads' as responsetable, 'streetname' as getfield, cast(oid as integer) as getid, street_name as displaytext from street_names where street_name ~* '$query' )";
			$poiSQL["CATS"] = "(select 'CATS' as responsetype, 'cats_light_rail_stations' as responsetable, 'gid' as getfield, gid as getid, name as displaytext from cats_light_rail_stations where name ~* '$query' ) union (select 'CATS' as responsetype, 'cats_park_and_ride' as responsetable, 'gid' as getfield, gid as getid, name as displaytext from cats_park_and_ride where name ~* '$query' )";
			$tmpsql = "";
			
			foreach ($searchTypes as $test) { 			
				if (in_array($test, $poi)) {
					if (strlen($tmpsql) > 0) { $tmpsql .= " union " . $poiSQL[$test]; }
					else {$tmpsql = $poiSQL[$test] ; }
				}
			}
			
			$sql .= "select * from (" . $tmpsql . ") as foo order by responsetype, displaytext";

		}
	}

}
catch (Exception $e) {
	trigger_error("Caught Exception: " . $e->getMessage(), E_USER_ERROR);
}


if (strlen($sql) < 10) {
	header("Content-Type: application/json");
	echo $_REQUEST['callback'] . '({"total_rows":"-1","rows":"row"})';
}
else {
	// Send the response
	require_once("../inc/json.pdo.inc.php");
	//echo $sql;
	$pgconn = pgConnection();
	$recordSet = $pgconn->prepare($sql);
				$recordSet->execute();
	header("Content-Type: application/json");
	echo rs2json($recordSet);
}	
			



?>