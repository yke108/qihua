<?php
namespace User\Controller;

use       Think\Controller;
use Common\Basic\CsException;

class AccountSecurityController extends CommonController {

    //更改密码
    public function editPassword() {
        /*判断旧密码的正确性*/
        $this->checkLogin();
        if (IS_AJAX) {
            //检查TOKEN
            // $this->checkActionToken();
            $data['oldPassword'] = I('json.oldPassword');
            $data['password'] = I('json.password');
            $data['repassword'] = I('json.repassword');
            $member = D('Member');

            if ($member->create($data)) {
                $memberObj = $member->get($this->uid);
                $originPwd = passencrypt($data['oldPassword'], $memberObj['salt']);
                if ($originPwd != $memberObj['password']) {
                    throw new CsException("The old password is incorrect!", 400);
                }
                /*验证通过，修改*/
                $salt = rand(1000, 9999);
                $password = passencrypt($data['password'], $salt);
                $member->Modify($this->uid, 'password', $password);
                $member->Modify($this->uid, 'salt', $salt);
                $this->ajaxReturn('success');
            } else {
                throw new CsException($member->getError(), 400);
            }
        }
        $this->display();
    }

    /**
     * 绑定手机
     */
    public function bindPhone() {
        $this->checkLogin();
        //非中国用户不用填写手机
        if (session('country') != 'CN') {
            $this->redirect('User/Account/Index');
        }
        $data['msgCode'] = I('post.msgCode');
        $data['phone'] = I('post.phone');
        $member = D('Member');

        if (IS_AJAX && IS_POST) {
            if ($member->create($data)) {
                //手机存在性
                if ($member->checkPhoneIsExist($data)) {
                    $res['msg'] = 'Phone Number Exist Already!';
                    $res['code'] = '400';
                    $res['data']['error'] = $res['msg'];
                    $this->ajaxReturn($res);
                }

                //手机验证码
                $code = checkMessage($data['msgCode']);
                if ($code['code'] == 400) {
                    $this->ajaxReturn($code);
                }
                //或运算, 保证不影响其他状态值
                $bind = $member->getOneFiled($this->uid, 'bind') + 0;
                $bind = C('STATUS_BIND')['BIND_PHONE'] | $bind;
                $member->Modify($this->uid, 'phone', $data['phone']);
                $member->Modify($this->uid, 'bind', $bind);
                $res['msg'] = 'success';
                $res['code'] = '200';
                $res['data']['url'] = '/User/AccountSecurity/bindPhone';
                $this->ajaxReturn($res);
            } else {
                $res['msg'] = $member->getError();
                $res['code'] = '400';
                $this->ajaxReturn($res);
            }
        }

        $memberObj = $member->get($this->uid);
        $PhoneCount = D('User/Member')->CheckPhoneNum( $memberObj['phone'] );
        $this->assign('PhoneCount', $PhoneCount);
        $this->assign('memberObj', $memberObj);
        $this->display();
    }

    /**
     * 绑定邮箱
     */
    public function bindEmail() {
        $this->checkLogin();
        $data['email'] = strtolower(I('post.email'));
        $data['msgCode'] = I('post.msgCode');
        $member = D('Member');

        if (IS_AJAX && IS_POST) {
            if ($member->create($data)){
                //邮箱存在性
                if ($member->checkEmailIsExist($data)) {
                    $res['msg'] = 'Email Address Exist Already!';
                    $res['code'] = '400';
                    $res['data']['error'] = $res['msg'];
                    $this->ajaxReturn($res);
                }

                //邮箱验证码
                $code = checkEmail($data['msgCode']);
                if ($code['code'] == 400) {
                    $this->ajaxReturn($code);
                }
                //或运算, 保证不影响其他状态值
                $bind = $member->getOneFiled($this->uid, 'bind') + 0;
                $bind = C('STATUS_BIND')['BIND_EMAIL'] | $bind;
                $member->Modify($this->uid, 'email', $data['email']);
                $member->Modify($this->uid, 'bind', $bind);//已绑定邮箱
                $res['msg'] = 'success';
                $res['code'] = '200';
                $res['data']['url'] = '/User/AccountSecurity/bindEmail';
                $res['data']['email'] = $this -> hiddenEmail($data['email']);
                $this->ajaxReturn($res);
            } else {
                $res['msg'] = $member->getError();
                $res['code'] = '400';
                $this->ajaxReturn($res);
            }
        }
        $memberObj = $member->get($this->uid);
        $memberObj['email'] = $this -> hiddenEmail($memberObj['email']);

        $this->assign('memberObj', $memberObj);
        $this->display();
    }


    /**
     * *
     * @string  $email       邮箱地址
     * @return  返回隐藏中间部分的邮箱地址
     */
    private function hiddenEmail($email){
        if(empty($email)){
            return false;
        }
        $arr = explode('@', $email);
        $len = strlen($arr['0']);
        if($len > 3){
            $rest = substr($arr[0],0,3);
        }else{
            $rest = substr($arr[0],0,1);
        }
        $email = $rest."*****@".$arr['1'];
        return $email;
    }
} 