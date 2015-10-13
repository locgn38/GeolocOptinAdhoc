<!doctype html>
<html lang="fr">

<head>
<title>DEMO GENDLOC - APIE</title>
<meta  charset="UTF-8" >
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<!--bibliothèque jquery -->
<script src="http://code.jquery.com/jquery-2.1.3.min.js"></script>
<!-- -->
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
<script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
<!--bibliothèque leaflet pour affichage map-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<!--bibliothèque sidebar pour affichage menu-->
<script src="/js/sidebar/leaflet-sidebar.min.js"></script>
<link rel="stylesheet" href="/js/sidebar/leaflet-sidebar.css" />
<!--bibliothèque dynatable pour affichage résultats recherches et sms-->
<script src="/js/jquery-dynatable/jquery.dynatable.js"></script>
<link rel="stylesheet" href="/js/jquery-dynatable/jquery.dynatable.css" />
<!--bibliothèque coordinates pour affichage coordonnées sous la souris-->
<script src="/js/Leaflet.Coordinates/dist/Leaflet.Coordinates-0.1.4.src.js"></script>
<link rel="stylesheet" href="/js/Leaflet.Coordinates/dist/Leaflet.Coordinates-0.1.4.css" />
<!--[if lte IE 8]><link rel="stylesheet" href="./js/Leaflet.Coordinates/dist/Leaflet.Coordinates-0.1.4.ie.css" /><![endif]-->
<!--feuille de style table-->
<link rel="stylesheet" href="/css/table.css" />
<!--bibliothèque intl-tel-input pour affichage menu envoi sms-->
<script src="/js/intl-tel-input/build/js/intlTelInput.js"></script>
<link rel="stylesheet" href="./js/intl-tel-input/build/css/intlTelInput.css">
<!--bibliothèque styledLayerControl pour affichage menu couches geo-->
<script src="/js/Leaflet.StyledLayerControl/src/styledLayerControl.js"></script>
 <link rel="stylesheet" href="./js/Leaflet.StyledLayerControl/css/styledLayerControl.css" />
<!--bibliothèque leaflet-ajax-->
<script src="/js/leaflet-ajax/dist/leaflet.ajax.min.js"></script>
<!--bibliothèque restpostgis pour affichage couche résultats géoloc-->
<script src="/js/restpostgis/lvector.js"></script>


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
<?php
// récupération données utilisateurs connecté

$result = User::getGeom();
$x = $result->x;
$y = $result->y;
$z = $result->zoom;
$utilisateur = $result->nom;

// include ('./conn/conn.inc.php');
// $quer = "SELECT st_x(geom), st_y(geom), nom, zoom FROM utilisateurs WHERE code = '$utilisateur'";
// $result = pg_query($dbconn, $quer);
// if (pg_num_rows($result)<>1) {
// 	echo 'erreur';
// 	exit;
// 	}
// $nom = pg_fetch_result($result, 0, nom);
// $x = pg_fetch_result($result, 0, 0);
// $y = pg_fetch_result($result, 0, 1);
// $z = pg_fetch_result($result, 0, 3);
?>

<!-- déclaration variables javascript -->
<script type="text/javascript">
var center_x="<?php echo $x; ?>";
var center_y="<?php echo $y; ?>";
var center_z="<?php echo $z; ?>";
var unite="<?php echo $utilisateur; ?>";
//var url ="<?php APP::getInstance()->config['url']; ?>";

// fonctions de conversion de coordonnées
// prévoir include + résoudre probleme signe hémisphère sud ??
function ConvertDDToDMS(D){
    var sign;
	D<0?sign="-":"";
	return [sign,Math.abs(0|D), '° ', 0|(D<0?D=-D:D)%1*60, "' ", 0|D*60%1*60, '"'].join('');
	//return [0|D, '° ', 0|(D<0?D=-D:D)%1*60, "' ", 0|D*60%1*60, '"'].join(''); ???
}
function ConvertDDToDMM(D){
	var sign;
	D<0?sign="-":"";
	return [sign,Math.abs(0|D), '° ', ((D<0?D=-D:D)%1*60).toFixed(3), "' "].join('');
}
</script>


<!-- interface-->

