<?php
	include 'includes/header.php';
	$display = false;
	if($_SERVER['QUERY_STRING'] != ""){
		$display = true;
		
		require_once 'php/db.php';
		require_once 'php/cookie.php';
		$dbCon = new DatabaseConnection();
		
		$id = $_SERVER['QUERY_STRING'];
		
		$retUser = getUserByCookie($_COOKIE['login']);
		$loggedIn = !!$ret;
		
		$retZoom = $dbCon->getZoomById($id);
		if(!$retZoom){ //zoomography definitely wasn't found
			include('error/404.php');
			exit;
		}
		
		$imgLoc = $retZoom['imgLoc'];
		$ogLoc = $retZoom['ogLoc'];
		$color = $retZoom['background'];
		$fx = $retZoom['focusX'];
		$fy = $retZoom['focusY'];
		$soundLoc = $retZoom['soundLoc'];
		$dateCreated = $retZoom['dateCreated'];
		$ipAddress = $retZoom['ipAddress'];
		$viewCount = $retZoom['viewCount'];

		$claimed = !empty($retZoom['creator']);
		
		$dbCon->incrementZoomById($id);

		$shortURL = "http://drzm.co/$id";
		$longURL = "http://dz.dustindoloff.com/$id";
		
		//Share social link
		$facebookShareLink = "https://www.facebook.com/dialog/feed?app_id=620536684639636" .
			"&link=$shortURL" .
			"&picture=http://" . $_SERVER['SERVER_NAME'] . "/" . $ogLoc .
			"&display=popup" .
			"&name=Share with Dramatic Zoom" .
			"&redirect_uri=http://" . $_SERVER['SERVER_NAME'] . "/redirectClose.php";
		$twitterShareLink = "https://twitter.com/intent/tweet?text=Zooooooom!&url=$shortURL";
		$googleShareLink = "https://plus.google.com/share?url=$longURL";
		$redditShareLink = "http://www.reddit.com/submit?url=$shortURL";
		
		
		$facebookShareLink = "window.open('$facebookShareLink','popUpWindow','height=319,width=480,left=0,top=0,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');";
		$twitterShareLink = "window.open('$twitterShareLink'  ,'popUpWindow','height=219,width=480,left=0,top=0,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');";
		$googleShareLink = "window.open('$googleShareLink'    ,'popUpWindow','height=319,width=480,left=0,top=0,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');";
		$redditShareLink = "window.open('$redditShareLink'    ,'popUpWindow','height=820,width=860,left=0,top=0,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');";
		
		printHeadTop();

		setRealLoc('display');

		echo '
			<script type="text/javascript" src="/js/display.js"></script>
			<link rel="stylesheet" href="/css/display.css" type="text/css" />
			<!-- Required to avoid occasional image load on side of browser. CSS does not block the load -->
			<style type="text/css">
				#main-holder{
					position: absolute;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
				}
				#sound, #legacySoundHolder{
					position: absolute;
					top: -9999px;
					left: -9999px;
					z-index: 10000;
					position: absolute;
				}
				td, table{
					vertical-align: middle;
					height: 100%;
					width: 100%;
					text-align: center;
				}
				#main{
					text-align: center;
					max-width: 600px;
					max-height: 600px;
				}
			</style>
			
			<meta itemprop="image" content="http://' . $_SERVER["SERVER_NAME"] . "/" . $ogLoc . '">
			<meta property="og:title" content="Dramatic Zoom"/>
			<meta property="og:image" content="http://' . $_SERVER["SERVER_NAME"] . "/" . $ogLoc . '"/>
			<meta property="og:url" content="' . $longURL . '"/>
			<meta property="og:description" content="Dramatic Zoom allows you to share images that dramatically zoom in for emphasis."/>
		';
	}else{
		$imgLoc = "";
		$color = "#b0ccd9";
		$fx = ".5";
		$fy = ".5";
		
		printHeadTop();
		setRealLoc('noDisplay');
		echo '
			<script src="/js/noDisplay.js"></script>
			<link rel="stylesheet" href="/css/noDisplay.css" type="text/css" />';
	}
?>
	
	<style type="text/css">
	body{
		<?php
		echo "background: $color;";
		if($display) echo 'overflow: hidden;';
		?>
	}
	</style>
	<?php
		printHeadBottom();
	?>
