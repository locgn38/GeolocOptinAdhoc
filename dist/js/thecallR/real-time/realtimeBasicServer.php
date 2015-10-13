<?php
/**
* <h3>Reatime Basic Server</h3>
*/

/**
* <h4>SDK Library (Basic)</h4>
*/
require('../src/ThecallrRtBasicServer.php');

/**
* <h4>This method must handle the request from THECALLR</h4>
* @param object $received_object Object sent by THECALLR
* @return object "ThecallrRtCommandObject" Object which contains the voice command to execute
*/
function request_processing($received_object) {
	// $command is our response (the voice command to execute)
	$command = new ThecallrRtCommandObject();
	// App check. You can handle many voice app with the same callback.
	if ($received_object->app == '92571EB9'){ // Our voice app ID
		// We keep the variables
		$command->variables = $received_object->variables;
		// Where are we in our IVR ? "sequence" is used to move inside the IVR tree
		$sequence = (property_exists($received_object->variables,'sequence'))?$received_object->variables->sequence:0;
		$result = $received_object->command_result;
		$error = $received_object->command_error;
		// If there's an error, we hard code the sequence
		if (!empty($error)) {
			$sequence = 99999;
		}
		// Replies by $sequence
		switch ($sequence) {
			case 0:
				$command->play("TTS|TTS_EN-GB_SERENA|Hello ! Please record your name after the tone.");
			break;
			case 1:
				$command->record(2, 10);
				//$command->record('erreur', 'erreur'); // Simulation d'erreur
			break;
			case 2:
				$command->variables->media_file = $result;
				$command->play("TTS|TTS_EN-GB_SERENA|Your name is...");
			break;
			case 3:
				$command->play_record($received_object->variables->media_file);
			break;
			case 4:
				$command->hangup();
			break;
			case 99999:
				$command->play("TTS|TTS_EN-GB_SERENA|Oops, error: $error");
			break;
		}
		// We move in our IVR
		$command->variables->sequence = $sequence + 1;
	}
	// Logs
	$log_content = "---------------------------------------\n";
	$log_content.= json_encode($received_object)."\n";
	$log_content.= "\n";
	$log_content.= json_encode($command)."\n";
	$log_content.= "---------------------------------------\n";
	$log = file_put_contents('log_processing.txt', $log_content, FILE_APPEND);
	// Response
	return $command;
}

/**
* <h4>Web Service</h4>
*/
try {
	// Server init
	$server = new ThecallrRtBasicServer();
	
	// Server start, we specify the callback function to handle incoming requests
	$server->start('request_processing');
	
} catch (Exception $e) {
	// Error logging
	$log_content = "!!! [".date('Y-m-d h:i:s')."] ".$e->getMessage()." ************************\n";
	if (isset($server) && $server instanceof ThecallrRtBasicServer) {
		$log_content.= $server->received_json."\n";
	}
	$log_content.= "**************************************************************************\n";
	return file_put_contents('log_errors.txt', $log_content, FILE_APPEND);
}
?>