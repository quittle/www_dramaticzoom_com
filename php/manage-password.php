<?php

	/*/
	 * Handles requests for modifying passwords
	/*/
	
	require_once "security.php";
	if(!validate()){
		echo -99;
		exit;
	}
		
	$method = $_POST['method'];
	$user = $_POST['username'];
	$email = $_POST['email'];
	$pass = $_POST['password'];
	$newPass = $_POST['newPassword'];
	$special = $_POST['special'];
	
	if($method === "reset"){
		if($user !== "" && $pass !== "" && $specail !== ""){
			require_once 'db.php';
			require_once 'password.php';
			
			$secrets = createSecrets($pass);
			
			$dbCon = new DatabaseConnection();
			if($dbCon->setPasswordByUsername($user, $secrets['password'], $secrets['salt'], $special))
				echo 0;
			else
				echo -2;
		}else{
			echo -1;
		}
	}else if($method === "reset-known"){
		require_once 'cookie.php';
		$user = getUserByCookie();
		if($user)
			$user = $user['username'];
		if($user !== "" && $user !== null && $pass !== "" && $newPass !== ""){
			require_once 'db.php';
			require_once 'password.php';
			
			$secrets = createSecrets($newPass);
			
			$dbCon = new DatabaseConnection();
			if($dbCon->resetPassword($user, $pass, $secrets['password'], $secrets['salt']))
				echo 0;
			else
				echo -2;
		}else{
			echo -1;
		}
	}else if($method === "send-reset"){
		require_once 'db.php';
		
		$dbCon = new DatabaseConnection();
		$ret = $dbCon->getUserByEmail($email);
		
		if($ret['username']){
			if($ret['status'] == DatabaseConnection::ACCOUNT_STATUS_UNREGISTERED){
				echo -2;
			}else{
				require_once 'email-reset.php';
				require_once 'security.php';
				
				$special = getDecentRandomHash();
				$status = DatabaseConnection::ACCOUNT_STATUS_FORGOT;
				$dbCon->setSpecialByUsername($ret['username'], $special, status);
				sendForgotPassword($ret['username'], $ret['email'], $special);
				
				echo 0;
			}
		}else{
			echo -1;
		}
	}
?>