<pre>
<?php
/**
* <h3>F. Manager Conference rooms</h3>
*/

/**
* <h4>1. Client initialization and configuration</h4>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Create conference app</h4>
* <p>Description</p>
*/
try {
	// Room name
	$app_name = 'My first conference room';

	// Room parameters
	$app_params = new stdClass();
	// Room media
	$app_params->medias = new stdClass();
	$app_params->medias->welcome = 109753;
	$app_params->medias->pin_ask = 109756;
	$app_params->medias->pin_invalid = 109757;
	// Room limits
	$app_params->limits = new stdClass();
	$app_params->max_duration = 3600;	// In seconds (0 = unlimited)
	$app_params->max_connected = 0; 	// 0 = unlimited
	// Room audio recording
	$app_params->recording = 'OFF'; 	// AUTO - MANUAL
	// Is the room open or not?
	$app_params->open = TRUE;

	// Room access points
	$room_access = array(); // see next step

	// Room creation
	$room = $THECALLR->call('conference/10.create_room', $app_name, $app_params, $room_access);

	// Show room
	echo '<pre>';
	print_r($room);
	echo '</pre>';

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Room access points</h4>
* <p>Description</p>
*/
try {
	// Previously created room
	$room_hash = $room->hash;

	// First room access
	$access01 = new stdClass();
	$access01->label = 'My first access point';
	$access01->pin = '1234';
	$access01->max_duration = 300; // seconds

	// Add room access
	$room = $THECALLR->call('conference/10.add_room_access', $room_hash, $access01);

	// Show room
	echo '<pre>';
	print_r($room);
	echo '</pre>';

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>4. Assign a DID to your conference room</h4>
* <p>Description</p>
*/
try {
	// Previously created room
	$room_hash = $room->hash;

	// Get available DIDs
	$dids_list = $THECALLR->call('apps.get_dids', TRUE); // TRUE = only available

	// We assign the first available DID to the room
	if (count($dids_list) > 0) {

		$assign_status = $THECALLR->call('conference/10.assign_did', $room_hash, current($dids_list)->hash);

		// Success!
		echo "The DID has been successfully assigned to the conference room.<br />";

	} else {
		echo 'No DID available.';
	}

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>5. Outbound call to the conference room</h4>
* <p>Description</p>
*/
try {
	// Previously created conference room
	$room_hash = $room->hash;

	// Call settings
	$number_called = '__PHONENUMBER__'; // target
	$number_caller = '__PHONENUMBER__'; // cli
	$confirmation = TRUE; // Should we ask confirmation or join the room immediately?

	// Options
	$options = new stdClass();
	$options->max_duration = 300; // seconds

	// Call
	$access = $THECALLR->call('conference/10.call_number', $room_hash, $number_called, $number_caller, $confirmation, $options);

	// Show room access
	echo '<pre>';
	print_r($access);
	echo '</pre>';

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>6. Live commands</h4>
* <p>Description</p>
*/
try {
	// Previously created conference room
	$room_hash = $room->hash;

	// Room audio recording
	// Note : The "recording" parameter must be set to "MANUAL" for this to work.
	$THECALLR->call('conference/10.start_recording', $room_hash);
	$THECALLR->call('conference/10.stop_recording', $room_hash);

	// Pause some connected users (when paused, they're hearing music)
	$THECALLR->call('conference/10.pause', array('__CONNECTED_HASH__'));
	$THECALLR->call('conference/10.unpause', array('__CONNECTED_HASH__'));

	// Mute some connected
	$THECALLR->call('conference/10.mute', array('__CONNECTED_HASH__'));
	$THECALLR->call('conference/10.unmute', array('__CONNECTED_HASH__'));

	// Hangup some connected
	$THECALLR->call('conference/10.hangup', array('__CONNECTED_HASH__'));

} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>7. View room status</h4>
* <p>Description</p>
*/
try {
	// Previously created room
	$room_hash = $room->hash;

	// Live room status
	$live = $THECALLR->call('conference/10.view_live_room', $room_hash);

	// Connected users
	echo '<pre>';
	print_r($live->connected);
	echo '</pre>';

	// Room events
	$filters = new stdClass();
	$filters->room_hash = $room_hash;

	$events = $THECALLR->call('conference/10.list_events', $filters, 0, 30);

	// Show events
	echo '<pre>';
	print_r($events);
	echo '</pre>';

} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>