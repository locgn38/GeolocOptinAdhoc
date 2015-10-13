<?php
/**
* THECALLR real time services management webservice
* @author Tatunca <fw@thecallr.com>
*/

class ThecallrRtBasicServer {

	public $received_json;							// JSON request received from THECALLR
	public $received_object;						// Object received from THECALLR

	public $sent_object;							// Object sent back to THECALLR
	public $sent_json;								// JSON response sent back to THECALLR

	const REQUIRED_PHP_VERSION = '5.2.0';			// PHP minimum version

	/**
	* Constructor : configuration analysis
	* @param bool $check_configuration Server configuration check
	*/
	public function __construct($check_configuration = FALSE) {
		if ($check_configuration === TRUE) {
			$this->check_configuration();
		}
	}

	/**
	* Server configuration check
	*/
	public function check_configuration() {
		// PHP version check
		$version = array('required_version'		=>	self::REQUIRED_PHP_VERSION,
						 'installed_version'	=>	phpversion()
						 );
		if (version_compare($version['installed_version'], $version['required_version'], '>=') === FALSE) {
			throw new Exception('INVALID_PHP_VERSION');
		}
		// JSON extension check
		if (function_exists('json_encode') === FALSE){
			throw new Exception("UNAVAILABLE_JSON_PHP_EXTENSION");
		}
		// Response
		return TRUE;
	}

	/**
	* Start server
	* @param callable $process_callback Process function executed at each request (string(__FUNCTION__) or array(__CLASS__, __METHOD__))
	*/
	public function start($process_callback) {
		// JSON request analysis
		$this->get_request();
		// Process function validity check
		if (!is_callable($process_callback)) {
			throw new Exception("INVALID_PROCESS_FUNCTION");
		}
		// Process function execution
		$this->sent_object = call_user_func_array($process_callback, array($this->received_object));
		// Reponse check
		if (!($this->sent_object instanceof ThecallrRtCommandObject)) {
			throw new Exception("INVALID_PROCESS_FUNCTION_RESPONSE");
		}
		// Send response
		$this->write_response();
		// Response
		return TRUE;
	}

	/**
	* JSON request analysis
	*/
	private function get_request() {
		// "JSON" string is contained ?
		if (!array_key_exists('HTTP_RAW_POST_DATA',$GLOBALS)){
			throw new Exception("EMPTY_REQUEST");
		}
		// JSON string decoding
		$this->received_json = $GLOBALS["HTTP_RAW_POST_DATA"];
		$receiveStdObject = json_decode($this->received_json);
		// Decoding check
		if (is_null($receiveStdObject) || !is_object($receiveStdObject)){
			throw new Exception("INVALID_REQUEST");
		}
		// ThecallrRtReceivedObject values initialization and allocation
		$this->received_object = new ThecallrRtReceivedObject();
		foreach ($this->received_object as $propertyName=>$v) {
			// All ThecallrRtReceivedObject properties must be filled
			if (property_exists($receiveStdObject,$propertyName)) {
				$this->received_object->$propertyName = $receiveStdObject->$propertyName;
			} else {
				throw new Exception("INVALID_PROPERTY [$propertyName]");
			}
		}
		// Response
		return TRUE;
	}

	/**
	* Send response
	*/
	private function write_response() {
		if ($this->sent_object instanceof ThecallrRtCommandObject) {
			// JSON encoding
			$this->sent_json = json_encode($this->sent_object);
			// Write response
			header("HTTP/1.1 200 OK");
			header("User-Agent: Realtime Basic Server 1.0");
			header("Content-Length: ".strlen($this->sent_json));
			header("Content-Type: application/json; charset=utf-8");
			header("Connection: close");
			echo $this->sent_json;
		}
	}


}

