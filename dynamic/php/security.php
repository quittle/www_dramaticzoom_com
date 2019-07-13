<?php //This file is intended as a module for security related functions

	function getDecentRandomHash(){
		$dat = getrusage();
		$dat = $dat["ru_nswap"] . $dat["ru_majflt"] . $dat["ru_utime.tv_sec"] . $dat["ru_utime.tv_usec"];
		return hash("sha256", time() . "howdy partner!" . rand() . "gross negligence" . $user . rand() . "hilbert" . $dat . rand() . microtime() . "I'm feelin' pretty old, almost 256% of my age_3 years a-go!");
	}
	
	// Validate for clean inputs, suggested output for return false is -99
	// Note that this function only checks the login cookie
	function validate(){
		$cleanRegex = '/^[~\(\)\+\$=\|\?\!_@\.\-0-9a-zA-Z\s]*$/';
		//Ensure clean $_GET values
		foreach($_GET as $val){
			if(!preg_match($cleanRegex, $val)){
				return false;
			}
		}
		//Ensure clean $_POST values
		foreach($_POST as $val){
			if(!preg_match($cleanRegex, $val)){
				return false;
			}
		}
		//Ensure clean login cookie
		if(!preg_match($cleanRegex, $_COOKIE['login'])){
			return false;
		}
		return true;
	}
?>