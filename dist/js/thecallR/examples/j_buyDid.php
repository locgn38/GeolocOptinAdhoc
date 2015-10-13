<pre>
<?php
/**
* <h3>J. Reserve and buy DIDs</h3>
*/

/**
* <h4>1. Client initialization and configuration</h4>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Check your store quota</h4>
* <p>Your account may have a store quota : maximum number of purchase over a period.</p>
*/
try {
	
	// get_quota_status
	$quota_status = $THECALLR->call('did/store.get_quota_status');
	
	// show quotas
	echo '<pre>';
	print_r($quota_status);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. List countries, DID types, and area codes</h4>
* <p>Reserving and buying DIDs is done by area code.</p>
*/
try {
	
	// Countries available in the store
	$countries = $THECALLR->call('did/areacode.countries');
	$country = $countries[0]->code; // First country 
	
	// DID types
	$types = $THECALLR->call('did/areacode.types', $country);
	$type = $types[0]->code;	// First type
	
	// Get area codes for a country and a type
	$areacodes = $THECALLR->call('did/areacode.get_list', $country, $type);
	
	// Print area codes
	echo '<pre>';
	print_r($areacodes);
	echo '</pre>';
	
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>4. Get a quote</h4>
* <p>Get a quote without reserving</p>
*/
try {
	
	// Area code ID (1 = FR-GEOGRAPHIC-ILE-DE-FRANCE)
	$area_code = 1;
	// DID class (CLASSIC or GOLD)
	$class = 'CLASSIC';
	// Quantity you plan to buy
	$quantity = 5;
	
	// This method will tell you how much it would cost 
	$quote = $THECALLR->call('did/store.get_quote', $area_code, $class, $quantity);
	
	// Show quote
	echo '<pre>';
	print_r($quote);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>5. Reserve DIDs before buying</h4>
* <p>You must reserve before buying.</p>
*/
try {
	
	// Area code ID you wish to buy
	$area_code = 1;
	// DID class (CLASSIC or GOLD)
	$class = 'CLASSIC';
	// Quantity you want to buy
	$quantity = 5;
	// Do you want random DIDs or sequential sets? (RANDOM ou SEQUENTIAL)
	$mode = 'RANDOM';
	
	// Reserve the DIDs
	$lock = $THECALLR->call('did/store.reserve', $area_code, $class, $quantity, $mode);
	
	// You need the token to buy
	echo "Token : {$lock->token}<br />";
	
	// Show the DIDs reserved
	echo '<pre>';
	print_r($lock->items);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>6. Buy</h4>
* <p>Buy the DIDs you just reserved.</p>
*/
if (is_object($lock) && property_exists($lock, 'token') {
	try {
		
		// Buy with the reservation token
		$order = $THECALLR->call('did/store.buy_order', $lock->token);
		
		// Show your order confirmation
		echo '<pre>';
		print_r($order);
		echo '</pre>';
		
	} catch (Exception $error) {
		die($error->getMessage());
	}
}

/**
* <h4>6. Cancel a DID subscription</h4>
* <p>This method lets you cancel a DID subscription</p>
*/
try {
	
	// DID id
	$did_hash = 'DIDHASH';
	
	// Request a cancel of a DID subscription
	$status = $THECALLR->call('did/store.cancel_subscription', $did_hash);
	
	// Result display
	echo '<pre>';
	print_r($status);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

?>
</pre>