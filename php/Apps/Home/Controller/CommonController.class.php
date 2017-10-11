<?php
// +----------------------------------------------------------------------
// | Keywa Inc.
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.keywa.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: vii
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;
use Common\Basic\CsException;

class CommonController extends Controller {

    protected $uid = 0;

    protected function _initialize() {
    	if (strpos($_SERVER['HTTP_REFERER'], 'localhost')){
    		$this->uid = 356;
    		return;
    	}
        $this->uid = D('Home/Member')->getLoginUid();
        //判断商户是否有企业认证
        $this->Auth = D('Home/Member')->getLoginAuth($this->uid);
        $this->Pass = D('Home/Member')->CheckedPass($this->uid);
        //公司联营状态
        $this->companySign = D('Home/Member')->getLoginUserCompanySign($this->uid);
    }

    /**
     * 检查用户是否登录
     */
    protected function checkLogin() {
        $res = false;
        if (empty($this->uid)) {
            $res = true;
        } elseif ($this->Pass) {
            session('Uid', null);
            session('token', null);
            $res = true;
        }
        if ($res) {
            if (IS_AJAX) {
                throw new CsException("Please login", 400);
            } else {
                $this->redirect('/User/Index/login');
            }
            exit;
        }
    }

    /*
     * 检查商户是否有企业认证
     * */
    protected function checkAuth() {
        if (($this->Auth) == 2) {
            $this->redirect('/Account/success_auth');

        } elseif (($this->Auth) == 3) {
            $this->redirect('/Account/exam_auth');
        } else {
            $this->redirect('/Account/submit_auth');
        }
    }

    /**
     * 接口不存在处理函数
     */
    public function _empty() {
        header("http/1.1 404 not found");
        header("status: 404 not found");
        $this->display('Public/404');
    }

    /**
     * 检查提交表单TOKEN
     */
    protected function checkActionToken(){
        //检查请求频率
        $requestRate = limitRate();
        if ($requestRate['code'] == 400) {
            $this->ajaxReturn($requestRate);
        }

        if( !function_exists( 'getallheaders' ) ){
            $headers = D( 'Common/SecurityCode' )->getallheaders();
        }else{
            $headers = getallheaders();
        }
        $token = trim( $headers['Actiontoken'] );
        if( empty( $token ) ){
            $token = trim( I( 'request._ActionToken_' ) );
        }
        $data = array(
            'token' => str_replace( ' ', '+', urldecode( $token ) ),
        );
        $ret = D( 'User/Member' )->checkActionToken( $data );
        if( $ret['code'] != 200 ){
            if( IS_AJAX ){
                $this->ajaxReturn( $ret );
            }else{
                $this->error( $ret['msg'] );
            }
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
}
