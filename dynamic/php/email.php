<?php
	require_once "Mail.php";
	require_once "Mail/mime.php";
	 
	function sendEmail($to, $subject, $body, $isHTML){
		$host = "mail.dramaticzoom.com";
		$username = "quittle";
		$password = "kn5Chtt7";

		$headers = array(	'From' => 'no-reply@dramaticzoom.com',
							'To' => $to,
							'Subject' => $subject
						);


		$mime = new Mail_mime(array("eol" => "\r\n"));
		if($isHTML)
			$mime->setHTMLBody($body);
		else
			$mime->setTxtBody($body);
		$body = $mime->get();
		$headers = $mime->headers($headers);


		$smtp =& Mail::factory('smtp',
			array(	'host' => $host,
					'auth' => true,
					'username' => $username,
					'password' => $password
			));

		$smtp->send($to, $headers, $body);
	}
?>