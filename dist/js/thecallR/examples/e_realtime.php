<pre>
<?php
/**
* <h3>E. Make a REAL-TIME application</h3>
* <p> Like VoiceXML, Real-Time allows you to monitor your inbound and outbound calls thanks to a WebService you provide.<br />
* Some server examples are available in this SDK (see "<b>realtimeBasicServer.php</b>" and "<b>realtimeExtendedServer.php</b>")</p>
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
* <p>Real time application management is available through "<b>dialr</b>" service.<br />
* The only application type valid for this service is "REALTIME10".</p>
*/
try {

	// Application type is REALTIME10
	$app_type = 'REALTIME10';
	// Application name
	$app_name = 'My first realtime app';
	// Your webservice access parameters
	$app_params = new stdClass();
	$app_params->url = '__YOURSERVER__/SDK/realtimeExtendedServer.php'; // Your URL callback. Our system will POST call status to your URL, and your answers will control the call
	$app_params->data_format = 'JSON';		// Data format. The only format supported right now is "JSON"
	$app_params->login = '__YOURLOGIN__';		// Login if your URL is password protected (HTTP Basic Authentication)
	$app_params->password = '__YOURPASSWORD__';	// Password If your URL is password protected (HTTP Basic Authentication)

	// Application creation and ID recovery
	$app = $THECALLR->call('apps.create', $app_type, $app_name, $app_params);
	$app_id = $app->hash;

	// Application ID display
	echo 'Realtime application ID : ' . $app_id . '<br />';

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Make outbound call from THECALLR</h4>
* <p>For outbound calls, ...</p>
*/
try {

	// WARNING
	// All the dates are GMT/UTC. Your country time offset is not handled
	date_default_timezone_set('UTC');

	// Application ID. Application type must be REALTIME10
	$call_app = '__APP_ID__';

	// target (number to call!)
	$call_target = array('number'=>'__PHONENUMBER__', 'timeout'=>20);

	// Specify calling Line Identification. Can be "BLOCKED", any DID you have on your account, or any phone we have previously authorized on your account
	$call_cli = 'BLOCKED';

	// Specify a custom field visible in CDR (can be used to tag calls)
	$call_cdr_field = 'myDialrField';

	// options
	$call_options = array('cli' => $call_cli, 'cdr_field' => $call_cdr_field);

	// Start the call
	$call = $THECALLR->call('dialr/call.realtime', $call_app, $call_target, $call_options);

	// Call time display
	echo 'Call request id = ' . $call . '<br />';

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>4. Assign a Did to the application for inbound calls</h4>
* <p>For inbound calls, ...</p>
*/
try {

	// Did ID
	$did_id = '__DID_ID__';

	// Assign Did to application
	$assign_status = $THECALLR->call('apps.assign_did', $app_id, $did_id);

	// Result display
	echo "Application ID #$app_id has been removed from Did ID #$did_id.<br />";

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>5. Application removal</h4>
* <p>Did is automatically removed during application removal.</p>
*/

try {

	// Application removal
	$delete_status = $THECALLR->call('apps.delete', $app_id);
	// Result display
	echo "Application ID #$app_id has been removed.<br />";

} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>