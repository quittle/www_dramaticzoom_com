<?php

	/*/
	 * Handles requests for modifying emails
	/*/
	
	require_once "security.php";
	if(!validate()){
		echo -99;
		exit;
	}
		
	$method = $_POST['method'];
	$email = $_POST['email'];
	
	if($method === "reset-known"){
		require_once "cookie.php";
		$user = getUserByCookie();
		if($user)
			$user = $user['username'];
		if($user !== "" && $user !== null && $email !== ""){
			require_once 'db.php';
			require_once 'password.php';
			
			$dbCon = new DatabaseConnection();
			if($dbCon->setEmailByUsername($user, $email))
				echo 0;
			else
				echo -2;
		}else{
			echo -1;
		}
	}
?>