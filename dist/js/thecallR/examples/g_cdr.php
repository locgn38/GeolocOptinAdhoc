<pre>
<?php
/**
* <h3>G. Retrieve your CDR</h3>
*/

/**
* <h4>1. Client initialization and configuration</h4>
* <p>Your credentials are the ones you use with https://thecallr.com/s/</p>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Retrieve detailed inbound and outbound CDR</h4>
*/
try {
	
	// WARNING
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');
	
	// Call type (IN ou OUT)
	$type = 'IN';
	// Start date (included)
	$from = date('Y-m-d 00:00:00'); 	// Today at 00h00
	// End date (included)
	$to = date('Y-m-d 23:59:59');		// Today at 23h59
	// Application ID or NULL if you want to retrieve all CDR
	$app = NULL;
	// DID ID (IN) or number (OUT) or NULL to retrieve all CDR
	$number = NULL;
	
	// "cdr.get" method execution
	$cdr = $THECALLR->call('cdr.get', $type, $from, $to, $app, $number);
	
	// CDR display
	echo '<pre>';
	print_r($cdr);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>