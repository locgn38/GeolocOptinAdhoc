<pre>
<?php
/**
* <h3>B. Send SMS</h3>
* <p>Documentation of methods and parameters not used in this example are available <a href="api/services/sms">here</a>.</p>
*/

/**
* <h4>1. Client initialization and configuration</h4>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Send a SMS</h4>
* <p>Method description</p>
*/
try {
	
	// Sender
	$sender = "THECALLR";
	// Recipient phone number (E.164 format)
	$to = "__PHONENUMBER__";
	// SMS text
	$text = "This is my first SMS with THECALLR API :)";
	// Options
	$options = new stdClass();
	$options->flash_message = FALSE;
	
	// "sms.send" method execution
	$result = $THECALLR->call('sms.send',$sender,$to,$text,$options);
	
	// The method returns the SMS ID
	echo 'SMS ID : ' . $result . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. SMS sent list</h4>
* <p>Method description</p>
*/
try {
	
	// WARNING
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');
	
	// SMS type
	$type = "OUT";
	// Start date
	$from = "2012-01-01 00:00:00";
	// End date
	$to = date('Y-m-d H:i:s');
	
	// "sms.get_list" method execution
	$result = $THECALLR->call('sms.get_list',$type,$from,$to);
	
	// The method returns an array of sent SMS
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>