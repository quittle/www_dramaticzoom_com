<?php
	include_once 'includes/header.php';
	printHeadTop();
	printHeadBottom();
	$username = $_GET['u'];
	$email = $_GET['e'];
	$special = $_GET['s'];
	
	include_once 'php/validateInputs.php';
	
	$positiveState = validate() && $username !== null && $username !== "" && $email !== null && $email !== "" && $special !== null && $special !== "";

	if($positiveState){
		include 'php/db.php';
		$dbCon = new DatabaseConnection();
		$user = $dbCon->getUserByUsername($username);

		$positiveState = $user['special'] === $special;
		
		if($positiveState){
			$dbCon->clearSpecialByUsername($username);
		}
	}
	
?>
<body>
	<?php echo getHeader(true); ?>
	<div class="section">
	<h1>
		<?php echo $positiveState ? "Registration Complete" : "Error"; ?>
	</h1>
	<p>
		<?php echo $positiveState ? "Thank you for registering for Dramatic Zoom." : ("There was a problem validating your email request." . ($user['status'] === 0 ? "<br /><br /><strong>Already registered</strong>" : "")); ?>
	</p>
	</div>
</body>