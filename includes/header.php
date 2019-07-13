<?php
	$realLoc = "";
	function setRealLoc($loc){
		global $realLoc;
		$realLoc = $loc;
	}
	function getRealLoc(){
		global $realLoc;
		return $realLoc;
	}
	
	function mobileCSS($loc){
		return '
			<link rel="stylesheet" href="' . $loc . '" type="text/css" media="(max-device-width: 720px)" />
			<link rel="stylesheet" href="' . $loc . '" type="text/css" media="handheld" />
			<!--link rel="stylesheet" href="' . $loc . '" type="text/css" /-->';
	}
	
	function printHeadTop(){
		print '
			<!DOCTYPE html>
			<head>
				<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
				<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1" />
				<title>Dramatic Zoom</title>
				<link rel="icon" href="/favicon.ico" sizes="16x16" type="image/vnd.microsoft.icon" />
				<link rel="stylesheet" href="/css/main.css" type="text/css" />';
		print mobileCSS("/css/mobile.css");
		
		$loc = substr($_SERVER['PHP_SELF'], 0, -3);
		$cssBaseUrl = '/css' . $loc . 'css';
		$jsBaseUrl = '/js' . $loc . 'js';
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . $cssBaseUrl))
			echo '<link rel="stylesheet" type="text/css" href="' . $cssBaseUrl . '" />';
			
		print '
				<script src="/js/DJuice.js"></script>
				<script src="/js/DJuiceUI.js"></script>
				<script src="/js/otherScript.js"></script> <!-- For hash functions -->
				<script src="/js/main.js"></script>
		';
		
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . $jsBaseUrl)){
			echo '<script src="' . $jsBaseUrl . '"></script>';
		}
		
		$realLoc = $_SERVER['PHP_SELF'];
		setRealLoc(substr($realLoc, 1, strrpos($realLoc, ".")-1));
	}
	function printHeadBottom(){
		$rl = getRealLoc();
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/css/m." . $rl . ".css")){
			print mobileCSS("/css/m.$rl.css");
		}
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/js/m." . $rl . ".js")){
			print '<script>window.jLoc="' . $rl . '"</script>';
		}
		print'
				<script> <!-- Set user agent as html tag -->
					var ie = navigator.userAgent;
					if((/MSIE|Trident/).test(ie))
						document.documentElement.className += " ie ";
					document.documentElement.setAttribute("data-ua", navigator.userAgent)
				</script>
				<link rel="stylesheet" href="/css/ie.css" type="text/css" /> <!-- IE only stylesheet -->
				
				<!--[if !IE]><!-->
					<script>
						if(/*@cc_on!@*/false){
							document.documentElement.className+=" ie10 "
							document.write("<link rel=\"stylesheet\" href=\"/css/ie10.css\" type=\"text/css\" />");
						}
					</script>
				<!--<![endif]-->
				<!--[if lte IE 9]>
					<link rel="stylesheet" href="/css/ie9.css" type="text/css" />
					<script defer="defer" src="/js/pngfix.js"></script>
				<![endif]-->
				<!--[if lt IE 9]>
					<link rel="stylesheet" href="/css/ie8.css" type="text/css" />
				<![endif]-->
			</head>
		';
	}
	
	function getHeader($links){
		return '
			<div id="mobile-detector"></div>
			<div id="header">
				<a href="/">Dramatic Zoom</a> <span id="small">Share Bigger.</span>
				' .
				($links?
					'<br />
					<div id="navbar">
						<a class="navbar-item who" onclick="showDrop(\'who\')">Who?</a>
						<a class="navbar-item what" onclick="showDrop(\'what\')">What?</a>
						<a class="navbar-item login" onclick="showDrop(\'login\')">Log In!</a>
					</div>'
					:'') . '
			</div>
			<a id="bugReport" onclick="showDrop(\'bugReport\')"><div id="bugReport-top" title="Report A Bug"></div></a>
			<div id="drop-container">
				<div id="alertMessage">
					Signed in as <strong id="alert-user"></strong><br />
					<a href="/manage">Manage Account</a><br />
					<a onclick="logout()">Sign Out</a>
				</div>
				<div class="drop who">
					Invented and programmed by <a href="http://dustindoloff.com" target="_blank">Dustin Doloff</a>, solving the problems you didn&apos;t know existed.
				</div>
				<div class="drop what">
					The internet is filled with services dedicated to sharing photos, but none of them help share images in a way that immediately draws the viewer&apos;s <strong>attention where you want</strong>. Dramatic Zoom aims to change this utilizing the latest in <strong>image zooming technologies</strong>.  Our goal is to <strong>revolutionize</strong> the way you share photos that dramatically zoom.  Here&apos;s <a target="_blank" href="http://drzm.co/iSctp">an example</a> of what a zoomograph looks like.
				</div>
				<div class="drop login">
					<form id="login" onsubmit="return (loginSubmit() && false)">
						<div>Username:<input id="login-user" type="text" /></div>
						<div>Password:<input id="login-pass" class="indicator" type="password" onkeypress="checkCaps(event, \'login-pass\')" /></div>
						<div>
							<span>
								<a onclick="showDrop(\'forgotPassword\')">Forgot Password?</a><br />
								No Account?&nbsp;&nbsp;<a onclick="showDrop(\'register\')">Register!</a>
							</span>
							<span>
								<input class="button" type="submit" value="Log In" />
							</span>
						</div>
					</form>
				</div>
				<div class="drop register">
					<form id="register" onsubmit="return register()">
						<table>
							<tr>
								<td>
									<label for="register-user">Username:</label>
									<input id="register-user" name="register-user" class="indicator" type="text" onkeyup="reg_checkuser()" />
								</td>
								<td>
									<label for="register-email">Email:</label>
									<input id="register-email" name="register-email" class="indicator" type="text" onkeyup="checkemail(\'register-email\')" />
								</td>
							</tr><tr>
								<td>
									<label for="register-pass">Password:</label>
									<input id="register-pass" name="register-pass" class="indicator" type="password" onkeypress="checkCaps(event, \'register-pass\', \'register-pass2\')" onkeyup="checkpass(\'register-pass\', \'register-pass2\')" />
								</td>
								<td>
									<label for="register-pass2">Confirm Password:</label>
									<input id="register-pass2" name="register-pass2" class="indicator" type="password" onkeypress="checkCaps(event, \'register-pass\', \'register-pass2\')" onkeyup="checkpass(\'register-pass\', \'register-pass2\')" />
								</td>
							</tr>
							<tr>
								<td>
									<label for="register-agree">I agree to the <a target="_blank" href="/privacy">privacy policy</a></label> <input id="register-agree" name="register-agree" type="checkbox" />
								</td>
								<td>
									<input id="register-submit" class="button" type="submit" value="Register" />
								</td>
						</table>
					</form>
				</div>
				<div class="drop bugReport">
					<form id="bug" onsubmit="return bugSubmit(this)">
						<h3>
							Report A <select name="reportType">
								<option>Website Issue</option>
								<option>Suggestion</option>
								<option>Content Problem</option>
								<option>Legal Issue</option>
								<option>nother Thing</option>
							</select>
						</h3>
						<div>
							Email: <input type="text" name="email" placeholder="(Optional)" />
						</div>
						<div>
							<textarea name="description" placeholder="Describe the problem"></textarea>
						</div>
						<div>
							<input type="submit" value="Report!" />
						</div>
					</form>
				</div>
				<div class="drop forgotPassword">
					<form id="forgot" onsubmit="return forgotSubmit(this)">
						<h3>
							Reset Password
						</h3>
						<div>
							Email: <input type="text" name="email" />
						</div>
						<div>
							<input type="submit" value="Reset Password" />
							<div class="clearfix"></div>
						</div>
					</form>
				</div>
			</div>
			' . ($links ? '
				<div id="bottom-navbar">
					<a class="bottom-navbar-item" href="/help">Help</a>
					<a class="bottom-navbar-item" href="/privacy">Privacy Documents</a>
				</div>'
			: '') . ' 
		';
	}
?>