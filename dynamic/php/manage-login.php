<?php
	require_once 'db.php';
	require_once 'security.php';
	require_once 'password.php';
	require_once 'cookie.php';
	
	if(!validate()){
		echo -99;
		exit;
	}
	
	$dbCon = new DatabaseConnection();
	
	$emailPattern = "/^[?!_\.\-0-9a-zA-Z]+@[-0-9a-zA-Z]+(\.[0-9a-zA-Z]+)+$/";

	//Grab input data and store it in variables appropriately
	$method = $_GET["method"];
	$ip = $_SERVER['REMOTE_ADDR'];
	$cookie = $_GET["cookie"]?$_GET["cookie"]:$_COOKIE['login'];
	$id = $_GET["id"];
	$user = strtolower($_GET["user"]);
	$pass = $_GET["pass"];
	$email = $_GET["email"];
	
	if($method === "login"){ // -1=empty inputs, -2=invalid username/password, otherwise=success
		if(empty($user) || empty($pass)){
			echo -1;
		}else if(verifyPassword($user, $pass)){
			$cookie = getDecentRandomHash();
			$cookieExpiration = time() + 2*60*60; //Two hours
			
			//$dbCon->setCookieByUsername($user, $cookie, $cookieExpiration);
			$dbCon->doLoginByUsername($user, $cookie, $cookieExpiration);
			echo $cookie;
		}else{
			echo -2;
		}
	}else if($method === "register"){ // 1=success, -1=empty inputs, -2=taken username, -3=taken email, -4=invalid email
		//Check for empties
		if(empty($user) || empty($pass) || empty($email)){
			echo -1;
			exit;
		}
		//Check for valid email
		else if(!preg_match($emailPattern, $email)){
			echo -4;
			exit;
		}
		
		//Check for untaken username
		$ret = $dbCon->getUserByUsername($user);
		if($ret){
			echo -2;
			exit;
		}
		//Check for untaken email
		$ret = $dbCon->getUserByEmail($email);
		if($ret){
			echo -3;
			exit;
		}
		
		//Everything is good so create account
		$secrets = createSecrets($pass);
		$pass = $secrets['password'];
		$salt = $secrets['salt'];
		$special = getDecentRandomHash();
		
		$userZooms = "userZooms/$user";
		$status = DatabaseConnection::ACCOUNT_STATUS_UNREGISTERED; //Uncomfirmed email
		
		$dbCon = new DatabaseConnection();
		$dbCon->putUser($user, $email, $pass, $userZooms, $salt, $special, $status);

		//Create an empty file for userZooms
		fclose(fopen($userZooms, 'w'));
		
		//Send registration email
		require_once 'email-register.php';
		sendRegistration($user, $email, $special);
		
		echo 1;
	}else if($method === "check"){
		//Check for empties
		if(empty($user) && empty($email)){
			echo -1;
			exit;
		}
		//Check for valid email address
		if(!empty($email) && !preg_match($emailPattern, $email)){
			echo -4;
			exit;
		}
		
		if(!empty($user)){
			$ret = $dbCon->getUserByUsername($user);
			if($ret){
				echo -2;
				exit;
			}
		}
		
		if(!empty($email)){
			$ret = $dbCon->getUserByEmail($email);
			if($ret){
				echo -3;
				exit;
			}
		}
		
		echo 1;
	}else if($method === "getUser"){
		$ret = getUserByCookie($cookie);
		if($ret)
			echo $ret['username'];
		else
			echo -1;
	}else if($method === "getEmail"){
		$ret = getUserByCookie($cookie);
		if($ret)
			echo $ret['email'];
		else
			echo -1;
	}else if($method === "test"){

	}
	
	//Supports up to 14,740,599 inclusively before duplicates. Should be fine, right?
	function shorten($dec){
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_.~+-()$=|';
		$alphabet = str_split($alphabet, 1);
		
		$l = count($alphabet);
		
		$shortenedId = '';
		while($dec > 0){
			$d = $dec % $l;
			$dec = ($dec - $d) / $l;
			$shortenedId .= $alphabet[$d];
		}
		return $shortenedId;
	}
	
	flush();
	ob_flush();
?>