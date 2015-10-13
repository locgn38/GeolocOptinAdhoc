<pre>
<?php
/**
* <h3>A. Quick Start</h3>
*/

/**
* <h4>1. Include the Library and edit your credentials</h4>
* <p>Your credentials are the ones you use with https://thecallr.com/s/</p>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Requirements</h4>
*/
try {
	
	// This method will check your environment and throw an Exception if something is wrong
	$result = $THECALLR->check_configuration();
	
	// Response 
	echo 'Your server completed all requirements.<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Method execution without parameters</h4>
*/
try {
	
	// The "system.get_timestamp" method returns THECALLR server timestamp
	$result = $THECALLR->call('system.get_timestamp');
	
	// Response display
	echo 'TheCallr server time : ' . date('Y-m-d H:i:s',$result) . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>4. Method execution with one parameter</h4>
*/
try {
	
	// 1. "call" method: each parameter of the method to execute is provided as an argument
	$result = $THECALLR->call('apps.get_list', TRUE);
	
	// 2.  "send" method: all parameters of the method to execute are provided as an array
	$result = $THECALLR->send('apps.get_list', array(TRUE));
	
	// Response display
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>5. Method execution with several parameters</h4>
*/
try {
	
	// WARNING
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');
	
	// 1. "call" method
	$result = $THECALLR->call('analytics.get_summary', '2012-01-01 00:00:00', '2012-12-31 23:59:59', NULL);
	
	// 2. "send" method
	$result = $THECALLR->send('analytics.get_summary', array('2012-01-01 00:00:00', '2012-12-31 23:59:59', NULL));
	
	// Response display
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>6. Method execution with error generation</h4>
*/
try {
	
	// this method does not exist (unfortunately), a call to it throws an exception
	$result = $THECALLR->call('service.make_me_rich');
	
	// Response display
	echo '<pre>';
	print_r($result);
	echo '</pre>';
	
}catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>