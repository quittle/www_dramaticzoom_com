<?php
	require_once 'php/security.php';

	try{
		//Ensure clean $_POST values, (custom? for upload)
		foreach($_POST as $key => $val)
			if($key != 'imageFile-words' && !preg_match('/^[?\/:!%#_@.\-0-9a-zA-Z]*$/', $val))
				throw new Exception("INVALID PARAMETERS ($val)");

		$ext = null;
		$tempFileName = null;
		
		if($_POST['imageFile-words'] != ""){ //Image from URL
			//Create header for request
			header("Content-type: " . exif_imagetype($_POST['imageFile-words']));
			//Create context from options
			$opts = array(
				'http'=>array(
					'method'=>"GET",
					'header'=>"Accept-language: en\r\n" .
							"Accept-Language: en-us\r\n" .
							"User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)\r\n" .
							"Connection: Keep-Alive\r\n" .
							"Cache-Control: no-cache\r\n"
				)
			);
			$context = stream_context_create($opts);
			//Temp File should be unique
			$tempFileName = "./reqTemp/tempImg/" . getDecentRandomHash();
			
			//Make the request to the server and write the output to the temp file
			$ret = file_put_contents($tempFileName, file_get_contents($_POST['imageFile-words'], false, $context));
			if(!$ret){
				throw new Exception("Invalid image URL");
			}
			
			//Detect the type by actual type, not name. Support extension-less images
			if(@imagecreatefromjpeg($tempFileName))
				$ext = ".jpg";
			elseif(@imagecreatefromgif($tempFileName))
				$ext = ".gif";
			elseif(@imagecreatefrompng($tempFileName))
				$ext = ".png";
			elseif(@imagecreatefrombmp($tempFileName))
				$ext = ".bmp";
			else
				throw new Exception('Invalid file provided');
		}else{ //Upload image
			$type = $_FILES['imageFile']['type'];
			//Validate image type
			if(strcmp("image/",substr($type, 0, 6)) == 0){ //Make sure it starts with the string 'image/'
				$type = exif_imagetype($_FILES['imageFile']['tmp_name']);
				if($type == IMAGETYPE_JPEG){
					@imagecreatefromjpeg($_FILES['imageFile']['tmp_name']);
					$ext = ".jpg";
				}elseif($type == IMAGETYPE_GIF){
					@imagecreatefromgif($_FILES['imageFile']['tmp_name']);
					$ext = ".gif";
				}elseif($type == IMAGETYPE_PNG){
					@imagecreatefrompng($_FILES['imageFile']['tmp_name']);
					$ext = ".png";
				}elseif($type == IMAGETYPE_BMP){
					@imagecreatefrombmp($_FILES['imageFile']['tmp_name']);
					$ext = ".bmp";
				}else
					throw new Exception('Invalid file provided');
				$tempFileName = $_FILES['imageFile']['tmp_name'];
			}else throw new Exception('Invalid file provided');
		}
		//Image type validated
		
		//Add requires
		require_once 'php/db.php';
		require_once 'php/cookie.php';
		
		//Create the DatabaseConnection
		$dbCon = new DatabaseConnection();
		
		//Check the filesize
		if(filesize($tempFileName) > 1048576){ // more than 1 MB
			throw new Exception("Image provided too large. Please resize it below one megabyte and try again.");
		}

		//Get nice 8 character md5 name
		$val = $dbCon->getAndIncrementIndex();
		
		/*
		$indexLog = fopen("./log.txt", 'r+');
		$val = -1;
		if(flock($indexLog, LOCK_EX)){  // acquire an exclusive lock
			$val = fread($indexLog, filesize("./log.txt"));
			rewind($indexLog);
			fwrite($indexLog, intval($val)+1);
			fflush($fp);            // flush output before releasing the lock
			flock($indexLog, LOCK_UN);    // release the lock
		} else {
			throw new Exception("Unable to lock file");
		}
		fclose($indexLog);
		*/
		
		$id = createId($val);
		
		$fName = $id . $ext;

		//Move the image to the images folder
		$file_path = "images/" . $fName;
		//Check if the tempFileName is "uploaded" (meaning it's in the temporary php upload folder) and either move it the was php suggests or rename (equivalent to just move) if downloaded from URL
		$success = is_uploaded_file($tempFileName) ? move_uploaded_file($tempFileName, $file_path) : rename($tempFileName, $file_path);

		if(!$success)
			throw new Exception("Image not uploaded");

		//Store in database
		$c = $_POST['color'];
		$fx = $_POST['fx'];
		$fy = $_POST['fy'];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		//Create Open Graph square image [depends on the 'convert' and 'identify' ImageMagick (ImageMagick 6.8.3-6) program and potential OS dependency based on exec'ing commands]
		$ogLoc = "og/$fName";
		
		//Just double check the user input color #FFF rgba(255,255,255,1)
		if(!preg_match('/^[\. \(\),#0-9a-frgA-FRG]*$/', $val))
			throw new Exception("Invalid color parameter");
		
		/* Complicated because c program doesn't work */
		$command = 'convert "./' . $file_path . '" -resize 1500x1500\> -resize 200x200\< -background "' . $c . '" -gravity center "./' . $ogLoc . '";\
				identify -format "%[fx:min(w,h)]" "./' . $ogLoc . '"';
	
		exec($command, $out1);
		$minSize = $out1[0];
		
		$command = 'convert "./' . $ogLoc . '" -gravity center -extent ' . $minSize . 'x' . $minSize . ' "./' . $ogLoc . '"';
		exec($command, $out2);
		/* End of resizing */

		$ret = getUserByCookie($_COOKIE['login']);
		$user = null;
		if($ret)
			$user = $ret['username'];
		
		$dbCon->putZoom($id, $file_path, $ogLoc, $c, $fx, $fy, 'audio/chipmonk', $ip, $user);

		if($user){
			$f = fopen($ret['userZooms'], 'a');
			fwrite($f, $id . "\n");
			fclose($f);
		}
		
		if($success){
			if($_POST['ajax'])
				echo $id;
			else{
				header('Location: /' . $id);
			}
		}else{
			echo "<h1>There was an error when you just did that thing</h1><br />
				<h2>That's all I can really tell you, sorry.</h2><br />
				<h3>Well, that and go <a href='/'>home</a> and try again.";
		}
	} catch(Exception $e) { //Bad file type
		if($_POST['ajax'])
			echo '-1';
		else
			echo '<h1>Whoops, bad file. <a href="/">Go back</a> and try a different file.</h1><br /><br />
				<h2>Redirecting in 5 seconds...</h2>
				<br />
				<h3>Error provided: ' . $e->getMessage() . '</h3>
				<script type="text/javascript">
					setTimeout(function(){window.location = "/";}, 5000);
				</script>';
	}
	
	
	//Supports up to 14,740,599 inclusively before duplicates. Should be fine, right?
	function createId($val){
		$val = crc32($val);
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_+-()=';
		$alphabet = str_split($alphabet, 1);
		
		$l = count($alphabet);
		
		$shortenedId = '';
		while($val > 0){
			$d = $val % $l;
			$val = ($val - $d) / $l;
			$shortenedId .= $alphabet[$d];
		}
		return $shortenedId;
	}
	
	//Explanation
	//md5s are long, we can shrink them down by converting the 16 characters 0-9a-f into 64 characters
	//This loop takes 4 characters at a time and makes them one unique character
	//then concatenates these 1/4 characters to turn a 32 char string into an 8 char one
	function md5ToShorter($md5){
		$chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', '(', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', '\'', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', '~', 'H', 'I', 'J', '.', 'L', ')', 'N', 'O', 'P', '+', 'R', 'S', 'T', 'U', 'V', 'Q', 'X', 'Y', 'Z', '-', 'K', '_', 'G', 'm', 'M', 'W', 'w', '!');
		$ret = "";
		
		for($i=0;$i<strlen($md5);$i+=4){
			$char1 = $md5[$i];
			$ascii1 = ord($char1);
			if($ascii1 >= 48 && $ascii1 <= 57)
				$ascii1 -= 48;
			else if($ascii1 >= 97 && $ascii1 <= 102)
				$ascii1 -= 87;
				
			$char2 = $md5[$i+1];
			$ascii2 = ord($char2);
			if($ascii2 >= 48 && $ascii2 <= 57)
				$ascii2 -= 48;
			else if($ascii2 >= 97 && $ascii2 <= 102)
				$ascii2 -= 87;
				
			$char3 = $md5[$i+2];
			$ascii3 = ord($char3);
			if($ascii3 >= 48 && $ascii3 <= 57)
				$ascii3 -= 48;
			else if($ascii3 >= 97 && $ascii3 <= 102)
				$ascii3 -= 87;
				
			$char4 = $md5[$i+3];
			$ascii4 = ord($char4);
			if($ascii4 >= 48 && $ascii4 <= 57)
				$ascii4 -= 48;
			else if($ascii4 >= 97 && $ascii4 <= 102)
				$ascii4 -= 87;

			$ret = $ret . $a[$ascii1+$ascii2+$ascii3+$ascii4];
		}
		return $ret;
	}
	/*
	function lastIndexOf($haystack, $needle){
		$len = strlen($needle);
		$start = $len * -1;
		for($start = strlen($haystack)-$len;$start>=0;$start--){
			if(strcmp($needle, substr($haystack, $start, $len)) == 0)
				return $start;
		}
		return -1;
	}*/
?>