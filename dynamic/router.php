<?php
    require __DIR__ . '/vendor/autoload.php';

    $mimes = new \Mimey\MimeTypes;

    lambda(function (array $event) {
        $path = $event['path'];
        if (strpos($path, '/Prod/') === 0) {
            $path = substr($path, 5);
        }
        initialize_globals($event);

        $file = substr($path, 1);
        if (preg_match('/\.php$/', $file)) {
            $file = substr($file, 0, -4);
        }

        $routes = get_routes();
        if (in_array($file, $routes)) {
            $msg = eval_to_string(getenv('LAMBDA_TASK_ROOT') . '/' . $file . '.php');
            return array(
                'body' => base64_encode($msg . '\n'),
                'headers' => array(
                    'Content-Type' => 'text/html',
                ),
                'isBase64Encoded' => TRUE
            );
        } else if (is_file($file)) {
            return array(
                'body' => base64_encode(file_get_contents($file)),
                'headers' => array(
                    'Content-Type' => get_content_type($file)
                ),
                'isBase64Encoded' => TRUE
            );
        } else {
            return array('body' => "Did not find route $file");
        }
    });

    function get_content_type($file_name) {
        global $mimes;
        $extension = pathinfo($file_name)['extension'];
        $mime_type = $mimes->getMimeType($extension);
        echo "File: $file_name Extension: $extension MimeType: $mime_type";
        return $mime_type;
    }

    function eval_to_string($file) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    function get_routes() {
        # Get all files and directories in the root directory
        $contents = scan_dir_recursive('.');

        # Find the php files
        $contents = array_filter($contents, function($file) {
            return preg_match('/\.php$/', $file) and is_file($file);
        });
        # Remove the '.php' and the leading './'
        $contents = array_map(function($file) {
            return substr($file, 2, -4);
        }, $contents);
        return $contents;
    }

    function initialize_globals(array $event) {
        global $_SERVER, $_GET, $_COOKIE, $_POST;

        $_SERVER['QUERY_STRING'] = $event['queryStringParameters'];
        $_GET = $event['queryStringParameters'];
        if (array_key_exists('Cookie', $event['headers'])) {
            $_COOKIE = http_parse_cookie($event['headers']['Cookie']);
        } else {
            $_COOKIE = array();
        }
        parse_str($event['body'], $_POST);
    }

    function scan_dir_recursive($root) {
        $ret = array();
        foreach (scandir($root) as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }

            $path = $root . '/' . $dir;

            if (is_file($path)) {
                array_push($ret, $path);
            } else {
                $ret = array_merge($ret, scan_dir_recursive($path));
            }
        }
        return $ret;
    }


    # Source: https://wtherror.wordpress.com/2015/01/16/problems-upgrading-php55-curl-openssl-pecl-http/
    function http_parse_cookie($szHeader, $object = true){
        $obj         = new stdClass;
        $arrCookie   = array();
        $arrObj      = array();
        $arrCookie =  explode("\n", $szHeader);
        for($i = 0; $i<count($arrCookie); $i++){
            $cookie          = $arrCookie[$i];
            $attributes      = explode(';', $cookie);
            $arrCookie[$i]   = array();
            foreach($attributes as $attrEl){
                $tmp = explode('=', $attrEl, 2);
                if(count($tmp)<2){
                    continue;
                }
                $key     = trim($tmp[0]);
                $value   = trim($tmp[1]);
                if($key=='version'||$key=='path'||$key=='expires'||$key=='domain'||$key=='comment'){
                    if(!isset($arrObj[$key])){
                        $arrObj[$key] = $value;
                    }
                }else{
                    $arrObj['cookies'][$key] = $value;
                }
            }
        }
        if($object===true){
            $obj     = (object)$arrObj;
            $return  = $obj;
        }else{
            $return = $arrObj;
        }
        return $return;
    }
?>