<pre>
<?php
/**
* <h3>K. Manage audio media</h3>
*/

/**
* <h4>1. Client initialization and configuration</h4>
*/
require('../src/ThecallrClient.php');

$thecallrLogin = '__LOGIN__';
$thecallrPassword = '__PASSWORD__';
$THECALLR = new ThecallrClient($thecallrLogin, $thecallrPassword);

/**
* <h4>2. Upload a media in library</h4>
* <p>Message creation follows several steps:<br />
* &nbsp;&nbsp;- 1/ Media creation.</li><br />
* &nbsp;&nbsp;- 2/ Content load or definition.<br />
* &nbsp;&nbsp;- 3/ Assign one or more keywords.<br />
* About audio media, please see <a href="api/services/media/#media.set_content"> documentation</a>.</p>
*/
try {
	
	// 1. Media definition
	$msg_name = 'My test media with audio file';
	// Media creation and ID recovery
	$msg_id_01 = $THECALLR->call('media.create', $msg_name);
	
	// 2. Content definition
	$file_text_content = 'Waiting music 01';
	// Audio file content recovery
	$file_content = file_get_contents('assets/Media_sample.wav');
	// Base 64 content encoding
	$file_encoded = base64_encode($file_content);
	// Assign the content to the created media
	$content = $THECALLR->call('media.set_content', $msg_id_01, $file_text_content, $file_encoded);
	
	// 3. Keywords definition
	$keywords = new stdClass();
	$keywords->CATEGORY = array('WELCOME','RINGTONE');	// Category tag
	$keywords->VERSION = array('1');			// Custom tag
	$keywords->SOURCE = array('SDKexample');		// Custom tag
	$tags = $THECALLR->call('media.set_tags', $msg_id_01, $keywords);
	
	// ID display
	echo 'File Media ID : ' . $msg_id_01 . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>3. Create a Text-To-Speech media in the library</h4>
* <p>Like the previous example, media creation follows several steps.<br />
* Retrieve available voice list in <a href="api/services/media/#media.set_content_with_tts">documentation</a>.</p>
*/
try {
	
	// 1. Media definition
	$msg_name = 'My test media with TTS';
	// Media creation and ID recovery
	$msg_id_02 = $THECALLR->call('media.create', $msg_name);
	
	// 2. Content definition
	$msg_text = 'Hello ! Welcome !';
	// Voice
	$msg_voice = 'TTS_FR-FR_AUDREY';
	// Options
	$msg_options = new stdClass();
	$msg_options->rate = 50;
	// Assign the content to the created media
	$content = $THECALLR->call('media.set_content_with_tts', $msg_id_02, $msg_text, $msg_voice, $msg_options);
	
	// 3. Keywords definition (key => value)
	$keywords = new stdClass();
	$keywords->CATEGORY = array('WELCOME');		// Category tag
	$keywords->LANGUAGE = array('fr_FR');		// Language tag
	$keywords->SOURCE = array('SDKexample');	// Custom tag
	$tags = $THECALLR->call('media.set_tags', $msg_id_02, $keywords);
	
	// ID display
	echo 'TTS Media ID : ' . $msg_id_02 . '<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>4. Retrieve media list from library</h4>
*/
try {
	
	// Search tags definition
	$tags = new stdClass();
	$tags->SOURCE = 'SDKexample';
	
	// Search media list containing "SDK_example" in "source" tag
	$msg_list = $THECALLR->call('media.get_library_by_tags', $tags);
	
	// Media display
	echo '<pre>';
	print_r($msg_list);
	echo '</pre>';
	
} catch (Exception $error) {
	die($error->getMessage());
}

/**
* <h4>5. Delete media</h4>
*/
try {
	
	// Media IDs to delete
	$msg_to_delete_01 = $msg_id_01;
	$msg_to_delete_02 = $msg_id_02;
	
	// Delete 01 media
	$status01 = $THECALLR->call('media.delete', $msg_to_delete_01);
	// Execution result
	echo 'Media #' . $msg_to_delete_01 . ' deleted<br />';
	
	// Delete 02 media
	$status02 = $THECALLR->call('media.delete', $msg_to_delete_02);
	// Execution result
	echo 'Media #' . $msg_to_delete_02 . ' deleted<br />';
	
} catch (Exception $error) {
	die($error->getMessage());
}
?>
</pre>