<?php
/**
* Real time application scenario step management webservice
* @author Tatunca <fw@thecallr.com>
*/

class ThecallrRtExtendedServer {

	public $received_json;							// JSON request received from THECALLR
	public $received_object;						// Object received from THECALLR

	public $sent_object;							// Object sent back to THECALLR
	public $sent_json;								// JSON response sent back to THECALLR

	public $apps = array();							// Declared application list ($app[_APP_ID__] = object ThecallrRtApp)
	public $apps_step_callback = array();			// Each step executed function list ($app[_APP_ID__] = function)

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
		//JSON extension check
		if (function_exists('json_encode') === FALSE){
			throw new Exception("UNAVAILABLE_JSON_PHP_EXTENSION");
		}
		// Response
		return TRUE;
	}

	/**
	* Application declaration
	* @param string $app_id Application ID
	* @param object $app_object Application scenario [object=ThecallrRtApp]
	* @param callable $on_step_change_callback Each step executed callback function (string(__FUNCTION__) or array(__CLASS__, __METHOD__))
	* @return bool Declaration status
	*/
	public function declare_app($app_id,$app_object,$on_step_change_callback=null) {
		// Save application
		$this->apps[$app_id] = $app_object;
		// Save callback function
		if (!is_null($on_step_change_callback) && is_callable($on_step_change_callback)) {
			$this->apps_step_callback[$app_id] = $on_step_change_callback;
		}
		// Response
		return TRUE;
	}

	/**
	* Start server
	*/
	public function start() {
		// JSON request analysis
		$this->get_request();
		// Request processing
		$this->execute_request();
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
		// RealtimeReceivedObject values initialization and allocation
		$this->received_object = new RealtimeReceivedObject();
		foreach ($this->received_object as $propertyName=>$v) {
			// All RealtimeReceivedObject properties must be filled
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
	* Request processing
	*/
	private function execute_request() {
		// Response initialization (RealtimeResponseObject object)
		$this->sent_object = new RealtimeResponseObject();
		// Default values allocation to properties
		$this->sent_object->command_id 	= 	rand(100,999);
		$this->sent_object->command		= 	'hangup';
		$this->sent_object->params 		= 	new stdClass();
		$this->sent_object->variables 	= 	new stdClass();
		// RealtimeReceivedObject object check
		if ($this->received_object instanceof RealtimeReceivedObject) {
			// Request analysis and processing depending of application
			if (array_key_exists($this->received_object->app,$this->apps) && $this->apps[$this->received_object->app] instanceof ThecallrRtApp) {
				$app = $this->apps[$this->received_object->app];
				// Error management
				if (!empty($this->received_object->command_error)) {
					// Retrieve step that generates error from previous step parameters
					if (isset($this->received_object->variables->step)) {
						$next_step = $app->get_next_step($this->received_object->variables->step,$this->received_object->command_result);
					} else {
						$next_step = $app->get_next_step(0);
					}
					// Replace previous step values by those of the step that generates error
					$this->received_object->variables->step = $next_step['step'];
					$this->received_object->command_result = '_error_';
				}
				// During a "read" command, a variable name can be specified to store command result.
				// This variable name is affected to "step_result_var_name" property of "variables"
				// property of "RealtimeReceivedObject" object.
				if (property_exists($this->received_object->variables,'step_result_var_name')) {
					// Result allocation and save in specified variable
					$var = $this->received_object->variables->step_result_var_name;
					$this->received_object->variables->$var = $this->received_object->command_result;
					// "step_result_var_name" property removal
					unset($this->received_object->variables->step_result_var_name);
				}
				// Next step definition depending of status and result
				$next_step = null;
				switch ($this->received_object->call_status){
					case 'INCOMING_CALL':
						// Incoming call starts, first step is retrieved
						$next_step = $app->get_next_step(0);
					break;
					case 'UP':
						if (isset($this->received_object->variables->step)) {
							// "step" property is filled
							$next_step = $app->get_next_step($this->received_object->variables->step,$this->received_object->command_result);
						} else {
							// Incoming call starts, first step is retrieved
							$next_step = $app->get_next_step(0);
						}
					break;
					case 'BUSY':
					case 'NOANSWER':
					case 'REJECTED':
					case 'HANGUP':
						// Call is finished. No command is needed but we send "end"
						$next_step = array('command'=>'end');
					break;
				}
				// Command allocation to RealtimeResponseObject object
				if (!is_null($next_step) && array_key_exists('command',$next_step)) {
					$this->sent_object->command = $next_step['command'];
					// Variables reallocation
					$this->sent_object->variables = $this->received_object->variables;
					// Current step save
					$this->sent_object->variables->step = (array_key_exists('step',$next_step))?$next_step['step']:0;
				}
				// Parameters processing and allocation
				if (!is_null($next_step) && array_key_exists('parameters',$next_step)) {
					$this->sent_object->params = (object) $next_step['parameters'];
					// Replace tags
					$this->sent_object->params = $this->replace_tags_in_parameters($this->sent_object->params, $this->sent_object->variables);
				}
				// During a "read" command, a variable name can be specified to store command result.
				// This variable name is affected to "step_result_var_name" property of "variables"
				// property of "RealtimeResponseObject" object in order to be processed during result recovery.
				if (!is_null($next_step) && array_key_exists('result_var_name',$next_step)) {
					$this->sent_object->variables->step_result_var_name = $next_step['result_var_name'];
				}
				// Callback function execution
				// 1st argument: RealtimeReceivedObject object
				// 2nd argument: RealtimeResponseObject object
				if (array_key_exists($this->received_object->app,$this->apps_step_callback)) {
					$callback = call_user_func_array($this->apps_step_callback[$this->received_object->app], array($this->received_object, $this->sent_object));
				}
			}
		}
		// Response
		return $this->sent_object;
	}

	/**
	* Send response
	*/
	private function write_response() {
		if ($this->sent_object instanceof RealtimeResponseObject) {
			// JSON encoding
			$this->sent_json = json_encode($this->sent_object);
			// Write response
			header("HTTP/1.1 200 OK");
			header("User-Agent: Realtime Server 1.0");
			header("Content-Length: ".strlen($this->sent_json));
			header("Content-Type: application/json; charset=utf-8");
			header("Connection: close");
			echo $this->sent_json;
		}
	}

	/**
	* Replace tags inserted in $parametres object
	* A tag is identified by a name surrounded by {} (ex: {the_tag})
	* It is replaced by "the_tag" property value of $tags object
	* Unknown tags are replaced by an empty string
	* @param object $parameters Command parameters
	* @param object $tags Tags list (tag->value)
	* @return object $parameters analysé et traité
	*/
	private function replace_tags_in_parameters($parameters, $tags) {
		foreach ($parameters as &$pValue) {
			if (is_string($pValue) && preg_match_all("#\{([^}]*)\}#",$pValue,$matches)) {
				foreach ($matches[1] as $match) {
					$value = (property_exists($tags,$match))?$tags->$match:'';
					$pValue = str_replace('{'.$match.'}',$value,$pValue);
				}
			}else if (is_object($pValue) || is_array($pValue)) {
				$pValue = $this->replace_tags_in_parameters($pValue, $tags);
			}
		}
		return $parameters;
	}

}

/**
* Received object from THECALLR during call initialization  or in response of command execution
*
*/
class RealtimeReceivedObject {
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
								// 		- REJECTED : Our system rejected the call
	public $cdr_field;			// Value copied from dialr/call.* methods
}

/**
* Sent object for command execution
*
*/
class RealtimeResponseObject {
	public $command_id;			// Response ID
	public $command;			// Command name to execute
	public $params;				// Command parameters
	public $variables;			// (key/value) variables associated with the call. These variables are sent back at each command return.
}
?>