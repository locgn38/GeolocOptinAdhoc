<pre>
<?php
/**
* <h3>H. Calls Analytics</h3>
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
* <h4>2. Global Analytics (summary)</h4>
* <p>Note : "credit" and "debit" fields are in EUR cents.</p>
*/
try {
	// WARNING
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');
	
	// Start date (included)
	$from = date('Y-m-d 00:00:00'); 	// Today at 00h00
	// End date (included)
	$to = date('Y-m-d 23:59:59');		// Today at 23h59
	
	// "analytics/calls.summary" method execution
	$calls = $THECALLR->call('analytics/calls.summary', $from, $to);
	
	// display Calls Analytics 
	echo '<pre>';
	print_r($calls);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Calls Analytics over time (by hour, day or month depending on the range)</h4>
* <p>Note : "credit" and "debit" fields are in EUR cents.</p>
*/

try {
	
	// WARNING
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');
	
	// Start date (included)
	$from = date('Y-m-d 00:00:00'); 	// Today at 00h00
	// End date (included)
	$to = date('Y-m-d 23:59:59');		// Today at 23h59
	
	// "analytics/calls.history" method execution
	$calls = $THECALLR->call('analytics/calls.history', $from, $to);
	
	// display Calls Analytics 
	echo '<pre>';
	print_r($calls);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>