<?php
include("includes/constants.php");
import("api.ClashAPI");
import("utils.Logger");

$res = array();
try {
	$content =  json_decode(ClashAPI::getCurrentLeagueGroup(MY_CLAN));
	$res = array(
		"responseCode" => 200,
		"content" => $content
	);
} //end try
catch(Exception $e) {
	Logger::write(Logger::ERROR, "Error retrieving league group information.", $e);
	Logger::write(Logger::DEBUG, $e->getTraceAsString());
	$res = array(
		"responseCode" => 503,
		"errorMessage" => $e->getMessage()
	);
} //end catch

http_response_code($res["responseCode"]);
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
echo json_encode($res);
?>