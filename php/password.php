<?php
	// $pass should be an SHA-256 hashing of the raw text, not the raw text itself
	function createSecrets($pass){
		require_once "security.php";
		
		$salt = getDecentRandomHash();
		$saltedPass = hash("sha256", $pass . $salt);
		return array(
			"password" => $saltedPass,
			"salt" => $salt);
	}
	
	function verifyPassword($user, $pass){
		require_once 'db.php';
		
		$dbCon = new DatabaseConnection();
		$ret = $dbCon->getUserSecretsByUsername($user);
		return $ret['password'] === hash("sha256", $pass . $ret['salt']);
	}
?>