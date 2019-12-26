<?php 
// Debugging
define("DEBUG", 1);

// Site Root, Doc Root
define('SITEROOT','/onerouses/');
define('DOCROOT', $_SERVER['DOCUMENT_ROOT'] . SITEROOT);
define("LIBPATH", DOCROOT . "lib/");

define('COC_API_URL', 'https://api.clashofclans.com/v1/');
define('COC_AUTH_TOKEN', '[api key here]');
define('MY_CLAN', '[clan ID here]');

date_default_timezone_set ('America/Chicago');


/**
 * Name: import
 * Purpose: Parse a string as a library path for including files in other PHP files
 * Arguments:
 *  - $libString: string to parse as import library
 * Returns: void
 */
$_IMPORT_ARRAY = array();
function import($libString = "") {
	if(!is_string($libString) || $libString == "") {
		error_log('FATAL ERROR: Specified class file path to must be a non-empty string!');
		printf("%s", "An unrecoverable error has occurred.  Please try again.  If problem persists, please contact the webmaster.");
		exit;
	} //end if
	
	global $_IMPORT_ARRAY;
	if(!isset($_IMPORT_ARRAY[$libString]) || !$_IMPORT_ARRAY[$libString]) {
		$_IMPORT_ARRAY[$libString] = 1;
		
		$importPathArray = explode(".", $libString);
		$filePath = LIBPATH . implode("/", $importPathArray) . ".php";
		if(file_exists($filePath))
			require_once($filePath);
	} //end if
	
	return;
} //end function import()

//check to make sure we're connecting through SSL
if(!DEBUG) {
	import("utils.Utils");
	if(!Utils::isSecureConnection()) {
		http_response_code(403); //permanent redirect
		echo json_encode(array(
			"ResponseCode" => 403,
			"ErrorMessage" => "Request submitted over insecure connection. Please use HTTPS for all requests."
		));
		exit;
	} //end if
} //end if
?>