<?php //THIS FILE SHOULD BE RETIRED AS SOON AS POSSIBLE
	function validate(){
		//Ensure clean $_GET values
		foreach($_GET as $val){
			if(!preg_match('/^[?!_@.\-0-9a-zA-Z]*$/', $val)){
				echo '-99'; // INVALID PARAMETERS
				return false;
			}
		}
		//Ensure clean $_POST values
		foreach($_POST as $val){
			if(!preg_match('/^[?!_@.\-0-9a-zA-Z]*$/', $val)){
				echo '-99'; // INVALID PARAMETERS
				return false;
			}
		}
		if(!preg_match('/^[?!_@.\-0-9a-zA-Z]*$/', $_COOKIE['login'])){
			echo '-99'; // INVALID PARAMETERS
			return false;
		}
		return true;
	}
?>