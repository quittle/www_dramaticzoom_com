<?php
	include '../includes/header.php';
	header("HTTP/1.0 404 Not Found");
	printHeadTop();
	printHeadBottom();
?>
<body>
	<?php echo getHeader(true); ?>
	<div class="section">
		<h1>Error 404</h1>
		We're sorry, the page you were looking for does not exist.
	</div>
</body>