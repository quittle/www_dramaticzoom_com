<?php
	include 'includes/header.php';
	printHeadTop();
	printHeadBottom();	
	
	require_once 'php/cookie.php';
	$ret = getUserByCookie($_COOKIE['login']);
?>

<body>
	<?php echo getHeader(true); ?>
	<div id="manage" class="section">
		<h1>Manage Account</h1>
		<div id="manage-prompt">
			<?php
				if(!$ret)
					echo "Login to manage account";
			?>
		</div>
		<div id="manage-real" style="display:none">
			<form id="form-reset" onsubmit="return submitChangePassword(this)">
				<h2>Change Password</h2>
				<div>Current Password: <input id="reset-orig" type="password" /></div>
				<div>New Password: <input id="reset-password1" class="indicator" type="password" onkeyup="checkpass('reset-password1', 'reset-password2')" /></div>
				<div>Confirm Password: <input id="reset-password2" class="indicator" type="password" onkeyup="checkpass('reset-password1', 'reset-password2')" /></div>
				<input type="submit" value="Reset Password" />
			</form>
			<form id="form-changeEmail" onsubmit="return submitChangeEmail(this)">
				<h2>Change Email</h2>
				<div>New Email: <input id="reset-email" class="indicator" type="text" onkeyup="checkemail('reset-email')" /></div>
				<input type="submit" value="Change Email" />
			</form>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="section">
		<h1>My Zoomographs</h1>
		<div id="zoomographs">
			<?php
				if(!$ret)
					echo "Login to view zoomographies";
			?>
			<div class="clearfix"></div>
		</div>
	</div>
</body>