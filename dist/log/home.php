<?php
session_start();
if(empty($_SESSION['login_user']))
{
header('Location: index.php');
}
?>
<script language="javascript">document.location="/apie/APIE.php"</script> 
<!doctype html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>PGHM ISERE - DEMO APIE - LOGIN</title>
<link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<div id="main">
<h1>Page accueil</h1>
<a href="logout.php">Logout</a>
</div>
</body>
</html>