<?php
//récupération des valeurs passées
$n = $_GET['n'];
$poslat=$_GET['y'];
$poslong=$_GET['x'];
$prec=$_GET['p'];
$l=$_GET['l'];
$addr=$_SERVER['REMOTE_ADDR'];
$agent=$_SERVER['HTTP_USER_AGENT'];


//connection bdd
include ('../conn/conn.inc.php');

//gestion des erreurs 4 5 6
if ($n==4) {	// ERREUR PERMISSION GPS
	echo "VOUS DEVEZ AUTORISER LE PARTAGE DE VOTRE POSITION !\n";
	$quer = "UPDATE public.smsloc SET statut = 'PERMISSION' WHERE id = '$l'";
	$stat = pg_query($dbconn, $quer);
	//Libère le résultat
	pg_free_result($stat);
	exit;
}
if ($n==5) {	// ERREUR GPS
	echo "GPS allumé et à l'extérieur !\n";
	$quer = "UPDATE public.smsloc SET statut = 'ERREUR 5' WHERE id = '$l'";
	$stat = pg_query($dbconn, $quer);
	//Libère le résultat
	pg_free_result($stat);
	exit;
}
if ($n==6) {	// ERREUR TIMEOUT
	echo "GPS allumé et à l'extérieur !\n";
	$quer = "UPDATE public.smsloc SET statut = 'TIME_OUT' WHERE id = '$l'";
	$stat = pg_query($dbconn, $quer);
	//Libère le résultat
	pg_free_result($stat);
	exit;
}

//include des fonctions de conversion de coordonnées
include('coord.inc.php');

//-- Récupération  données en base relative à l'id sms
// nouvelle vérification code
$quer = "SELECT * FROM public.smsloc WHERE id = '$l'";
$result = pg_query($dbconn, $quer);
if (pg_num_rows($result)<>1) {
	echo "Erreur de code. Message NON envoye\n";
	exit;
}
$row = pg_fetch_row($result,0);
$datetime = $row[1];
$unite = $row[2];
$msg = $row[3];
$lang = $row[4];
$tel = $row[5];
$val = $row[6];
$lim = new DateTime($datetime);
$now = new DateTime();
$now->format('Y-m-d H:i:s');


// vérification validité lien 2H ou 24H
$interval = $lim->diff($now);
if ($val==1 AND ($interval->format('%R%h'))>=2) {
	echo "Lien expire. Message NON envoye\n";
	exit;
	}
if ($val==2 AND ($interval->format('%R%d'))>=1) {
	echo "Lien expire. Message NON envoye\n";
	exit;
	}
	
//-- Récupération commune
$loc ="POINT(".$poslong." ".$poslat.")";
$quer ="SELECT com FROM communes WHERE ST_Contains(geom,ST_GeometryFromText('$loc',4326))";
$result = pg_query($dbconn, $quer);
$row = pg_fetch_row($result,0);
$com = $row[0];

//-- Récupération  email
$quer = "SELECT * FROM utilisateurs WHERE code = '$unite'";
$result = pg_query($dbconn, $quer);
if (pg_num_rows($result)<>1) {
	echo "Erreur de code utilisateur. Message NON envoye\n";
	exit;
}
$row = pg_fetch_row($result,0);
$to = $row[3];
	
// Libère le résultat
pg_free_result($result);

//-- corps email
$subject = "coordonnees telephone ".$tel. " - precision: ".round($prec,0)."m ".$n;
$body = "UTILISATEUR : ". $row[2]. " - precision: ".round($prec,0)."m\r\n\r\n".
"TELEPHONE : ".$tel. " - MESSAGE : ".$msg. " - LANGUE : ".$lang."\r\n\r\n".
"POSITION D.DDDDD : ".convertDD($poslat, "lat"). " - ".convertDD($poslong, "long")."\r\n\r\n".
"POSITION DD MM SS : ".convertSexa($poslat, "lat"). " - ".convertSexa($poslong, "long")."\r\n\r\n".
"POSITION DD MM MM : ".convertMM($poslat, "lat"). " - ".convertMM($poslong, "long")."\r\n\r\n".
"COMMUNE : ".$com;
$headers = 'From: gendloc@pghm-isere.com' . "\r\n" . 'Reply-To: gendloc@pghm-isere.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

// envoi email tracking (pas de retour navigateur)
if ($msg=="Tracking"){mail($to, $subject, $body, $headers);}
// envoi email geoloc
if ($msg=="Geoloc"){
if (mail($to, $subject, $body, $headers)) {
	// mise à jour affichage navigateur
	if ($prec<=15){
		echo ("<table width='100%'>
		<tr><td><div id='vert'>PRECISION 60 m</div></td></tr>
		<tr><td><div id='vert'>PRECISION 30 m</div></td></tr>
		<tr><td><div id='vert'>PRECISION 15 m</div></td></tr>
		</table><br>
 <table width='100%'><tr><td><H1>SUCCES</H1></td></tr></table></br>Message envoyé à ".$unite);
	}
	else if ($prec<=30) {
		echo ("<table width='100%'>
		<tr><td><div id='vert'>PRECISION 60 m</div></td></tr>
		<tr><td><div id='vert'>PRECISION 30 m</div></td></tr>
		<tr><td><div id='rouge'>PRECISION 15 m</div></td></tr>
		</table><br>
 <table width='100%'><tr><td><H1>PATIENTER</H1></td></tr></table>");
	}
	else {
		echo ("<table width='100%'>
		<tr><td><div id='vert'>PRECISION 60 m</div></td></tr>
		<tr><td><div id='rouge'>PRECISION 30 m</div></td></tr>
		<tr><td><div id='rouge'>PRECISION 15 m</div></td></tr>
		</table><br>
 <table width='100%'><tr><td><H1>PATIENTER</H1></td></tr></table>");
	}
} else {
echo("<p>Message echec - Recommencer</p>");
}

}
//-- Mise à jour statut dans table smsloc
$quer = "UPDATE public.smsloc SET statut = 'OK' WHERE id = '$l'";
$stat = pg_query($dbconn, $quer);
//Libère le répsultat
pg_free_result($stat);

//-- Stockage données
$quer = "INSERT INTO public.zeoloc (unite, addr, precision, useragent, geom, orig, tel, com) VALUES('$unite', '$addr', $prec, '$agent', ST_GeomFromText('POINT($poslong $poslat)', 4326), '$l', '$tel', '$com')";
$stockage = pg_query($dbconn, $quer);
// Libère le répsultat
pg_free_result($stockage);
// Ferme la connexion
pg_close($dbconn);

?>