<?php
/**
 * Class Utils
 *
 * Class that houses general common application utility methods, e.g. sending
 * emails, cleaning user input for database storage, etc.
 */
class Utils {
	const DEFAULT_EMAIL = "robert@holycow2.com";
	const DEFAULT_MIME = "text/plain";
	const DEFAULT_BCC = "Robert Carbajal <robert@holycow2.com>";

	/**
	 * Name: sanitizeInput
	 * Visibility: public static
	 * Purpose:
	 * 	Cleans user input of any characters that might cause harm to the application or it's associated database.
	 * Arguments:
	 *	- string $text: string of text that is to be cleaned
	 * Returns: string of cleaned text
	 */
	public static function sanitizeInput($text) {
		$fixedText = "";

		/* determine how to handle slashes */
		if(get_magic_quotes_gpc()) //if slashes are added automatically, strip them
			$fixedText = trim(htmlentities(strip_tags(stripslashes(self::transcribe_cp1252_to_latin1($text)))));
		else //if slashes are left out, then we need to do nothing but apply remaining sanitize routines
			$fixedText = trim(htmlentities(strip_tags(self::transcribe_cp1252_to_latin1($text))));

		return $fixedText;
	} //end static method sanitizeInput()

	/**
	 * Name: isValidEmail
	 * Visibility: public static
	 * Purpose:
	 * 	Determines if a given string is a valid email address.
	 * Arguments:
	 *	- string $email: string of text to be validated
	 * Returns: true if the string represents a valid email address, false otherwise
	 */
	public static function isValidEmail($email) {
		return preg_match("/^[A-Za-z0-9._%-]+@([A-Za-z0-9-]+\.)+[A-Za-z]{2,4}$/", $email);
	} //end static method isValidEmail

	/**
	 * Name: sendEmail
	 * Visibility: public static
	 * Purpose:
	 * 	Sends an email using the mail() function with the given subject and body text
	 *	to the specified email address. This method also has optional parameters that
	 *	allow for specifying content type and any BCC addresses. Multiple email addresses
	 *	separated by commas can be provided to the $emailAddress and $bcc parameters. If
	 *	no email address is specified for the $emailAddress parameter, the email will be
	 *	sent to the address specified by the Utils::DEFAULT_EMAIL class constant.
	 * Arguments:
	 *	- string $subject: Subject of the email to be sent
	 *	- string $message: Body content of the email to be sent
	 * 	- (optional) string $emailAddress: Email address(es) to which emails will be sent
	 * 	- (optional) string $contenttype: Mime type of content to be specified in the "Content-type" header information
	 * 	- (optional) string $bcc: BCC email address(es) to which emails will be sent
	 * Returns: void
	 */
	public static function sendEmail($subject, $emailText, $emailAddress = self::DEFAULT_EMAIL, $contenttype = self::DEFAULT_MIME, $bcc = "") {
		if($bcc != "") $bcc = "Bcc: $bcc" . PHP_EOL;
		if(!mail($emailAddress, $subject, $emailText,
				"From: Robert Carbajal <robert@holycow2.com>" . PHP_EOL .
				$bcc .
				"Content-type: $contenttype; charset=iso-8859-1" . PHP_EOL,
				"-frobert@iso-form.com")) {
			return false;
		} //end if

		return true;
	} //end static method sendEmail

	/**
	 * Name: transcribe_cp1252_to_latin1
	 * Visibility: public static
	 * Purpose:
	 * 	Converts text from cp1252 charset to latin1 for compatibility with the database's
	 *	default charset. This is most commonly used to handle strings that come from
	 * 	Microsoft products (e.g. Word, Excel, etc.). NOTE: This method was pulled from
	 *	a comment made by troelskn at gmail dot com at http://php.net/manual/en/function.strtr.php.
	 * Arguments:
	 *	- string $cp1252: string encoded in cp1252 to be converted
	 * Returns: void
	 */
	private static function transcribe_cp1252_to_latin1($cp1252) {
		return strtr(
			$cp1252,
			array(
				"\x80" => "e",  "\x81" => " ",    "\x82" => "'", "\x83" => 'f',
				"\x84" => '"',  "\x85" => "...",  "\x86" => "+", "\x87" => "#",
				"\x88" => "^",  "\x89" => "0/00", "\x8A" => "S", "\x8B" => "<",
				"\x8C" => "OE", "\x8D" => " ",    "\x8E" => "Z", "\x8F" => " ",
				"\x90" => " ",  "\x91" => "`",    "\x92" => "'", "\x93" => '"',
				"\x94" => '"',  "\x95" => "*",    "\x96" => "-", "\x97" => "--",
				"\x98" => "~",  "\x99" => "(TM)", "\x9A" => "s", "\x9B" => ">",
				"\x9C" => "oe", "\x9D" => " ",    "\x9E" => "z", "\x9F" => "Y"
			)
		);
	} //end static method transcribe_cp1252_to_latin1

	public static function authenticateRequest($appID, $signature) {
		if(DEBUG)
			return true;

		if($appID == "" || strlen($signature) != 64)
			return false;

		$hash = hash_hmac("sha256", $appID . FLUENTSEE_API_SECRET, FLUENTSEE_API_SECRET);
		if($appID != FLUENTSEE_API_ID || $hash != $signature)
			return false;

		return true;
	} //end method authenticateRequest

	public static function isSecureConnection() {
		return (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off');
	} //end method isSecureConnection
} //end class Utils
?>