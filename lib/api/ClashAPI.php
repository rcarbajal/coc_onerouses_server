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
	
	public static function getPlayerInfo($playerTag) {
		$url = sprintf(COC_API_URL . self::$urls["player"]["info"]["url"], urlencode($playerTag));
		return self::getResponse($url);
	} //end method getPlayerInfo
	
	public static function getWarLog($clanId, $limit = -1) {
		$url = sprintf(COC_API_URL . self::$urls["clan"]["warlog"]["url"], urlencode($clanId));
		if ($limit > 0)
			$url .= "?limit=$limit";
		return self::getResponse($url);
	} //end method getWarLog
	
	public static function getCurrentLeagueGroup($clanId) {
		$url = sprintf(COC_API_URL . self::$urls["clan"]["leaguegroup"]["url"], urlencode($clanId));
		$response = self::getResponse($url);
		$response = str_replace("\"members\"", "\"memberList\"", $response);
		return $response;
	} //end method getCurrentLeagueGroup
	
	public static function getCurrentLeagueWar($warId) {
		$url = sprintf(COC_API_URL . self::$urls["clan"]["currentleaguewar"]["url"], urlencode($warId));
		return self::getResponse($url);
	} //end method getCurrentLeagueWar
	
	public static function getCurrentWar($clanId) {
		$url = sprintf(COC_API_URL . self::$urls["clan"]["currentwar"]["url"], urlencode($clanId));
		return self::getResponse($url);
	} //end method getCurrentWar

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
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //REMOVE THIS LINE WHEN PUBLISHING TO WEB HOST

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

		//check for errors
		if(intval($httpcode) != 200) {
			$msg = "Error occurred while attempting to call to remote URL. Status code: $httpcode; Error JSON: $response;";
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