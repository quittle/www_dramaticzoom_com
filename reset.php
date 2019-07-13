<?php
	include_once 'includes/header.php';
	printHeadTop();
	printHeadBottom();
	$username = $_GET['u'];
	$email = $_GET['e'];
	$special = $_GET['s'];
	
	include_once 'php/security.php';
	
	$positiveState = validate() && $username !== null && $username !== "" && $email !== null && $email !== "" && $special !== null && $special !== "";

	if($positiveState){
		include 'php/db.php';
		$dbCon = new DatabaseConnection();
		$user = $dbCon->getUserByUsername($username);

		$positiveState = (($user['special'] === $special) && ($user['status'] === DatabaseConnection::ACCOUNT_STATUS_FORGOT));
	}
	
?>
<body>
	<?php echo getHeader(true); ?>
	<div class="section">
	<h1>
		Password Reset
	</h1>
	<p>
		<?php echo $positiveState ?
			'<form id="form-forgot" onsubmit="return resetSubmit(this,\'' . $username . '\',\'' . $special . '\')">
				<div>
					<span>New Password: </span><span><input type="password" name="pass1" /></span>
				</div>
				<div>
					<span>Confirm Password: </span><span><input type="password" name="pass2" /></span>
				</div>
				<div>
					<input type="submit" value="Reset" />
				</div>
			</form>' :
			'<strong>Error: Invalid credentials.</strong>  If you copied the link from the email, make sure you copied it correctly';
		?>
	</p>
	</div>
</body>