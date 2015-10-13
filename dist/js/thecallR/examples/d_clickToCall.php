<pre>
<?php
/**
* <h3>D. Make a ClickToCall application</h3>
* <p>ClickToCall is also known as "Web Callback"</p>
*/

/**
* <h4>1. Client initialization and configuration</h4>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Application creation</h4>
*/
try {
	
	// A ClickToCall application has the CLICKTOCALL10 type
	$app_type = 'CLICKTOCALL10';
	// Application name
	$app_name = 'My first ClickToCall app';
	// Application parameters
	$app_params = new stdClass();
	// Media configuration for both interlocutors
	$app_params->medias = new stdClass();
	$app_params->medias->A_welcome = 84;	// Played to A immediately
	$app_params->medias->A_ringtone = 3;	// Played to A while calling B
	$app_params->medias->B_whisper = 0;	// Played to B only (A still hears the ringtone)
	$app_params->medias->AB_bridge = 0;	// Played to both parties before bridging the call
	// Call attempts if A is not answering
	$app_params->A_attempts = 1;
	// Pause between attempts
	$app_params->A_retrypause = 30;
	
	// Application creation and ID recovery
	$app = $THECALLR->call('apps.create', $app_type, $app_name, $app_params);
	$app_id = $app->hash;
	
	// Application ID display
	echo 'ClickToCall application ID : ' . $app_id . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Start a call</h4>
* <p>On your website, a user will have to fill in his phone number and optionaly the date and time he wants to be called back.
* Those data will be then sent to THECALLR WebService using "<b>clicktocall/calls.start_2</b>" method.<br />
* You can cancel a call with "<a href="api/services/clicktocall/calls/#clicktocall/calls.cancel"><b>clicktocall/calls.cancel</b></a>" method.</p>
*/
try {
	
	// WARNING
	// Executing this method will make the call.
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');
	
	// CLICKTOCALL10 applicatioon ID
	$app = 'B74ABC51';
	// "A" phone number list configuration
	$A_targets = array();	// You can add as many phone numbers as you want
	$A_targets[] = array('number'=>'__PHONENUMBER__','timeout'=>20);
	// "B" phone number list configuration
	$B_targets = array();	// You can add as many phone numbers as you want
	$B_targets[] = array('number'=>'__PHONENUMBER__','timeout'=>30);
	// Options
	$options = new stdClass();
	// Scheduled time of the call (on 30 seconds).
	$options->schedule = date('Y-m-d H:i:s', time() + 30);
	// Caller phone number (E.164 international format) or BLOCKED if blocked
	$options->cli = 'BLOCKED';
	// Custom parameters visible in CDR. Very useful for call tracking
	$options->cdr_field = 'myClickToCallField';
	
	// Call start and call ID recovery
	$call_id = $THECALLR->call('clicktocall/calls.start_2', $app, $A_targets, $B_targets, $options);
	
	// Call ID display
	echo 'ClickToCall call ID : ' . $call_id . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}


/**
* <h4>4. Retrieve call status</h4>
* <p> "<b>clicktocall/calls.get_status</b>" method allows you to get call properties.</p>
*/
try {
	
	// Call ID. As an example, we get ID of call made above
	$call = $call_id;
	
	// Get call status
	$call_status = $THECALLR->call('clicktocall/calls.get_status', $call);
	
	// Display
	echo '<pre>';
	print_r($call_status);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>