<!-- SIDEBAR -->
	<div id="sidebar" class="sidebar collapsed">
        <!-- Nav tabs -->
        <ul class="sidebar-tabs" role="tablist">
            <li><a href="#home" role="tab"><i class="fa fa-home"></i></a></li>
			<li><a href="#map-marker" role="tab"><i class="fa fa-map-marker"></i></a></li>
            <li><a href="#messages" role="tab"><i class="fa fa-envelope"></i></a></li>
	    </ul>

        <!-- Tab panes -->
        <div class="sidebar-content active">
            <div class="sidebar-pane" id="home">
                <h1>DEMONSTRATEUR GENDLOC - APIE</h1>
				<h1>Fonctionnalités proposées</h1>
                <p>Fond de carte OSM</p>
				<p>Gendloc</p>
			</div>

		<div class="sidebar-pane" id="map-marker"><h1>Géolocalisation</h1>
			<input id="phone" type="tel"></br></br>
			<select name="message">
				<option value="Geoloc">Geoloc</option>
				<option value="Tracking">Tracking</option>
			</select>
			<select name="lang">
				<option value="Français">Français</option>
				<option value="Anglais" disabled >Anglais</option>
				<option value="Espagnol" disabled >Espagnol</option>
				<option value="Italien" disabled >Italien</option>
				<option value="Allemand" disabled >Allemand</option>
				<option value="Russe" disabled >Russe</option>
			</select></br></br>
			<form id="validite">
				<input type="radio" name=val value="1" checked>2 HEURES
				<input type="radio" name=val value="2">24 HEURES
				<input type="radio" name=val value="3">DEFINITIF
			</br></br>
			<INPUT type="button" onclick="sms()" value="ENVOYER SMS" >
			</form>

			<div id="res_sms"></div>


<!-- initialisation intlTelInput -->
<script>
var phone = $("#phone");
phone.intlTelInput({
        preferredCountries: ['fr', 'gb', 'it', 'de', 'es', 'ch'],
        defaultCountry: "auto",
        geoIpLookup: function(callback) {
          $.get('http://ipinfo.io', function() {}, "jsonp").always(function(resp) {
            var countryCode = (resp && resp.country) ? resp.country : "";
            callback(countryCode);
          });
        },
        utilsScript: "../..//lib/libphonenumber/build/utils.js" // just for formatting/placeholders etc
});

/**
 * envoi sms
 */
