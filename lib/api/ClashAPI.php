<?php
import("exceptions.DataException");

class ClashAPI {
	private static $urls = array(
		"clan" => array(
			"search" => array(
				"url" => "clans/",
				"params" => array(
					"name", "warFrequency", "locationId", "minMembers", "maxMembers", "minClanPoints", "minClanLevel", "limit", "after", "before", "labelIds"
				)
			),
			"info" => array(
				"url" => "clans/%s"
			),
			"members" => array(
				"url" => "clans/%s/members",
				"params" => array(
					"limit", "after", "before"
				)
			),
			"warlog" => array(
				"url" => "clans/%s/warlog",
				"params" => array(
					"limit", "after", "before"
				)
			),
			"currentwar" => array(
				"url" => "clans/%s/currentwar"
			),
			"leaguegroup" => array(
				"url" => "clans/%s/currentwar/leaguegroup"
			),
			"currentleaguewar" => array(
				"url" => "clanwarleagues/wars/%s"
			)
		),
		"player" => array(
			"info" => array(
				"url" => "players/%s"
			)
		),
		"location" => array(
			"info" => array(
				"url" => "locations/%s"
			)
		)
	);

	public static function getClanInfo($clanId) {
		$url = sprintf(COC_API_URL . self::$urls["clan"]["info"]["url"], urlencode($clanId));
		return self::getResponse($url);
	} //end method getClanInfo

	private static function getResponse($url) {
		//set header information
		$headerArr = array(
			"content-type: application/json",
			sprintf("Authorization: Bearer %s", COC_AUTH_TOKEN)
		);

		//initiate and execute cURL operations
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

		//check for errors
		if(intval($httpcode) != 200) {
			$msg = "Error occurred while attempting to send push notification. Status code: $httpcode; Error JSON: $response;";
			if(!empty(curl_error($ch))) {
				$msg .= "; cURL Error: (" . curl_errno($ch) . ") " . curl_error($ch);
			} //end if

			curl_close($ch);
			throw new DataException(__METHOD__ . "::" . $msg);
		} //end if

		//close cURL session
		curl_close($ch);

		return $response;
	} //end method getResponse
} //end class ClashAPI
?>