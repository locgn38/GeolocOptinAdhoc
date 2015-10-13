<pre>
<?php
/**
* <h3>C. Create a Call Tracking Voice App</h3>
* 
* 
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
* <p>Step description</p>
*/
try {
	
	// A CallTracking application has the CALLTRACKING10 type
	$app_type = 'CALLTRACKING10';
	// Application name
	$app_name = 'My first CallTraking app';
	// Application parameters
	$app_params = new stdClass();
	// Media configuration
	$app_params->medias = new stdClass();
	$app_params->medias->welcome = 84;	// Played immediately to the caller
	$app_params->medias->ringtone = 3;	// Played while the target is ringing
	$app_params->medias->whisper = 0;	// Played to the target only (callee whispering)
	$app_params->medias->bridge = 0;	// Played to both parties before bridging the call
	// Recipient array initialization (several recipients can be called simultaneously)
	$app_params->targets = array();
	// Add a recipient
	// Recipient phone number must follow international format
	// Timeout parameter is the ringing timeout in seconds
	$app_params->targets[] = array('number'=>'__PHONENUMBER__','timeout'=>20);
	
	// Application creation and ID recovery
	$app = $THECALLR->call('apps.create', $app_type, $app_name, $app_params);
	$app_id = $app->hash;
	
	// ID display
	echo 'CallTracking application ID : ' . $app_id . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Available Did recovery</h4>
* <p>This step lists all available dids in order to assign the first one to the application.<br />
* You can also use an available did of your choice.</p>
*/

try {
	
	// The method argument allows you to retrieve only available DIDs
	$only_available = TRUE;
	
	// Method execution
	$dids_list = $THECALLR->call('apps.get_dids', $only_available);
	
	// List display
	echo '<pre>';
	print_r($dids_list);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>4. Assign a did to an application</h4>
* <p>At the end of this step, your Call Tracking application is working.</p>
*/

// Check that a did is available
if (is_array($dids_list) && count($dids_list) > 0) {
	try {
		
		// Get first did ID
		$did_id = current($dids_list)->hash;
		
		// Assign did to application
		$assign_status = $THECALLR->call('apps.assign_did', $app_id, $did_id);
		
		// Result display
		echo "The DID #$did_id has been successfully assigned to the Voice App #$app_id<br />";
		
	} catch (Exception $error) {
		die($error->getMessage());
	}
}

/**
* <h4>5. Remove DID from application and application removal</h4>
* <p>Step description</p>
*/

try {
	
	// Remove did from application
	$remove_status = $THECALLR->call('apps.remove_did', $did_id);
	// Result display
	echo "The DID #$did_id has been successfully unassigned from #$app_id.<br />";
	
	// Application removal
	$delete_status = $THECALLR->call('apps.delete', $app_id);
	// Result display
	echo "The Voice App #$app_id has been deleted.<br />";
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>