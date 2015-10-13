<?php
try {
//récupération des valeurs $_GET
$unite=User::get('nom');
$msg = $this->waitParam('get', 'message', array(Request::PARAM_NOT_NULL));
$lang = $this->waitParam('get', 'lang', array(Request::PARAM_NOT_NULL));
// $code = User::get('code');
$val = $this->waitParam('get', 'val', array(Request::PARAM_NOT_NULL, Request::PARAM_TYPE_INTEGER));
$tel = $this->waitParam('get', 'tel', array(Request::PARAM_NOT_NULL, Request::PARAM_TYPE_TPH));

$DB = new Database();
$lastid = $DB->addSmsLock($unite, $msg,$lang,$tel,$val);

$urlSMS = APP::getInstance()->config['url'];
//génération texte suivant msg langue et code
$sender = "GENDLOC";
if ($msg=="Geoloc") {
   $text = "* Vérifier GPS et DATA à ON\r\n* Cliquer le lien " . $urlSMS . "/pos?c=".$lastid."\r\n* Accepter le partage\r\n* Patienter\r\n".$unite;
}
elseif ($msg=="Tracking") {
   $text = "* Vérifier GPS et DATA à ON\r\n* Cliquer le lien " . $urlSMS . "/trace?c=".$lastid."\r\n* Accepter le partage\r\n".$unite;
}

//envoi SMS et récupération du hash d'envoi
// require('/js/thecallR/src/ThecallrClient.php');
$config = APP::getInstance()->config['thecallr'];
$thecallrLogin = $config['login'];
$thecallrPassword = $config['password'];
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);
$settings=$config['settings'];
$res=$THECALLR->send($config['order']['set'],array($settings));


// Options
$options = new stdClass();
$options->flash_message = FALSE;
// "sms.send" method execution
$result = $THECALLR->call($config['order']['send'],$sender,$tel,$text,$options);
// The method returns the SMS ID

$nModif = $DB->setSmsSend($lastid, $result);

if($nModif != 1) {
   throw new Exception("l'envoi du sms a échoué");
}
// Renvoi au navigateur du succès de l'envoi du sms
$html = '</br>
         <table id="sms-table">
            <thead> <!-- En-tête du tableau -->
               <tr>
                   <th>TEL</th>
         		   <th>STATUT</th>
         		   <th style="display:none;">HASH</th>
         		</tr>
            </thead>
            <tbody style="text-align:left">
               <tr>
                  <td>'.$tel.'</td>
                  <td>ENVOYE</td>
                  <td style="display:none;">'.$result.'</td>
               </tr>
             </tbody>
          </table>';

$response = array("success" => true, "data"=> $html);
} catch(Exception $e) {
   // Renvoi au navigateur du succès de l'envoi du sms
   $html = '</br>
         <table id="sms-table">
            <thead> <!-- En-tête du tableau -->
               <tr>
                   <th>TEL</th>
         		   <th>STATUT</th>
         		   <th>Message</th>
         		</tr>
            </thead>
            <tbody style="text-align:left">
               <tr>
                  <td>'.$tel.'</td>
                  <td>ERROR</td>
                  <td>'.$e->getMessage().'</td>
               </tr>
             </tbody>
          </table>';

   $response = array("success" => false, "error"=> $html);
}
/**
 * les réponse ajax en json
 */
die(json_encode($response));
