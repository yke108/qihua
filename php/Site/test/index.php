<?php
header('Access-Control-Allow-origin:*');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
	exit;
}
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','../../Apps/');
define('RUNTIME_PATH','../../Runtime/');
define('MULTI_MODULE', false);
define('BIND_MODULE','Admin');
try {
	require '../config.base.php';
} catch (\Exception $e) {
	$code = $e->getCode();
	$data = [
		'code'=>$code > 0 ? $code : 400,
		'message'=>$e->getMessage(),
	];
	echo json_encode($data);
}
