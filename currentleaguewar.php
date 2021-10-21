<?php
include("includes/constants.php");
import("api.ClashAPI");
import("utils.Logger");
import("utils.Utils");

$res = array();
$warTag = isset($_GET['wartag']) ? Utils::sanitizeInput($_GET['wartag']) : "";

try {
	if ($warTag != "") {
		$content = json_decode(ClashAPI::getCurrentLeagueWar($warTag));
		$res = array(
			"responseCode" => 200,
			"content" => $content
		);
	} //end if
	else
		$res = array(
			"responseCode" => 400,
			"errorMessage" => "No war ID specified"
		);
} //end try
catch(Exception $e) {
	Logger::write(Logger::ERROR, "Error retrieving curent league war information.", $e);
	Logger::write(Logger::DEBUG, $e->getTraceAsString());
	$res = array(
		"responseCode" => 500,
		"errorMessage" => $e->getMessage()
	);
} //end catch

http_response_code($res["responseCode"]);
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
echo json_encode($res);
?>