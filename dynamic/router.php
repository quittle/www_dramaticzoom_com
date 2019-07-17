<?php
    require __DIR__ . '/vendor/autoload.php';

    lambda(function (array $event) {
        $path = $event['path'];
        if (strpos($path, '/Prod/') === 0) {
            $path = substr($path, 5);
        }
        $httpMethod = $event['httpMethod'];
        $querystringParameters = $event['queryStringParameters'];

        $file = substr($path, 1);

        $routes = get_routes();
        if (in_array($file, $routes)) {
            $msg = eval_to_string(getenv('LAMBDA_TASK_ROOT') . '/' . $file . '.php');
            return array(
                'body' => $msg,
                'headers' => array(
                    'Content-Type' => 'text/html',
                )
            );
        } else if (is_file($file)) {
            return array(
                'body' => file_get_contents($file),
                'headers' => array(
                    'Content-Type' => mime_content_type($file)
                )
            );
        } else {
            return array('body' => "Did not find route $file");
        }
    });

    function eval_to_string($file){
        ob_start();
        include $file;
        return ob_get_clean();
    }

    function get_routes() {
        # Get all files and directories in the root directory
        $contents = scandir('.');
        # Find the php files
        $contents = array_filter($contents, function($file) {
            return preg_match('/\.php$/', $file) and is_file($file);
        });
        # Remove the '.php'
        $contents = array_map(function($file) {
            return substr($file, 0, -4);
        }, $contents);
        return $contents;
    }
?>