/**
* Received object from THECALLR during call initialization or in response of command execution
*
*/
class ThecallrRtReceivedObject {
	public $app;				// Application ID.
	public $callid;				// Unic call ID.
	public $request_hash;		// NULL for an inbound call, Call request ID for outbound call (returned by dialr/call.* methods)
	public $cli_name;			// Caller name.
	public $cli_number;			// Caller number.
	public $number;				// Callee number.
	public $command;			// Previous command.
	public $command_id;			// Previous command ID.
	public $command_result;		// Previous command result.
	public $command_error;		// Current command error.
	public $date_started;		// Call Date and Time.
	public $variables;			// (key/value) variables associated with the call.
	public $call_status;		// Call status:
								// 		- INCOMING_CALL : Incoming call
								// 		- UP : The call is live
								// 		- HANGUP : The call just hung up
								// 		- BUSY : The target number is busy (not us)
								// 		- NOANSWER : The call was not answered during given time
	public $cdr_field;			// Value copied from dialr/call.* methods
}

/**
* Sent object for command execution
*
*/
class ThecallrRtCommandObject {
	public $command_id;			// Response ID
	public $command;			// Command name to execute
	public $params;				// Command parameters
	public $variables;			// (key/value) variables associated with the call. These variables are sent back at each command return.

	/**
	* Constructor : properties initialization
	*/
	public function __construct() {
		$this->command_id 	= rand(100,999);
		$this->command 		= 'hangup';
		$this->params 		= new stdClass();
		$this->variables 	= new stdClass();
	}

	/**
	* Makes another call, and bridges the call on answer..
	* @param string $ringtone
	* @param string $cli
	* @param array $targets
	* @param string $whisper
	* @param string $cdr_field
	*/
	public function dialout($ringtone, $cli, $targets, $whisper, $cdr_field) {
		$this->command = 'dialout';
		$this->params->ringtone = $ringtone;
		$this->params->cli = $cli;
		$this->params->targets = $targets;
		$this->params->whisper = $whisper;
		$this->params->cdr_field = $cdr_field;
	}

	/**
	* Plays a Media.Library or say something with the Text-to-Speech.
	* @param string $media_id Media ID or Text to say.
	*/
	public function play($media_id) {
		$this->command = 'play';
		$this->params->media_id = $media_id;
	}

	/**
	* Plays a recording recorded with the "record" command.
	* @param string $media_file Temporary file name.
	*/
	public function play_record($media_file) {
		$this->command = 'play_record';
		$this->params->media_file = $media_file;
	}

	/**
	* Plays a Media and wait for a DTMF input at the same time.
	* @param string $media_id Prompt message.
	* @param int $attempts Maximum attempts.
	* @param int $max_digits Maximum digits.
	* @param int $timeout_ms Input timeout in milliseconds.
	*/
	public function read($media_id, $attempts, $max_digits, $timeout_ms) {
		$this->command = 'read';
		$this->params->media_id = $media_id;
		$this->params->attempts = $attempts;
		$this->params->max_digits = $max_digits;
		$this->params->timeout_ms = $timeout_ms;
	}

	/**
	* Record the user. The recording can be stopped by pressing the hash key '#', when silence is detected, or when maximum recording duration is reached.
	* @param int $silence (seconds) Stop recording on silence.
	* @param int $max_duration (seconds) Maximum recording duration.
	*/
	public function record($silence, $max_duration) {
		$this->command = 'record';
		$this->params->silence = $silence;
		$this->params->max_duration = $max_duration;
	}

	/**
	* Send DTMF digits.
	* @param string $digit Digits to send (0-9, , #).
	* @param int $timeout_ms (milliseconds) Amount of time between tones.
	* @param int $duration_ms (milliseconds) Duration of each digit.
	*/
	public function send_dtmf($digit, $timeout_ms, $duration_ms) {
		$this->command = 'send_dtmf';
		$this->params->digit = $digit;
		$this->params->timeout_ms = $timeout_ms;
		$this->params->duration_ms = $duration_ms;
	}

	/**
	* Wait for a few seconds.
	* @param int $wait (seconds) Time to wait.
	*/
	public function wait($wait) {
		$this->command = 'wait';
		$this->params->wait = $wait;
	}

