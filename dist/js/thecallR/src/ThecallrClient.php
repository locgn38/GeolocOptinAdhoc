<?php
/**
* THECALLR webservice communication library
* @author Tatunca <fw@thecallr.com>
*/

class ThecallrClient {
	
	private $url = 'https://api.thecallr.com';				// THECALLR webservice URL
	private $auth;											// Access identifiers
	
	const REQUIRED_PHP_VERSION = '5.2.0';					// PHP mininmum version
	
	/**
	* Initialization
	* @param string $thecallr_login Login
	* @param string $thecallr_password Password
	*/
	public function __construct($thecallr_login, $thecallr_password, $check_configuration = FALSE) {
		//Identifiers declaration
		$this->auth = "{$thecallr_login}:{$thecallr_password}";
		// Configuration check
		if ($check_configuration === TRUE) {
			$this->check_configuration();
		}
	}
	
	/**
	* Send a request to THECALLR webservice
	*/
	public function call() {
		// Affectation
		$params = func_get_args();
		$method = (count($params) > 0)?array_shift($params):'';
		// Execution
		return $this->send($method, $params);
	}
	
	/**
	* Send a request to THECALLR webservice
	*/
	public function send($method, $params = array(), $id = null) {
		// Create request format
		$request = $this->create_request($method, $params, $id);
		// Request encoding
		$jsonRequest = json_encode($request);
		// Headers
		$headers = array(
			'Expect:',
			'Content-Type: application/json-rpc; charset=utf-8',
		);
		// Curl initialization
		$curl = curl_init();
		// Curl configuration
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, TRUE);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonRequest);
		curl_setopt($curl, CURLOPT_USERPWD, $this->auth);
		// Curl execution
		$buffer = curl_exec($curl);
		$error = curl_error($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		// Error management
		if (strlen($error) > 0){
			throw new ThecallrClientException("CURL_REQUEST_ERROR", 4, array('curl_error'=>$error));
		}else if ($code != 200) {
			throw new ThecallrServerException("HTTP_CODE_ERROR", -1, array('http_code'=>$code));
		}
		// Response management
		$result = $this->parse_response($buffer);
		// Return
		return $result;
	}
	
	/**
	* System configuration check
	*/
	public function check_configuration() {
		// Php version check
		$version = array('required_version'		=>	self::REQUIRED_PHP_VERSION,
						 'installed_version'	=>	phpversion()
						 );
		if (version_compare($version['installed_version'], $version['required_version'], '>=') === FALSE) {
			throw new ThecallrClientException('INVALID_PHP_VERSION', 1, $version);
		}
		// JSON extension check
		if (function_exists('json_encode') === FALSE){
			throw new ThecallrClientException("UNAVAILABLE_PHP_EXTENSION [json]", 2);
		}
		
		// CURL extension check
		if (function_exists('curl_init') === FALSE){
			throw new ThecallrClientException("UNAVAILABLE_PHP_EXTENSION [curl]", 3);
		}
		// Response
		return TRUE;
	}
	
	/**
	* Request format
	*/
	private function create_request($method, $params = array(), $id = null) {
		$request = new stdClass;
		$request->id = (!is_int($id) || $id <= 0)?rand(100,999):$id;
		$request->jsonrpc = '2.0';
		$request->method = $method;
		$request->params = (is_array($params))?$params:array();
		return $request;
	}
	
	/**
	* Response analysis
	*/
	private function parse_response($json_response) {
		$response = json_decode($json_response);
		if (is_object($response) && property_exists($response,'result') && !is_null($response->result)){
			return $response->result;
		}else if (is_object($response) && property_exists($response,'error') && !is_null($response->error)){
			throw new ThecallrServerException($response->error->message,$response->error->code);
		}else{
			throw new ThecallrServerException('INVALID_RESPONSE', -2, array('response'=>$json_response));
		}
	}
	
}

/**
* JsonRpc Client Exception
*/
class ThecallrClientException extends JsonRpcException {}

/**
* JsonRpc Server Exception
*/
class ThecallrServerException extends JsonRpcException {}

/**
* JsonRpc Exception
*/
class JsonRpcException extends Exception {
	
	private $data;
	
	public function __construct($message, $code = 0, $data = null) {
		parent::__construct($message, $code);
		$this->data = $data;
	}
	
	public function getData() {
		return $this->data;
	}
	
}
?>