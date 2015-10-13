<?php
//récupération id
$id = $this->waitParam('get', 'c', array(Request::PARAM_NOT_NULL, Request::PARAM_TYPE_INTEGER));

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title>GENDLOC</title>

<style>
table,tr,td
{
border:1px solid black;
font-size:120%;
text-align:center;
}
div#vert{background-color:green;}
div#rouge{background-color:red;}
</style>

<script type="text/javascript">
//initialisation des variables, p(precision) arbitrairement à 999 (supérieur à 60)
var watchId=null;
var it=0;
var x=0;
var y=0;
var p=999;
var id='<?php echo $id; ?>' ;

function startWatch(){
    if (navigator.geolocation)
        var watchId = navigator.geolocation.watchPosition(successCallback,
                                  errorCallback,
                                  {enableHighAccuracy:true,
                                  timeout:30000,
                                  maximumAge:2000, frequency: 1000});
    else
        alert("Votre navigateur ne prend pas en compte la géolocalisation HTML5");
		//informer le serveur
    }

function stopWatch(){
    //exécuter quand position GPS valide et précision meilleure que 15m
	navigator.geolocation.clearWatch(watchId);
	WatchId=null;
	locajax();
    }

function errorCallback(error){
    switch(error.code){
        case error.PERMISSION_DENIED:
          alert("Vous n'avez pas autorisé le partage de votre position");
		  //informer serveur erreur 4
		  it=4;
		  locajax();
          break;
        case error.POSITION_UNAVAILABLE:
          alert("Recommencer GPS à ON - vue du ciel");
		  //informer serveur erreur 5
		  it=5;
		  locajax();
          break;
        case error.TIMEOUT:
          alert("Le service n'a pas répondu à temps");
		  //informer serveur erreur 6
		  it=6;
		  locajax();
          break;
    }
}

function successCallback(position){
	p=position.coords.accuracy;
	x=position.coords.longitude;
	y=position.coords.latitude;
	if (p<60) {document.getElementById("coord").innerHTML = "LAT : " + y.toFixed(5) + " / LONG : " + x.toFixed(5);}
    if (p<=15) {
		if (it<3) {
		it=3;
		stopWatch();
		}
	}
	if (p<30) {
		if (it<2) {
			it=2;
			locajax();
		}
	}
	if (p<60) {
		if (it==0) {
			it=1;
			locajax();
		}
	}
}

function locajax() {
   //envoi information serveur
   if (window.XMLHttpRequest) {
    xmlhttp=new XMLHttpRequest();
  } else {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("aff").innerHTML=xmlhttp.responseText;
    }
  }
  //envoi id, précision, lng, lat et it=nb itération ou code erreur
  xmlhttp.open("GET","/traitement/succes.php?l="+id+"&p="+p+"&x="+x+"&y="+y+"&n="+it,true);
  xmlhttp.send();
}
</script>
</head>
<body  bgcolor="silver" onload="startWatch();">
 <h1>LOCALISATION SECOURS</h1></br>
 <div>
  <h2>
    <ul type="circle">
      <li>Vérifier GPS et DATA à ON</li>
	  <li>Accepter le partage de votre position</li>
      <li>Attendre l'indication SUCCES</li>
	  <li>L'envoi de votre position est automatique</li>
	</ul>
  </h2>
</div>
<h2><div id="coord">/</div></br></h2>
<div id="aff"><table width='100%'>
<!-- etat de départ -->
		<tr><td><div id='rouge'>PRECISION 60 m</div></td></tr>
		<tr><td><div id='rouge'>PRECISION 30 m</div></td></tr>
		<tr><td><div id='rouge'>PRECISION 15 m</div></td></tr>
		</table><br>
 <table width='100%'><tr><td><H1>PATIENTER</H1></td></tr></table></div>

</html>
