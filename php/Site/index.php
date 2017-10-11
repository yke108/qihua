<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
header('Access-Control-Allow-origin:*');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
	exit;
}

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','../Apps/');
define('RUNTIME_PATH','../Runtime/');

// 引入ThinkPHP入口文件
// require '../ThinkPHP/ThinkPHP.php';
try {
	require 'config.base.php';
} catch (\Exception $e) {
	$code = $e->getCode();
	if($code){
		$code = intval($code);
	} else {
		$code = 400;
	}
	$data = [
		'code' => $code,
		'message' => $e->getMessage(),
	];
	echo json_encode($data);
	exit;
}


