<?php
	require_once 'db.php';
	require_once 'security.php';
	require_once 'cookie.php';
	
	if(!validate()){
		echo -99;
		exit;
	}
	
	$dbCon = new DatabaseConnection();
	
	//Get variables
	$cookie = $_GET['cookie']?$_GET['cookie']:$_COOKIE['login'];
	$id = $_GET['id'];
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$method = $_GET['method'];
	if($method === "getZooms"){
		$ret = getUserByCookie($cookie);
		if($ret && intval($ret['cookieExpiration']) >= time()){
			$userZooms = "../" . $ret['userZooms'];
			$zooms = explode("\n", file_get_contents($userZooms));
			for($i=0;$i<count($zooms);$i++){
				$zoom = $zooms[$i];
				if(!empty($zoom)){
					$ret = $dbCon->getZoomById($zoom);
					if($ret){
						echo $zoom . "," . $ret['ogLoc'] . "," . $ret['viewCount'] . "\n";
					}
				}
			}
		}else{
			echo -1;
		}
	}else if($method === "delZoom"){
		$userRet = getUserByCookie($cookie);
		$zoomRet = $dbCon->getZoomById($id);
		if(!$userRet || !$zoomRet || ($zoomRet['creator'] != $userRet['username'])){
			echo -1;
		}else{ //This user owns the Zoomography
			$userZoomLoc = "../" . $userRet['userZooms'];
			$userZoom = file($userZoomLoc, FILE_SKIP_EMPTY_LINES); //Get all the user zooms
			$skip = array_search($id . "\n", $userZoom); //get the index to skip
			if($skip !== false){ //$skip could either be a number or FALSE, never actually TRUE
				//Only unlink them if we think we can successfully delete everything
				unlink("../" . $zoomRet['ogLoc']);
				unlink("../" . $zoomRet['imgLoc']);
				
				$output = "";
				for($i=0;$i<count($userZoom);$i++)
					if($i != $skip)  //remove from userzooms
						$output .= $userZoom[$i];
				$f = fopen($userZoomLoc, 'w'); //This way keeps the file open for as short a time as possible
				fwrite($f, $output);
				fclose($f);
				
				$dbCon->delZoomById($id);
				
				echo 1;
			}else
				echo -2;
		}
	}else if($method === "claim"){
		$ret = $dbCon->getZoomByIdIp($id, $ip);
		if($ret && $ret['creator'] === "" && $ret['ipAddress'] === $ip && intval($ret['dateCreated']) + 2*24*60*60 > time()){ // Within 2 days
			$ret = getUserByCookie($cookie);
			if($ret){
				$dbCon->setZoomCreator($id, $ret['username']);
				$f = fopen("../" . $ret['userZooms'], 'a');
				fwrite($f, $id . "\n");
				fclose($f);
				echo 1;
			}else{
				echo -2; //Not signed in
			}
		}else{
			echo -1; // Zoom is un-claimable for some reason
		}
	}
?>