	/**
	* Wait until silence is detected, or timeout is reached.
	* @param int $silence_ms (milliseconds) Minimum silence duration.
	* @param int $iterations Number of times to try.
	* @param int $timeout_ms (seconds) Global timeout if silence is not detected.
	*/
	public function wait_for_silence($silence_ms, $iterations, $timeout_ms) {
		$this->command = 'wait_for_silence';
		$this->params->silence_ms = $silence_ms;
		$this->params->iterations = $iterations;
		$this->params->timeout_ms = $timeout_ms;
	}

	/**
	 * Start call recording
	 * @param string $announce Media to announce the call is being recorded. Set to 0 to ignore.
	 */
	public function start_call_recording($announce = 0) {
		$this->command = 'start_call_recording';
		$this->params->announce = $announce;
	}

	/**
	 * Stop call recording
	 * @param string $announce Media to announce the call is not being recorded anymore. Set to 0 to ignore.
	 */
	public function stop_call_recording($announce = 0) {
		$this->command = 'stop_call_recording';
		$this->params->announce = $announce;
	}

	/**
	 * Dynamically create a conference (if needed) and send the current call in it. Can be used to bridge calls together. This is a final command.
	 * @param string $id Conference ID. Obviously, you have to set the same conference id on the calls you wish to bridge...
	 * @param string $welcome Media played before joining the conference (and before optional pin_prompt)
	 * @param string $waiting Media played in a loop while there is only one participant.
	 * @param string $pin_prompt Media played to ask for a PIN code. Will be repeated until a PIN is entered.
	 * @param string $pin_error Media played when the PIN is invalid.
	 * @param string $pin_valid Media played when the PIN is valid.
	 * @param string $pin_code PIN code to ask for. Callee must press # after the PIN. Leave empty for auto-joining. [0-9]
	 * @param int $pin_max_tries How many times do we ask for the PIN? [1..10]
	 * @param int $pin_wait The number of seconds to wait for a digit response. [0..30]
	 * @param bool $auto_leave_when_alone Automatically leave the conference room when you're the last participant. Only applies when someone leaves - it does not apply when you are joining and you are first.
	 * @param string $auto_leave_announce Media played when leaving the conference room because you are the last participant. [format=rt_media]
	 * @param string $user_join Media played when someone enters the conference room [format=rt_media]
	 * @param string $user_leave Media played when someone leaves the conference room [format=rt_media]
	 */
	public function conference($id, $welcome = 0, $waiting = 0,
	                           $pin_prompt = 0, $pin_error = 0, $pin_valid = 0, $pin_code = '',
	                           $pin_max_tries = 3, $pin_wait = 5,
	                           $auto_leave_when_alone = FALSE, $auto_leave_announce = 0,
	                           $user_join = 0, $user_leave = 0) {
		$this->command = 'conference';
		$this->params->id = $id;
		$this->params->welcome = $welcome;
		$this->params->waiting = $waiting;
		$this->params->pin_prompt = $pin_prompt;
		$this->params->pin_error = $pin_error;
		$this->params->pin_valid = $pin_valid;
		$this->params->pin_code = $pin_code;
		$this->params->pin_max_tries = $pin_max_tries;
		$this->params->pin_wait = $pin_wait;
		$this->params->auto_leave_when_alone = $auto_leave_when_alone;
		$this->params->auto_leave_announce = $auto_leave_announce;
		$this->params->user_join = $user_join;
		$this->params->user_leave = $user_leave;
	}

	/**
	 * Simple conference bridge
	 * @param string $id Conference room ID. Obviously, you have to set the same id on the calls you wish to bridge...
	 * @param bool $auto_leave_when_alone Automatically leave the conference room when you're the last participant. Only applies when someone leaves - it does not apply when you are joining and you are first.
	 */
	public function simple_conference($id, $auto_leave_when_alone = TRUE) {
		$this->command = 'simple_conference';
		$this->params->id = $id;
		$this->params->auto_leave_when_alone = $auto_leave_when_alone;
	}

	/**
	 * Hangup a specific callid
	 * @param int $callid The callid you wish to hang up
	 */
	public function hangup_callid($callid) {
		$this->command = 'hangup_callid';
		$this->params->callid = $callid;
	}

	/**
	* Hangup the call.
	*/
	public function hangup() {
		$this->command = 'hangup';
	}
}