<?php
$code = $this->waitParam('get', 'c', array(Request::PARAM_NOT_NULL, Request::PARAM_TYPE_INTEGER));
// $code = strtoupper($_GET["c"]);
// if (empty($code)) {header("Location: http://pghm-isere.com/erreur.html");}
?>
<!DOCTYPE html>
<html>
<head>
	<title>GENDLOC</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
	<script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
	<style>
body {
    padding: 0;
    margin: 0;
}
html, body, #map {
    height: 100%;
}
</style>
</head>
<body>
	<!-- DIV pour la carte -->
	<div id="map"></div>

	<script type="text/javascript">
var intervalle=0;var tmstp=9999999999;var l="<?php echo $code; ?>";var apiKey="<?php echo $apikey; ?>";var layers=new Array();function geopUrl(b,a,c){return"http://wxs.ign.fr/"+b+"/wmts?LAYER="+a+"&EXCEPTIONS=text/xml&FORMAT="+(c?c:"image/jpeg")+"&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILECOL={x}&TILEROW={y}"}var attributionIGN='&copy; <a href="http://www.ign.fr/">IGN-France</a>';layers["Carte IGN"]=L.tileLayer(geopUrl(apiKey,"GEOGRAPHICALGRIDSYSTEMS.MAPS"),{attribution:attributionIGN,maxZoom:18});var map=L.map("map",{layers:layers["Carte IGN"],center:new L.LatLng(45,5),zoom:4});var first=true;var myIcon=L.icon({iconUrl:"./icon/croix.png",iconSize:[32,32]});var marker=L.marker(map.getCenter(),{icon:myIcon});var circle=L.circle(map.getCenter(),0);map.locate({setView:true,maxZoom:16,enableHighAccuracy:true,watch:true,maximumAge:5000,timeout:30000});function onLocationFound(b){var a=b.accuracy/2;if(first==true){marker.setLatLng(b.latlng);marker.addTo(map);circle.setLatLng(b.latlng);circle.addTo(map)}L.circle(b.latlng,0.1).addTo(map);circle.setRadius(a);circle.setLatLng(b.latlng);marker.setLatLng(b.latlng);intervalle=(b.timestamp-tmstp)/1000;if((first==true|intervalle>30)&&b.accuracy<40){if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest()}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")}xmlhttp.onreadystatechange=function(){if(xmlhttp.readyState==4&&xmlhttp.status==200){}};tmstp=b.timestamp;xmlhttp.open("GET","./traitement/succes.php?l="+l+"&p="+b.accuracy+"&x="+b.latlng.lng+"&y="+b.latlng.lat+"&n=track",true);xmlhttp.send();first=false}}map.on("locationfound",onLocationFound);function onLocationError(a){alert(a.message)}map.on("locationerror",onLocationError);
	</script>

</body>
</html>
