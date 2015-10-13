<?php
/**
* THECALLR application scenario definition
* @author Tatunca <fw@thecallr.com>
*/

class ThecallrRtApp {
	
	public $app_id;
	public $step = array();
	
	/** Constructor : application definition
	*/
	public function __construct($app_id) {
		$this->app_id = $app_id;
		// hangup command definition for not answered call management
		$this->add_step_command(9998,'hangup');
	}
	
	/**
	* Returns the configuration of the next step to execute depending of the current step result
	* @param int $step_id Current step ID
	* @param string $step_result Current step execution result
	* @return object Next step configuration
	*/
	public function get_next_step($step_id,$step_result=null) {
		if ($step_id == 0) {
			// If it is an incoming call, the first step is returned
			$sid = array_keys($this->step);
			sort($sid);
			return $this->step[current($sid)];
		}else if (array_key_exists($step_id,$this->step) && array_key_exists('connections',$this->step[$step_id])) {
			$next_step_default = null;
			// Looking for the next step depending of step result
			foreach ($this->step[$step_id]['connections'] as $connection) {
				if ($connection['step_result'] == $step_result) {
					$next_step = $connection['next_step_id'];
					// Retrieve and return corresponding step
					if (array_key_exists($next_step,$this->step)) {
						return $this->step[$next_step];
					}
					// Break
					break;
				}else if ($connection['step_result'] == '_default_') {
					$next_step_default = $connection['next_step_id'];
				}
			}
			// If $next_step does not match but there is a default step
			if (!is_null($next_step_default) && array_key_exists($next_step_default,$this->step)) {
				return $this->step[$next_step_default];
			}
		}
		// Default response
		return $this->step[9998];
	}
	
	/** Add a step command to the application
	*/
	public function add_step_command($step_id,$command,$parameters=null,$result_var_name=null) {
		$command = array('step'=>$step_id,'command'=>$command);
		if ($command['command'] != 'hangup'){
			$command['parameters'] = $parameters;
			$command['connections'] = array();
			// If the result must be saved in a variable
			if (!is_null($result_var_name) && is_string($result_var_name)) {
				$command['result_var_name'] = $result_var_name;
			}
		}
		$this->step[$step_id] = $command;
	}
	
	/** Add a connection condition depending of an execution command
	*/
	public function add_step_command_connection($step_id,$step_result,$next_step_id) {
		if (array_key_exists($step_id,$this->step)) {
			$connection = array('step_result'=>$step_result,'next_step_id'=>$next_step_id);
			$this->step[$step_id]['connections'][] = $connection;
		}
	}
	
}
?>