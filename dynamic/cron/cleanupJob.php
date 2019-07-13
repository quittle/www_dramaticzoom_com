<?php
	/*/
	 *	Cron Job run every day at midnight
	 *	php /home/quittle/dz/php/cleanupJob.php
	/*/
	
	function lastIndexOf($haystack, $needle){
		$len = strlen($needle);
		$start = $len * -1;
		for($start = strlen($haystack)-$len;$start>=0;$start--){
			if(strcmp($needle, substr($haystack, $start, $len)) == 0)
				return $start;
		}
		return -1;
	}
	
	$curDir = substr(__FILE__, 0, lastIndexOf(__FILE__, "/"));
	
	require_once $curDir. '/../php/db.php';
	
	$curTime = time();
	
	$dbCon = new DatabaseConnection();
	
	//Get all the users who have their status set to 2, unconfirmed
	$users = $dbCon->getUsersByStatus(2);
	
	for($i=0;$i<sizeof($users);$i++){
		$user = $users[$i];
		$date = intval($user["dateCreated"]);
		if(($curTime - $date) > 345600){ //4 days difference
			$dbCon->delUserByUsername($user["username"]);
			
			//Delete the userZooms file
			$fileLoc = $curDir . "/../" . $user["userZooms"];
			unlink($fileLoc);
		}
	}
?>