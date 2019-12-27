<?php
include("includes/constants.php");
import("api.ClashAPI");
import("utils.Logger");

$res = array();
try {
	header("Content-type: application/json");
	header("Access-Control-Allow-Origin: *");
	echo ClashAPI::getClanInfo(MY_CLAN);
	exit();
} //end try
catch(Exception $e) {
	Logger::write(Logger::ERROR, "Error saving assessment answer data.", $e);
	Logger::write(Logger::DEBUG, $e->getTraceAsString());
	$res = array(
		"ResponseCode" => 500,
		"ErrorMessage" => $e->getMessage()
	);
} //end catch

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
echo json_encode($res);
?>