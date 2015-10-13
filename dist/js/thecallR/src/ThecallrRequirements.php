<?php
class ThecallrRequirements {
	
	const REQUIRED_PHP_VERSION = '5.2.0';
	
	public static function check() {
		// Php version set up
		$installed_php_version = phpversion();
		
		// Php version check
		$test_php_version = version_compare($installed_php_version, TheCallrRequirements::REQUIRED_PHP_VERSION, '>=');
		if ($test_php_version === FALSE){
			throw new Exception("INVALID_PHP_VERSION [".TheCallrRequirements::REQUIRED_PHP_VERSION." required]");
		}
		
		// JSON extension check
		if (function_exists('json_encode') === FALSE){
			throw new Exception("UNAVAILABLE_PHP_EXTENSION [json]");
		}
		
		// CURL extension check
		if (function_exists('curl_init') === FALSE){
			throw new Exception("UNAVAILABLE_PHP_EXTENSION [curl]");
		}
		
		// Response
		return TRUE;
	}
	
}
?>