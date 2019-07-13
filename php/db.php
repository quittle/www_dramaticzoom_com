<?php
	/*/
	 * This file is for DatabaseConnecion, which should ideally manage all I/O
	 * for the database using prepared statements.
	 *
	 * Sample Call:
	 * $dbCon = new DatabaseConnection();
	 * $ret = $dbCon->getUserByUsername($user);
	 * echo "The email address for $user is '" . $ret['email'] . "'.";
	/*/

	/*/
	 *	Database status
	 *		ACCOUNT_STATUS_NORMAL		- 0	- Normal
	 *		ACCOUNT_STATUS_FORGOT		- 1	- Forgot Password
	 *		ACCOUNT_STATUS_UNREGISTERED	- 2	- Unregistered
	/*/
	require_once('KLogger.php'); // Change to FLogger to do no work and potentially speed up the site

	class DatabaseConnection{
		const ACCOUNT_STATUS_NORMAL = 0, ACCOUNT_STATUS_FORGOT = 1, ACCOUNT_STATUS_UNREGISTERED = 2;

		private $mysqli;
		private $logger;
		function __construct(){
			$this->mysqli = new mysqli("localhost", "dramaticzoom", "Os)%GZ15P`GJ9>l#}1-0!v)#1rK1#O`nw", "quittle_dramaticzoom");
			$this->logger = new KLogger('logs/db', KLogger::INFO);
		}

		function putUser($user, $email, $password, $userZooms, $salt, $special, $status){
			$this->logger->logInfo("putUser: $user : $email : $password : $userZooms : $salt : $special : $status");
			$user = strtolower($user);
			$email = strtolower($email);

			$statement = $this->mysqli->prepare("INSERT INTO users (username, email, password, userZooms, salt, dateCreated, special, status, lastLogin, totalLogins) VALUES ((?), (?), (?), (?), (?), (?), (?), (?), 0, 0)");
			$statement->bind_param("sssssdss", $user, $email, $password, $userZooms, $salt, time(), $special, $status);
			$this->execNoReturn($statement);
		}

		function getUserByUsername($user){
			$this->logger->logInfo("getUserByUsername: $user");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("SELECT username,email,password,cookie,userZooms,cookieExpiration,dateCreated,special,status,lastLogin,totalLogins FROM users WHERE username=(?) ");
			$statement->bind_param("s", $user);
			$statement->execute();
			$statement->bind_result($username, $email, $password, $cookie, $userZooms, $cookieExpiration, $dateCreated, $special, $status, $lastLogin, $totalLogins);
			$statement->fetch();
				$ret = array(
						"username" => $username,
						"email" => $email,
						"password" => $password,
						"cookie" => $cookie,
						"userZooms" => $userZooms,
						"cookieExpiration" => $cookieExpiration,
						"dateCreated" => $dateCreated,
						"special" => $special,
						"status" => $status,
						"lastLogin" => $lastLogin,
						"totalLogins" => $totalLogins);
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}
		function getUserSecretsByUsername($user){
			$this->logger->logInfo("getUserSecretsByUsername: $user");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("SELECT password,salt FROM users WHERE username=(?) ");
			$statement->bind_param("s", $user);
			$statement->execute();
			$statement->bind_result($password, $salt);
			$statement->fetch();
				$ret = array(
						"password" => $password,
						"salt" => $salt );
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}
		function getUserByEmail($email){
			$this->logger->logInfo("getUserByEmail: $email");
			$email = strtolower($email);

			$statement = $this->mysqli->prepare("SELECT username,email,password,cookie,userZooms,cookieExpiration,dateCreated,special,status FROM users WHERE email=(?) ");
			$statement->bind_param("s", $email);
			$statement->execute();
			$statement->bind_result($username, $emailOutput, $password, $cookie, $userZooms, $cookieExpiration, $dateCreated, $special, $status);
			$statement->fetch();
				$ret = array(
						"username" => $username,
						"email" => $emailOutput,
						"password" => $password,
						"cookie" => $cookie,
						"userZooms" => $userZooms,
						"cookieExpiration" => $cookieExpiration,
						"dateCreated" => $dateCreated,
						"special" => $special,
						"status" => $status );
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}
		function getUserByCookie($cookie){
			$this->logger->logInfo("getUserByCookie: $cookie");

			$statement = $this->mysqli->prepare("SELECT username,email,password,cookie,userZooms,cookieExpiration,dateCreated,special,status FROM users WHERE cookie=(?) ");
			$statement->bind_param("s", $cookie);
			$statement->execute();
			$statement->bind_result($username, $emailOutput, $password, $cookie, $userZooms, $cookieExpiration, $dateCreated, $special, $status);
			$statement->fetch();
				$ret = array(
						"username" => $username,
						"email" => $emailOutput,
						"password" => $password,
						"cookie" => $cookie,
						"userZooms" => $userZooms,
						"cookieExpiration" => $cookieExpiration,
						"dateCreated" => $dateCreated,
						"special" => $special,
						"status" => $status );
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}

		//Clear special and set status to 0 because obviously the status should reflect the change in special
		function clearSpecialByUsername($user){
			$this->logger->logInfo("clearSpecialByUsername: $user");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET special='', status=0 WHERE username=(?) ");
			$statement->bind_param("s", $user);
			$this->execNoReturn($statement);
		}

		function getUsersByStatus($status){
			$this->logger->logInfo("getUsersByStatus: $status");
			$ret = array();
			$statement = $this->mysqli->prepare("SELECT username,email,password,cookie,userZooms,cookieExpiration,dateCreated,special,status FROM users WHERE status=(?) ");
			$statement->bind_param("i", $status);
			$statement->execute();
			$statement->bind_result($username, $email, $password, $cookie, $userZooms, $cookieExpiration, $dateCreated, $special, $statusOutput);
			while($statement->fetch()){
				array_push($ret, array(
									"username" => $username,
									"email" => $email,
									"password" => $password,
									"cookie" => $cookie,
									"userZooms" => $userZooms,
									"cookieExpiration" => $cookieExpiration,
									"dateCreated" => $dateCreated,
									"special" => $special,
									"status" => $statusOutput ));
			}
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}

		function delUserByUsername($user){
			$this->logger->logInfo("delUserByUsername: $user");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("DELETE FROM users WHERE username=(?)");
			$statement->bind_param("s", $user);
			$this->execNoReturn($statement);
		}

		function setStatusByUsername($user, $status){
			$this->logger->logInfo("setStatusByUsername: $user : $status");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET status=(?) WHERE username=(?) ");
			$statement->bind_param("is", $status, $user);
			$this->execNoReturn($statement);
		}
		function setSpecialByUsername($user, $special, $status){ //Include status because updating special should always be attached to a new status
			$this->logger->logInfo("setSpecialByUsername: $user : $special : $status");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET special=(?), status=(?) WHERE username=(?) ");
			$statement->bind_param("sis", $special, $status, $user);
			$this->execNoReturn($statement);
		}
		function resetPassword($user, $curPassword, $newPassword, $newSalt){
			$this->logger->logInfo("resetPassword: $user : $curPassword : $newPassword : $newSalt");
			$user = strtolower($user);
			require_once 'password.php';

			if(verifyPassword($user, $curPassword)){
				$statement = $this->mysqli->prepare("UPDATE users SET password=(?), salt=(?) WHERE username=(?)");
				$statement->bind_param("sss", $newPassword, $newSalt, $user);
				//$ret is true only if the execute went successfully and it affected some rows (hopefully just one)
				$ret = $statement->execute() && ($statement->affected_rows > 0);
				$statement->close();

				return $ret;
			}else{
				return false;
			}
		}
		function setPasswordByUsername($user, $password, $salt, $special){
			$this->logger->logInfo("setPasswordByUsername: $user : $password : $salt : $special");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET special='', status=(?), password=(?), salt=(?) WHERE username=(?) AND status=1 AND special=(?) ");
			$statement->bind_param("dssss", DatabaseConnection::ACCOUNT_STATUS_NORMAL, $password, $salt, $user, $special);
			//$ret is true only if the execute went successfully and it affected some rows (hopefully just one)
			$ret = $statement->execute() && ($statement->affected_rows > 0);
			$statement->close();

			return $ret;
		}
		function doLoginByUsername($user, $cookie, $expiration){
			$this->logger->logInfo("doLoginByUsername: $user : $cookie : $expiration");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET cookie=(?), cookieExpiration=(?), lastLogin=(?), totalLogins=totalLogins+1 WHERE username=(?) ");
			$statement->bind_param("ssds", $cookie, $expiration, time(), $user);
			$this->execNoReturn($statement);
		}
		function setCookieByUsername($user, $cookie, $expiration){
			$this->logger->logInfo("setCookieByUsername: $user : $cookie : $expiration");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET cookie=(?), cookieExpiration=(?) WHERE username=(?) ");
			$statement->bind_param("sss", $cookie, $expiration, $user);
			$this->execNoReturn($statement);
		}
		function setEmailByUsername($user, $email){
			$this->logger->logInfo("setEmailByUsername: $user : $email");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE users SET email=(?) WHERE username=(?) ");
			$statement->bind_param("ss", $email, $user);

			return $this->execSuccess($statement);
		}

		/* This is the index used to generate the links for zoomographs */
		function getAndIncrementIndex(){
			$this->logger->logInfo("getAndIncrementIndex");

			$ret = -1;

			$lock = "'dz.indexLock'";
			$lockTimeout = 10; //in seconds
			$this->mysqli->query("SELECT GET_LOCK($lock, $lockTimeout)");

			$ret = $this->mysqli->query("SELECT counter FROM special WHERE type = 0");
			$this->mysqli->query("UPDATE special SET counter = counter + 1 WHERE type = 0");

			$this->mysqli->query("SELECT RELEASE_LOCK($lock)");

			if($ret != null && $ret != -1){
				$row = $ret->fetch_row();
				if($row == null){
					$ret = -1;
				}else{
					$ret = intval($row[0]);
				}
			}

			return $ret;
		}

		/* ZOOMOGRAPHY SECTION */
		function putZoom($id, $imgLoc, $ogLoc, $c, $fx, $fy, $audioLoc, $ip, $user){
			$this->logger->logInfo("putZoom: $id : $imgLoc : $ogLoc : $c : $fx : $fy : $audioLoc : $ip : $user");
			if($user == null)
				$user = "";

			$user = strtolower($user);

			$statement = $this->mysqli->prepare("INSERT INTO imageArtworks (id, imgLoc, ogLoc, background, focusX, focusY, soundLoc, viewCount, dateCreated, ipAddress, creator) VALUES
					((?), (?), (?), (?), (?), (?), (?), 0, '" . time() . "', (?), (?))");
			$statement->bind_param("ssssddsss", $id, $imgLoc, $ogLoc, $c, $fx, $fy, $audioLoc, $ip, $user);
			$this->execNoReturn($statement);
		}

		function getZoomById($inputId){
			$this->logger->logInfo("getZoomById: $inputId");
			$statement = $this->mysqli->prepare("SELECT id,imgLoc,ogLoc,background,focusX,focusY,soundLoc,viewCount,dateCreated,ipAddress,creator FROM imageArtworks WHERE id=(?) ");
			$statement->bind_param("s", $inputId);
			$statement->execute();
			$statement->bind_result($id, $imgLoc, $ogLoc, $background, $focusX, $focusY, $soundLoc, $viewCount, $dateCreated, $ipAddress, $creator);
			$statement->fetch();
				$ret = array(
						"id" => $id,
						"imgLoc" => $imgLoc,
						"ogLoc" => $ogLoc,
						"background" => $background,
						"focusX" => $focusX,
						"focusY" => $focusY,
						"soundLoc" => $soundLoc,
						"viewCount" => $viewCount,
						"dateCreated" => $dateCreated,
						"ipAddress" => $ipAddress,
						"creator" => $creator );
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}
		function getZoomByIdIp($inputId, $inputIp){
			$this->logger->logInfo("getZoomByIdIp: $inputId : $inputIp");
			$statement = $this->mysqli->prepare("SELECT id,imgLoc,ogLoc,background,focusX,focusY,soundLoc,viewCount,dateCreated,ipAddress,creator FROM imageArtworks WHERE id=(?) && ipAddress=(?) ");
			$statement->bind_param("ss", $inputId, $inputIp);
			$statement->execute();
			$statement->bind_result($id, $imgLoc, $ogLoc, $background, $focusX, $focusY, $soundLoc, $viewCount, $dateCreated, $ipAddress, $creator);
			$statement->fetch();
				$ret = array(
						"id" => $id,
						"imgLoc" => $imgLoc,
						"ogLoc" => $ogLoc,
						"background" => $background,
						"focusX" => $focusX,
						"focusY" => $focusY,
						"soundLoc" => $soundLoc,
						"viewCount" => $viewCount,
						"dateCreated" => $dateCreated,
						"ipAddress" => $ipAddress,
						"creator" => $creator );
			$statement->close();
			return isFilled($ret) ? $ret : false;
		}

		function setZoomCreator($id, $user){
			$this->logger->logInfo("setZoomCreator: $id : $user");
			$user = strtolower($user);

			$statement = $this->mysqli->prepare("UPDATE imageArtworks SET creator=(?) WHERE id=(?) ");
			$statement->bind_param("ss", $user, $id);
			$this->execNoReturn($statement);
		}

		function incrementZoomById($id){
			$this->logger->logInfo("incrementZoomById: $id");
			$this->logger->logInfo("increment zoom: $id");
			$statement = $this->mysqli->prepare("UPDATE imageArtworks SET viewCount = viewCount + 1 WHERE id=(?) ");
			$statement->bind_param("s", $id);
			$this->execNoReturn($statement);
		}

		function delZoomById($id){
			$this->logger->logInfo("delZoomById: $id");
			$statement = $this->mysqli->prepare("DELETE FROM imageArtworks WHERE id=(?)");
			$statement->bind_param("s", $id);
			$this->execNoReturn($statement);
		}

		private function execNoReturn($statement){
			$statement->execute();
			$statement->close();
		}

		private function execSuccess($statement){
			//$ret is true only if the execute went successfully and it affected some rows (hopefully just one)
			$ret = $statement->execute() && ($statement->affected_rows > 0);
			$statement->close();

			return $ret;
		}

		function __destruct(){
			$this->mysqli->close();
		}
	}

	//Helper function for consumers to check if good output
	//Untested for getUsersByStatus
	function isFilled($ret){
		foreach($ret as $val)
			if(!empty($val))
				return true;
	}
?>