<body>
	<?php
	if($display){ echo '
	<audio id="sound" controls="controls" preload="preload" height="100" width="100">
	  <source src="/audio/chipmonk.mp3" type="audio/mpeg">
	  <source src="/audio/chipmonk.ogg" type="audio/ogg">
	  <source src="/audio/chipmonk.mp4" type="audio/mp4">
	  <embed id="legacySound" height="50" width="100" autostart="false" src="/audio/chipmonk.mp3" type="audio/mpeg">
	</audio>
	<span id="legacySoundHolder"></span>
	<canvas id="main-canvas"></canvas>
	<div id="main-holder" style="text-align:center;">
		<table><tr><td>
			<img id="main" onload="zoom(' . $fx . ',' . $fy . ');" src="' . $imgLoc . '" />
		</td></tr></table>
	</div>

	<div id="overlay-holder">
		<div id="overlay" style="opacity:1;">
			<div id="overlay-center">';

				if(!$loggedIn && $ipAddress == $_SERVER['REMOTE_ADDR'] && intval($dateCreated) + 2*24*60*60 > time() && !$claimed){ // Within 2 days at the same location
					echo '
						<div id="overlay-prompt">
							<a onclick="unhideOverlay();showDrop(\'login\');">Log in</a> to keep track of your links.
						</div>
					';
				}
				
				echo '
				Share: <input id="overlay-text" type="text" readonly="readonly" onclick="select(this)" value="' . $shortURL . '" /><span id="copyText"> [CTRL + C] to copy to clipboard</span><br />
				<div class="social-buttons" id="share-social">
					Social:
					<a class="share-button" onclick="'. $facebookShareLink . '"><img src="/resources/images/facebook_logo.png" alt="facebook icon" />Share</a>
					<a class="share-button" onclick="'. $twitterShareLink .  '"><img src="/resources/images/twitter_logo.png" alt="twitter icon" />Tweet</a>
					<a class="share-button" onclick="'. $googleShareLink .   '"><img src="/resources/images/google_logo.png" alt="google+ icon" />Share</a>
					<a class="share-button" onclick="'. $redditShareLink .   '"><img src="/resources/images/reddit_logo.png" alt="reddit icon" />Submit</a>
				</div>
			</div>
		</div>
		
		<!-- Needs to go after to appear in from of the other overlay -->
		<div id="overlay-header" style="opacity:1;">
			' . getHeader(false) . '
			<div id="soundToggle" class="button" onclick="toggleSound()">' . ($_COOKIE['soundEnabled']!="0"?'Disable':'Enable') . ' Sound</div>
		</div>
	</div>
	';}else{
		echo getHeader(true) . '
	<div id="upload"><span id="dColorPicker"></span>
		<form enctype="multipart/form-data" method="post" action="upload.php" onsubmit="return s(this)">
			<div class="section" id="step1">
				<h1>Step 1: Choose your image</h1>
				<div style="float:right" id="imageFile-holder">
					<div class="button">
						Upload From Computer
						<input id="imageFile" name="imageFile" type="file" accept="image/*" onchange="loadFile(this)" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div style="float:right" class="button" onclick="showURL()">
					Upload From URL
				</div>
				<input id="imageFile-words" name="imageFile-words" style="width:0" type="text" onkeyup="loadURL()" />
				<div class="clearfix"></div>
				<div id="previewHolder" style="display:none;">
					<div id="focus"
						oncontextmenu="return false;"
						ontouchstart="setFocus(event,true);event.preventDefault()" ontouchmove="setFocus(event);event.preventDefault()" ontouchend="setFocus(event,false);event.preventDefault()"
						onclick="setFocus(event)" onmousedown="setFocus(event, true);event.preventDefault()" onmousemove="setFocus(event)" onmouseup="setFocus(event, false)"></div>
					<img id="preview" src="/resources/images/loading.gif"
						onload="previewLoad(this)" onerror="previewError(this)"
						oncontextmenu="setFocus(event);return false;"
						ontouchstart="setFocus(event,true);event.preventDefault()" ontouchmove="setFocus(event);event.preventDefault()" ontouchend="setFocus(event,false);event.preventDefault()"
						onclick="setFocus(event)" onmousedown="setFocus(event, true);event.preventDefault()" onmousemove="setFocus(event)" onmouseup="setFocus(event, false)" />
				</div>
				<br />
				<h1 id="previewText" style="display: none;">
					Step 2: Click image to set focus point
				</h1>
				<div class="clearfix"></div>
			</div>
			<div class="section" id="step3">
				<h1>Step 3: Choose background color</h1>
				<span id="choose-color">
					<script type="text/javascript">if(document.createElement("canvas").getContext)document.write("Right click image or use color picker");else document.write("Use color picker")</script>
				</span>
				<div id="colorPickerD"></div>
				<div class="clearfix"></div>
			</div>
			<div class="section" id="step4">
				<h1>Step 4: Submit</h1><input id="submitBtn" type="submit" class="button" value="Upload" />
			</div>

			<input type="hidden" id="fx" name="fx" />
			<input type="hidden" id="fy" name="fy" />
			<input type="hidden" id="color" name="color" value="' . $color . '" />
		</form>
	</div>
	';}
?>
</body>