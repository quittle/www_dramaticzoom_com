<?php
	require_once 'db.php';

	function getUserByCookie($cookie){
		if($cookie == null)
			$cookie = $_GET["cookie"]?$_GET["cookie"]:$_COOKIE['login'];

		$dbCon = new DatabaseConnection();
		$ret = $dbCon->getUserByCookie($cookie);
		if(!$ret)
			return null;
		else if(intval($ret['cookieExpiration']) < time())
			return null;
		else
			return $ret;
	}
?>