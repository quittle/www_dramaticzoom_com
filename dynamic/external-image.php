<?php
	$url = $_GET['url'];
	$options = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"Accept-language: en\r\n" .
					"Accept-Language: en-us\r\n" .
					"User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)\r\n" .
					"Connection: Keep-Alive\r\n" .
					"Cache-Control: no-cache\r\n"
		)
	);
	
	$context  = stream_context_create($options);
	$ret = file_get_contents($url, false, $context);
	
	//Set headers
	$r = get_headers($url);
	header($r[0]);
	header($r[1]);
	header($r[2]);
	header($r[3]);
	header($r[4]);
	header($r[5]);
	header($r[6]);
	
	//Print the image
	echo $ret;
?>