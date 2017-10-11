<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Common\Basic\CsException;
class PublicController extends CommonController {
    //登录页面
    public function login(){
        if(empty($_SESSION['username'])){
            $parent_url =  base64_decode(I('get.url'));
            $this->assign('parent_url',$parent_url);
            $this->display();
        }else{
            $this->redirect('Index/index');
        }
    }

    //退出
    public function logout(){
        unset($_SESSION['username']);
        unset($_SESSION['userid']);
        unset($_SESSION['gid']);
        unset($_SESSION['gname']);
        $return=array();
        $return['message']='退出成功！';
          $return['status']=true;
        $this->ajaxReturn($return);
    }

    //登录验证
    public function checkLogin(){
        //获取post数据
        $data = array();
        $data['captcha'] = I('json.captcha');
        $data['username'] = I('json.username');
        $data['password'] = I('json.password');

        //验证数组
        $rules = array(
            //array('captcha','require','验证码必须填写'),
            //array('captcha','checkverify','验证码错误',0,'callback'),
            array('username','require','用户名必须填写'),
            array('password','require','密码必须填写'),
            //array('password','checkpasswd','账号密码不正确',0,'callback',3,array($data['username'])),
        );

        $login = D('User');

        if (!$map = $login->validate($rules)->create($data)){
            // 对login数据验证
        	throw new CsException($login->getError(), 702);
        }else{
            //查询用户登录信息
            //获取登录密码随机数
            $field='salt';
            $where=array('username'=>$data['username']);
            $salt=$login->getUserData($field,$where);
            $status = $login->getaccountinfo($data['username'],$data['password'],$salt['salt']);
            if(!$status){
            	throw new CsException('登陆失败！用户名或密码错误。', 701);
            }

            //更新登录信息
            $info = $login->loginupdate($data['username'],$data['password'],$salt['salt']);
            if(!$info){
            	throw new CsException('更新时间和ip出错', 700);
            }

            session('username',$status['username']);
            session('userid',$status['id']);
            session('logintime',$status['lastlogintime']);

            //登录成功
            $data = [
            	'user'=>[
	            	'userid'=>$status['id'],
	            	'realname'=>$status['realname'],
            		'department'=>$status['department'],
            		'identity'=>$status['group'],
	            ],
            ];
            $this->ajaxReturn($data);
        }
    }

    //生成验证码
    public function verify(){
        ob_end_clean();
        $verify=new \Think\Verify();
        $verify->length=4;
        $verify->fontSize =50;     // 验证码字体大小
        $verify->imageH = 100;       // 验证码高度
        $verify->useImgBg = false;   // 开启验证码背景
        $verify->useNoise = false;  // 关闭验证码干扰杂点
        $verify->useCurve = false;
        $verify->fontttf='6.ttf';
        $verify->entry();
    }
}