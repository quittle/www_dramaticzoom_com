<?php
	include '../includes/header.php';
	header("HTTP/1.0 403 Forbidden");
	printHeadTop();
	printHeadBottom();
?>
<body>
	<?php echo getHeader(true); ?>
	<div class="section">
		<h1>Error 403</h1>
		We're sorry, but you can't look at this.
	</div>
</body>