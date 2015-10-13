<?php
try {

   //FILTRE SUR LES PARAMETRES ATTENDUS, DOIT CORRESPONDRE AUX REGLES SPECIFIÉES
   $username = $this->waitParam("post", "username", array(Request::PARAM_NOT_NULL));
   $password = $this->waitParam("post", "password", array(Request::PARAM_NOT_NULL, Request::PARAM_ENCRYPTED));

  $infoUser = User::auth($username, $password);
   if($infoUser) {
      //c ok, on stocke en session
   User::set("id", $infoUser->id);
   User::set("code", $infoUser->code);
   User::set("nom", $infoUser->nom);
   User::set("geom", $infoUser->geom);
   User::set("email", $infoUser->email);
      $response = array("success"=> true, "data" => $infoUser);
   } else {
      throw new Exception("l'authentification a échoué");
   }


} catch(Exception $e) {
   $response = array("success" => false, "error"=> $e->getMessage());
}
/**
 * les réponse ajax en json
 */
die(json_encode($response));

// include("../conn/conn.inc.php");
// session_start();
// if(isSet($_POST['username']) && isSet($_POST['password']))
// {
// username and password sent from Form
//$username=mysql_real_escape_string($_POST['username'],$db);
//$password=mysql_real_escape_string($_POST['password'],$db);

//$result=mysql_query("SELECT uid, username, password FROM users WHERE username='$username' and password='$password'");
//$count=mysql_num_rows($result);
//$row=mysql_fetch_array($result,MYSQL_ASSOC);





// $username=pg_escape_string ($dbconn, $_POST['username']);
// $password=pg_escape_string ($dbconn, $_POST['password']);
// $result=pg_query("SELECT id, code, pwd FROM utilisateurs WHERE code='$username' and pwd='$password'");
// $count=pg_num_rows($result);
// $row=pg_fetch_array($result);




// If result matched $myusername and $mypassword, table row must be 1 row
// if($count==1)
// {
// $_SESSION['login_user']=$row['id'];
// $_SESSION['unite']=$row['code'];
// $_SESSION['code']=$row['pwd'];

// echo $row['id'];
// }
// }
?>