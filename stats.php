<?php
	include_once 'includes/header.php';
	printHeadTop();
	printHeadBottom();
	
	include_once 'php/db.php';
	
	$dbCon = new DatabaseConnection();
?>
<body>
	<?php echo getHeader(true); ?>
	<div class="section">
		<h1>
			General Statistics
		</h1>
		<p><?php
			
		?></p>
	</div>
</body>