<?php
/**
* <h3>Reatime Extended Server</h3>
*/

/**
* <h4>SDK Library (Extended)</h4>
*/
require('../src/ThecallrRtApp.php');

$app = new ThecallrRtApp('__MY_APP_ID__'); // specify your voice app id here

// Step1 : Be polite
$app->add_step_command(1,'play',array('media_id'=>"TTS|TTS_EN-GB_SERENA|Welcome to our IVR"));
$app->add_step_command_connection(1,'0',2); // build the tree by specifying next steps by result

// Step 2 : Ask for something
$step2_params = array('media_id'=>"TTS|TTS_EN-GB_SERENA|Please type your zip code",
					  'attempts'=>2,
					  'timeout_ms'=>5000,
					  'max_digits'=>5
					  );
$app->add_step_command(2,'read', $step2_params, 'zipcode'); // the result will be save in the variable 'zipcode'
$app->add_step_command_connection(2, '_default_', 3); // default next step is 3
$app->add_step_command_connection(2, '_error_', 4);   // in case of error, next step is 4
$app->add_step_command_connection(2, 'TIMEOUT', 5);   // in case of timeout, next step is 5

// Step 3 : Repeat the code
$app->add_step_command(3, 'play', array('media_id'=>"TTS|TTS_EN-GB_SERENA|Your zip code is {zipcode}")); // {zipcode} is replaced by the previous result
$app->add_step_command_connection(3, '0', 2);

// Step 4 : In case shit
$app->add_step_command(4, 'play', array('media_id'=>"TTS|TTS_EN-GB_SERENA|Oops, something is wrong."));
$app->add_step_command_connection(4, '0', 100);

// Step 5 : Say goodbye
$app->add_step_command(5, 'play', array('media_id'=>"TTS|TTS_EN-GB_SERENA|Thank you and good bye. See you soon !"));
$app->add_step_command_connection(5, '0', 100);

// Etape 100 : Hangupg
$app->add_step_command(100,'hangup');


/**
* <h4>Callback function called each time there is a step change</h4>
*/
function on_step_change($received_object, $sent_object) {
	// Logs
	$log_content = "---------------------------------------\n";
	$log_content.= json_encode($received_object)."\n";
	$log_content.= "\n";
	$log_content.= json_encode($sent_object)."\n";
	$log_content.= "---------------------------------------\n";
	return file_put_contents('log_callback.txt', $log_content, FILE_APPEND);
}


/**
* <h4>Web Service</h4>
*/
require('src/ThecallrRtExtendedServer.php');

try {
	// Server Init
	$server = new ThecallrRtExtendedServer();
	
	// Voice App declaration and callback definition
	$server->declare_app($app->app_id, $app, 'on_step_change');
	
	// Server start
	$server->start();
	
} catch (Exception $e) {
	// Error logging
	$log_content = "!!! [".date('Y-m-d h:i:s')."] ".$e->getMessage()." ************************\n";
	if (isset($server) && $server instanceof ThecallrRtExtendedServer) {
		$log_content.= $server->received_json."\n";
	}
	$log_content.= "**************************************************************************\n";
	return file_put_contents('log_errors.txt', $log_content, FILE_APPEND);
}
?>