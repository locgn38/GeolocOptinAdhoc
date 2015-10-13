<pre>
<?php
/**
* <h3>I. SMS Analytics</h3>
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
* <h4>2. Outbound SMS Analytics (summary)</h4>
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
	
	// "analytics/sms.summary" method execution
	$sms = $THECALLR->call('analytics/sms.summary', $from, $to);
	
	// SMS Analytics display
	echo '<pre>';
	print_r($sms);
	echo '</pre>';
	
	// Outbound SMS statuses
	$sms_by_status = $THECALLR->call('analytics/sms.summary_out_by_status', $from, $to);
	
	// display SMS Analytics 
	echo '<pre>';
	print_r($sms_by_status);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Outbound SMS Analytics over time (by hour, day, month)</h4>
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
	
	// "analytics/sms.history" method execution
	$sms = $THECALLR->call('analytics/sms.history', $from, $to);
	
	// Analytics sms display
	echo '<pre>';
	print_r($sms);
	echo '</pre>';
	
	// Outbound SMS statuses
	$sms_by_status = $THECALLR->call('analytics/sms.history_out_by_status', $from, $to);
	
	// display SMS Analytics 
	echo '<pre>';
	print_r($sms_by_status);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>