function sms() {
	//récupération des valeurs
	var telInput = phone.intlTelInput("getNumber");
    var val=$('input[name=val]:checked', '#validite').val();
    var message = $("#map-marker").find( "select[name='message']" ).val();
    var lang = $("#map-marker").find( "select[name='lang']" ).val();
    var url = "/sms/envoi_sms.php";
	// Send the data using post
	//var posting = $.get( url, { unite: unite , message: message, lang: lang, val: val, code: code, tel: telInput}) ;
    var posting = $.get( url, { message: message, lang: lang, val: val, tel: telInput}) ;
	// Put the results in a div
	posting.done(function( data ) {
		var data = JSON.parse(data);
		if(data && ("success" in data) && data.success == true) {
			  var content = $( data.data );
			  $( "#res_sms" ).empty().append( content );
		} else {
			var content = $( data.error );
			  $( "#res_sms" ).empty().append( content );
		}
	});
}
</script>
	</div>



			<div class="sidebar-pane" id="messages"><h1>Nous contacter</h1><a href="mailto:<?php print APP::getInstance()->config['mail'];?>?subject=Contact+gendloc">PGHM ISERE - GENDLOC</a></div>
		</div>
	</div>

	<!-- LA CARTE -->
    <div id="map" class="sidebar-map"></div>


	<script type="text/javascript">
	// Carte Open OpenStreetMap_Mapnik
	var OpenStreetMap_Mapnik = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	});

	// marker resultat geoloc
	var locMarker = L.Icon.extend({
		options: {
        iconUrl: "icon/loc.png",
        shadowUrl: null
            }
	});

	// PRWSF - Résultats géolocalisation du jour pour l'utilisateur : pos_jour est une vue postgresql
	var where = "unite='"+unite+"'";
	var myPrwsfLayer = new lvector.PRWSF({
    url: "./js/restpostgis/",
    geotable: "pos_jour",
	geomFieldName: "geom",
	where: where,
	fields: "id,unite,datetime,precision,ST_X(geom),ST_Y(geom), orig, tel, com",
	scaleRange: [4,19],
	dynamic: true,
	autoUpdate: true,
	autoUpdateInterval: 15000,
	popupTemplate : function(properties) {
			var output = "<h3>GENDLOC</h3>";
			output += "REF : " + properties['unite'] + " - " + properties['orig'] +"<br />";
			output += "HEURE : " + properties['datetime'].substr(11,8) + "<br />";
			output += "TELEPHONE : " + properties['tel'] + "<br />";
			output += "PRECISION : " + properties['precision'] + " m <br /><br />";
			output += "D.D LAT : " + parseFloat (properties['st_y']).toFixed(5) + " - LNG : " + parseFloat (properties['st_x']).toFixed(5) +"<br />";
			output += "D°M.M LAT : " +  ConvertDDToDMM(parseFloat (properties['st_y'])) + " - LNG : " + ConvertDDToDMM(parseFloat (properties['st_x'])) +"<br />";
			output += "D°M\'S\'\' LAT : " + ConvertDDToDMS(parseFloat (properties['st_y'])) + " - LNG : " + ConvertDDToDMS(parseFloat (properties['st_x'])) +"<br />";
			output += "COMMUNE : "+ properties['com'];
			return output;
	} ,
	showAll: false,
	symbology: {
    type: "range", // Defines the symbology as a range type where values above a minimum and below a maximum value are symbolized the same way
    property: "precision", // The property (field, attribute) to use for defining range values and styles
    ranges: [ // An array of value ranges to set symbology. Each value range has a specific symbology.
        {
            range: [1, 15],
            vectorOptions: {
                                    icon: new locMarker({
                                        iconSize: new L.Point(18, 18),
										iconAnchor: new L.Point(9, 9),
										popupAnchor: new L.Point(0, 0)
                                    }),
									opacity:0.9,
                                    title: "{tel} - {id} ({unite})"
                                }
        },{
            range: [15.01, 30],
            vectorOptions: {
                                    icon: new locMarker({
                                        iconSize: new L.Point(30, 30),
										iconAnchor: new L.Point(15, 15),
										popupAnchor: new L.Point(0, 0)
                                    }),
									opacity:0.5,
                                    title: "{id} ({unite})"
                                }
        },{
            range: [30.01, 60],
            vectorOptions: {icon: new locMarker({
                                        iconSize: new L.Point(60, 60),
										iconAnchor: new L.Point(30, 30),
										popupAnchor: new L.Point(0, 0)
										}),
										opacity:0.3,
                                    title: "{id} ({unite})"
        }}
    ]
}


});


	// La carte Leaflet
	var map = L.map("map", {
			center: new L.LatLng(center_y, center_x),
			zoom: center_z
			});

	// ajout de la couche OSM
	map.addLayer(OpenStreetMap_Mapnik);

	// affichage couche résultats géolocalisation
	myPrwsfLayer.setMap(map);

	// récupération de la boundingbox
    nw=map.getBounds().getNorthWest();
	se=map.getBounds().getSouthEast();

	// initialisation et ajout menu couches
	var baseMaps = [{
                       groupName : "OSM",
						expanded : true,
						layers    : {
                            "Mapnik" : OpenStreetMap_Mapnik}
	}];

	var overlays = [];

	var options = {
				container_width 	: "200px",
				container_maxHeight	: "400px",
				group_maxHeight     : "150px",
				exclusive       	: false
			};

	var control = L.Control.styledLayerControl(baseMaps, overlays, options);
	map.addControl(control);

	// affichage sidebar
	var sidebar = L.control.sidebar('sidebar').addTo(map);

	//affichage échelle
	L.control.scale({'position':'bottomleft','metric':true,'imperial':false}).addTo(map);

	//affichage coordonnées curseur DD
	L.control.coordinates({useLatLngOrder:true,
			centerUserCoordinates:true,
			labelTemplateLat:"D.D Lat {y}",
			labelTemplateLng:"Lng {x}"}).addTo(map);
	//affichage coordonnées curseur DMS
	L.control.coordinates({
			position:"bottomright",
			useDMS:true,
			centerUserCoordinates:true,
			labelTemplateLat:"D°MM'SS'' Lat {y}",
			labelTemplateLng:"Lng {x}",
			useLatLngOrder:true
		}).addTo(map);

	//Récupération boundingbox après mouvement carte
	map.on('moveend', function(e) {
	nw=map.getBounds().getNorthWest();
	se=map.getBounds().getSouthEast();
	});

	</script>

</body>
</html>