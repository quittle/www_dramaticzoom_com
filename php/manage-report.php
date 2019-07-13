<?php
	/*/
	 * Handles reports from users
	/*/

	//Returns 0 on success, -1 for empty description or a bad email, -2 if the sending of the mail failed
	$type = $_POST['type'];
	$email = $_POST['email'];
	$desc = $_POST['desc'];
	if(!empty($desc) && (empty($email) || preg_match("/^[?!_\\.\\-0-9a-zA-Z]+@[-0-9a-zA-Z]+(\\.[0-9a-zA-Z]+)+$/", $email))){
		if(mail("webmaster@dustindoloff.com", "DRAMATIC ZOOM REPORT",
			"'$type' report made on " . date('Y/m/d h:m:s', time()) . "\n\n" . 
			"Response email address: " . (empty($email) ? "NONE" : $email) . "\n" .
			"Url: " . $_SERVER['HTTP_REFERER'] . "\n" . 
			"Description of $type:\n\n----\n\n" . $desc . "\n\n----\n\n" . 
			"END OF $type REPORT -- HAVE A NICE DAY")){
			echo "0";
		}else
			echo "-2";
	}else
		echo "-1";
?>