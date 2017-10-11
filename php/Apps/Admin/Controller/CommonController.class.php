<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Basic\CsException;
class CommonController extends  Controller{
    public function _initialize(){
        //验证登陆,没有登陆则跳转到登陆页面
        if (empty($_SESSION['username'])) {
        	//throw new CsException('未登录', 32);
        }
        return;
        if (!authCheck(MODULE_NAME . "/" . CONTROLLER_NAME . "/" . ACTION_NAME, session('userid'))) {
        	throw new CsException('你没有权限', 400);
        }
    }
    
    protected function ajaxReturn($data){
    	if (is_array($data)){
    		$data['message'] = 'OK';
    	} else {
    		$data = [
    			'message'=>$data,
    		];
    	}
    	!isset($data['code']) && $data['code'] = '000';
    	echo json_encode($data);
    	exit;
    }

    protected function _empty(){
        $this->error('你请求的页面不存在!');
    }
}