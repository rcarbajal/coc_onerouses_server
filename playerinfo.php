<?php
include("includes/constants.php");
import("api.ClashAPI");
import("utils.Logger");
import("utils.Utils");

$res = array();
$playerTag = isset($_GET['player']) ? Utils::sanitizeInput($_GET['player']) : "";

try {
	if ($playerTag != "") {
		$content = json_decode(ClashAPI::getPlayerInfo($playerTag));
		$res = array(
			"responseCode" => 200,
			"content" => $content
		);
	} //end if
	else
		$res = array(
			"responseCode" => 400,
			"errorMessage" => "No player specified"
		);
} //end try
catch(Exception $e) {
	Logger::write(Logger::ERROR, "Error retrieving player information.", $e);
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