<?php
include("includes/constants.php");
import("api.ClashAPI");
import("utils.Logger");
import("utils.Utils");

$limit = isset($_GET['limit']) ? Utils::sanitizeInput($_GET['limit']) : -1;

$res = array();
try {
	$content =  json_decode(ClashAPI::getWarLog(MY_CLAN, $limit));
	if (isset($content->items)) {
		for($i = 0; $i < count($content->items); ++$i) {
			$dateArr = date_parse($content->items[$i]->endTime);
			$content->items[$i]->endTime = $dateArr['year'] . "-" . $dateArr['month'] . "-" . $dateArr['day'] . " 12:00:00PM";
		} //end for
	} //end if
	
	$res = array(
		"responseCode" => 200,
		"content" => $content
	);
} //end try
catch(Exception $e) {
	Logger::write(Logger::ERROR, "Error retrieving war log information.", $e);
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