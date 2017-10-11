<?php
namespace User\Controller;

use Think\Controller;
use Think\Cache\Driver\Redis;
use Common\Basic\CsException;

class IndexController extends CommonController {

    /*检测手机号码是否已经被注册*/
    public function CheckPhone() {
        /*检测手机号*/
        $data['phone'] = $_POST['phone'];
        $data['act'] = $_POST['act'];
        if ($data['act'] == 'reg') {
            //手机白名单
            if (D('User/Member')->IsWhitePhone($data)) {
                $res['msg'] = '手机号码可以正常使用';
                $res['code'] = '200';
                $res['data']['ok'] = '手机号码可以正常使用';
                $this->ajaxReturn($res);
            }
            $rest = D('User/Member')->checkPhoneIsRight($data['phone']);
            if ($rest == 2) {
                $res['ok'] = '手机号码可以正常使用';
                $this->ajaxReturn($res);
            } else {
                $res['error'] = 'This phone number has been registered already.';
                $this->ajaxReturn($res);
            }
        } else if ($data['act'] == 'forget') {
            $rest = D('User/Member')->checkPhoneIsRight($data['phone']);
            if ($rest == 1) {
                $res['code'] = '200';
                $res['data']['ok'] = '手机号码可以正常使用';
            } else {
                $res['code'] = '400';
                $res['data']['error'] = 'Please use the registered phone number.';
            }

            $this->ajaxReturn($res);
        }
    }

    /**
     * 图片验证码检测
     */
    public function CheckVerify() {
        $verify = new \Think\Verify(array('reset' => false));
        $captcha = $_POST['captcha'];
        if ($verify->check ( $captcha ) == false) {
            $res ['msg'] = 'Sorry! Incorrect Captcha Code Format';
            $res ['code'] = '400';
            $res ['data'] ['error'] = 'Sorry! Incorrect Captcha Code Format';
            $this->ajaxReturn ( $res );
        } else {
            $res ['msg'] = '验证码正确';
            $res ['code'] = '200';
            $res ['data'] ['ok'] = '验证码正确';
            $this->ajaxReturn ( $res );
        }
    }
    
    /**
     * 发送邮件验证码 
     */
    public function sendRegisterEmail() {
        if (IS_AJAX) {
            $email = I('json.email');
            if (empty($email)) {
                throw new CsException('The Email is empty!', 400);
            }
            $ip = get_client_ip();
            $member = D('User/Member');
            $random = uniqid();
            $IpNum = $member->checkIp($ip);
            $num = $member->checkSendEmailRate($email, $random);
            if ($num >0 && $num < 4 && $IpNum < 100) {
                $key = md5($email.$random);
                $url = U('User/Index/registerForm', ['key'=> $key], '.html', $_SERVER['HTTP_HOST']);
                $title = '【keywa】email verification';
                $content = "<a target='_blank' href='{$url}'>register</a>";
                $ret = send_mail($email, $title, $content, false);
                $member->unsetSendEmailLock($email, $random);
                if ($ret['code'] == 200) {
                    $redis = new Redis();
                    $cacheKey = 'string:verifyCode:email:' . $key;
                    $redis->set($cacheKey, $email, 86400);
                    $this->ajaxReturn('success');
                } else {
                    throw new CsException($ret['msg'], 400);
                }
            } else {
                $member->unsetSendEmailLock($email, $random);
                throw new CsException('Three Email Verification Codes in 30 minutes only!', 400);
            }
        }
    }

    /**
     * 发送邮件验证码
     */
    public function sendEmail() {
        if (IS_AJAX) {
            $email = I('json.email');
            if (empty($email)) {
                throw new CsException('The Email is empty!', 400);
            }
            $ip = get_client_ip();
            $member = D('User/Member');
            $random = uniqid();
            $IpNum = $member->checkIp($ip);
            $num = $member->checkSendEmailRate($email, $random);

            if ($num >0 && $num < 4 && $IpNum < 100) {
                $code = sendEmail($email);
                $member->unsetSendEmailLock($email, $random);
                if ($code['code'] == 200) {
                    $this->ajaxReturn('success');
                } else {
                    throw new CsException($code['msg'], 400);
                }
            } else {
                $member->unsetSendEmailLock($email, $random);
                throw new CsException('Three Email Verification Codes in 30 minutes only!', 400);
            }
        }
    }

    /**
     * 检测邮箱存在性
     * @return int; 0:格式未通过检验, 1:已经存在, 2:不存在
     */
    public function CheckEmail() {
        $data['email'] = I('json.email');
        $rest = D('User/Member')->checkEmailIsRight($data['email']);
        $this->ajaxReturn(['status'=> $rest]);
        // $data['act'] = $_POST['act'];
        // if ($data['act'] == 'reg') {
        //     if ($rest == 2) {
        //         $res['ok'] = '';
        //         $this->ajaxReturn($res);
        //     } else {
        //         $res['error'] = D('User/Member')->getError();
        //         $this->ajaxReturn($res);
        //     }
        // } else if ($data['act'] == 'forget') {
        //     if ($rest == 1) {
        //         $res['ok'] = '';
        //         $this->ajaxReturn($res);
        //     } else {
        //         $res['error'] = 'Please use the registered Email Address.';
        //         $this->ajaxReturn($res);
        //     }
        // }
    }

    /*检测用户名是否已经被注册*/
    public function CheckUserName() {
        /*检测用户名*/
        $data['username'] = I('json.username');
        $rest = D('User/Member')->checkUserNameIsRight($data['username']);
        if ($rest) {
            $this->ajaxReturn('用户名可以注册');
        } else {
            throw new CsException('This member id has been registered already.', 400);
        }
    }

    /*
     * 判断失败次数
     * */
    public function getCount() {
        $name = trim(I('json.username'));
        if (!empty($name)) {
            $redis = \Think\Cache::getInstance('Redis');
            $art = $redis->get("string:fail:{$name}");
            $art = $art ? (int)$art : 0;
            $this->ajaxReturn(['count'=> $art]);
        }
    }


    /*登录*/
    Public function login() {
        if (IS_POST) {
            /*登录验证*/
            $data['username'] = trim(I('json.username'));
            $data['password'] = trim(I('json.password'));
            //$data['captcha'] = I('json.captcha');

            $member = D('member');
            $redis = \Think\Cache::getInstance('Redis');

            $rule = array(
                array('username', 'require', 'username required！'),
                array('password', 'require', 'password required！'),
            );
            if ($member->validate($rule)->create($data)) {
//                 $failCount = $redis->get("string:fail:".$data['username']) + 0;
//                 if ($failCount >= 3) {
//                     $verify = new \Think\Verify();
//                     if (empty($data['captcha']) || $verify->check($data['captcha']) == false) {
//                         throw new CsException("Sorry! Incorrect Captcha Code Format", 400);
//                     }
//                 }
                /*redis取出username*/
                $phoneCacheKey = 'string:phone:' . $data['username'];
                /*拿到对应的用户Uid*/
                if ($redis->exists("member:{$data['username']}")) {
                    $keys = $redis->get("member:{$data['username']}");
                } else if ($redis->exists($phoneCacheKey)) {
                    $keys = $redis->get($phoneCacheKey);
                } else {
                    $keys = '';
                }
                if ($keys) {
                    /*取出用户名*/
                    if ($redis->hget("hash:member:{$keys}", 'username') === $data['username'] || $redis->hget("hash:member:{$keys}", 'phone') === $data['username']) {
                        //判断用户状态
                        if ($redis->hget("hash:member:{$keys}", 'status') != 1) {
                            throw new CsException("Sorry! Username does not exist.", 400);
                        }
                        //取出对应密码
                        $passData = $redis->hmget("hash:member:{$keys}", array('password', 'salt'));
                        $salt = $passData['salt'];
                        $pass = $passData['password'];
                        $password = passencrypt($data['password'], $salt);
                        if ($pass == $password) {
                            /*登录成功新增最新登录时间之前,获取当前时间存入上一次登录时间*/
                            $lastLoginTime = $redis->hget("hash:member:{$keys}", 'lastLoginTime');
                            $redis->hset("hash:member:{$keys}", 'recentLoginTime', $lastLoginTime);
                            //登录之后，把uid,usrname存到session
                            $m = $redis->hMGet("hash:member:{$keys}", ['username', 'country', 'img']);
                            session('Uid', $keys);
                            session('memberName', $m['username']);
                            session('country', $m['country']);
                            if ($m['img']) {
                                session('userHeadImg', $m['img']);
                            }

                            //更新hash:member:Uid表的信息
                            $ip = get_client_ip();; //最近登录ip
                            $t = time();//最新登录时间
                            //存入hash:member:Uid
                            $info = $redis->hmset("hash:member:{$keys}", array('lastLoginIp' => $ip, 'lastLoginTime' => $t));
                            if (!$info) {
                                throw new CsException("login error", 400);
                            }
                            //登录成功
                            $token = $keys . $password . C('LOGIN_NUM')[0];
                            session('token', md5($token));

                            $authParam = array(
                                'id'   => $keys,
                                'time' => time(),
                            );
                            D('User/Member')->buildAutHCode($authParam);

                            if ($redis->exists("string:fail:{$data['username']}")) {
                                $redis->del("string:fail:{$data['username']}");
                            }
                            $this->ajaxReturn('success');
                        } else {
                            //加一个错误次数记录
                            if (!$redis->exists("string:fail:{$data['username']}")) {
                                $redis->set("string:fail:{$data['username']}", 1);
                                $redis->expire("string:fail:{$data['username']}", 60 * 60);
                            } else {
                                $str = $redis->get("string:fail:{$data['username']}");
                                $redis->set("string:fail:{$data['username']}", $str + 1);
                                $redis->expire("string:fail:{$data['username']}", 60 * 60);
                            }
                            throw new CsException("Sorry! Incorrect Password.", 400);
                        }
                    } else {
                        throw new CsException("Sorry! Username does not exist.", 400);
                        
                    }
                } else {
                    //登录失败
                    if (!$redis->exists("string:fail:{$data['username']}")) {
                        $redis->set("string:fail:{$data['username']}", 1);
                        $redis->expire("string:fail:{$data['username']}", 60 * 60);
                    } else {
                        $str = $redis->get("string:fail:{$data['username']}");
                        $redis->set("string:fail:{$data['username']}", $str + 1);
                        $redis->expire("string:fail:{$data['username']}", 60 * 60);
                    }
                    throw new CsException("Sorry! Username or Password incorrect.", 400);
                }
            } else {
                throw new CsException($member->getError(), 400);
            }
        }
        if (!empty($_SESSION['Uid'])) {
            /*session直接登录到首页*/
            $this->redirect('/home/my/user');
        } else {
            $this->display('login');
        }
    }
    
    public function forget_password(){
    	$this->display();
    }
    
    public function forget_password2(){
    	$this->display();
    }
    
    public function forget_password3(){
    	$this->display();
    }

    /*注册*/
    public function register() {
        if (IS_AJAX) { 
            $member = D('Member');
            $redis = \Think\Cache::getInstance('Redis');
            /*接收数据*/
            $password = trim(I('json.password'));
            $repassword = trim(I('json.repassword'));
            $companyName = trim(I('json.companyName'));
            $firstName = trim(I('json.firstName'));
            $secondName = trim(I('json.secondName'));
            $foxedPhone = trim(I('json.foxedPhone'));
            $areaId = I('json.areaId');
            $addressDetail = trim(I('json.addressDetail'));
            $phone = trim(I('json.phone'));
            // 验证必填参数
            if (empty($password) || empty($repassword)){
            	throw new CsException("Password can not be empty!", 400);
            }
            if (empty($companyName)){
            	throw new CsException("Company name can not be empty!", 400);
            }
            if (empty($firstName)){
            	throw new CsException("First name can not be empty!", 400);
            }
            if (empty($addressDetail)){
            	throw new CsException("Address can not be empty!", 400);
            }
            //foxed phone
            //area id
            // 密码是否一致
            if ($password != $repassword) {
                throw new CsException("Your confirmed password and password do not match!", 400);
            }
            // 判断邮箱认证
            $email = session('registerEmail');
            if (empty($email)) {
                throw new CsException("The email has not been validated", 400);
            }
            $username = $email;
            // 验证邮箱存在
            if (!$member->checkUserNameIsRight($email)) {
                throw new CsException("This email has been registered already.", 400);
            }
            //检测手机存在
            if (!empty($phone)) {
                if (!$member->IsWhitePhone(['phone'=> $phone])) {   //手机白名单
                    $rest = $redis->exists("string:phone:{$phone}");
                    if ($rest) {
                        throw new CsException("This Phone has been registered already.", 400);
                    }
                }
            }
            // 验证地址ID存在
            $tempId  = 0;
            foreach ($areaId as $value) {
                if ($value < 1) break;
                $tempId = $value;
            }
            $areaId = $tempId;
            $country = $addressList = '';
            if ($areaId > 0) {
                $addressList = $redis->hGet('hash:area:'.$areaId, 'parentList');
                if (empty($addressList)) {
                    $addressList = '';
                } else {
                    $countryId = explode(',', $addressList)[0];
                    $country = getAreaName($countryId);
                } 
            }
                

            $num = $redis->incr('string:member');
            $t = time();
            $ip = get_client_ip();;
            $salt = rand(1000, 9999);
            $pass = passencrypt($password, $salt);
            $redis->multi(\Redis::PIPELINE);
            $source = 'pc';
            $type = 'normal';
            $redis->hmset("hash:member:{$num}", $a = array('id'              => $num,
                                                           'username'        => $username,
                                                           'password'        => $pass,
                                                           'country'         => $country,
                                                           'salt'            => $salt,
                                                           'phone'           => $phone,
                                                           'img'             => '',
                                                           'email'           => $email,
                                                           'bind'            => C('STATUS_BIND')['BIND_EMAIL'],
                                                           'addTime'         => $t,
                                                           'lastLoginIp'     => $ip,
                                                           'lastLoginTime'   => $t,
                                                           'recentLoginTime' => '',
                                                           'status'          => '1', 
                                                           'is_new' => 1, 
                                                           'close' => 1, 
                                                           'isFirstLogin' => '1', 
                                                           'source' => $source, 
                                                           'companyName' => $companyName,
                                                           'firstName' => $firstName,
                                                           'secondName' => $secondName,
                                                           'foxedPhone' => implode('-', $foxedPhone),
                                                           'addressList' => $addressList,
                                                           'addressDetail' => $addressDetail,
                                                           'businessCert' => ''
                                                           ));
            /*注册成功，把username id存到一个单独的集合*/
            $redis->set("member:{$username}", $num);
            /*注册成功,把phone 和Uid存到string:phone集合*/
            //当用户选择国家为中国时,才有手机号码
            if (empty($phone)) {
                $redis->set("string:phone:{$phone}", $num);
            }
            if (empty($email)) {
                $redis->set("string:company:email:{$email}", $num);
            }
            /*Uid状态集合(初始值为正常1)0为删除,2禁用*/
            $redis->SAdd("set:member:status:1", $num);
            /*存user的status(初始值为正常)*/
            $redis->SAdd("set:member:sign:status:1", $num);
            /*用户签约情况记录(初始值未待审核)*/
            // $sign=$redis->sadd("set:member:sign:state:2",$num);
            /*加会员中心*/
            $redis->ZAdd("zset:member:addTime", $t, $num);
            $event = $redis->exec();
            /*注册成功增加搜索索引*/
            $shell = D('shell');
            $shell->index("member:username", $username, $num);
            /*注册成功，把Uid,username存到session*/
            if (!$event) {
                throw new CsException("Registration failure", 400);
            }

            /*注册成功之后注销验证码 session*/
            session('registerEmail', null);

            session('Uid', $num);
            session('memberName', $username);
            session('country', $country);

            $token = $num . $pass . C('LOGIN_NUM')[0];
            session('token', md5($token));

            $authParam = array(
                'id'   => $num,
                'time' => time(),
            );
            $member->buildAutHCode($authParam);

            $cacheKey = D( 'Home/Member' )->getRegisterSourceCacheKey( $source );
            $redis->SAdd( $cacheKey, $num );
            $cacheKey = D( 'Home/Member' )->getRegisterTypeCacheKey( $type );
            $redis->SAdd( $cacheKey, $num );

            // $res['msg'] = 'registration succeeds';
            // $res['code'] = '200';
            // $res['data']['url'] = '/User/index/result';
            $this->ajaxReturn('registration succeeds');
        } else {
            $this->display('register');
        }
    }

    public function registerForm() {
        $key = I('get.key');
        $redis = new Redis();
        $email = $redis->get('string:verifyCode:email:' . $key);
        if (empty($email)) {
            $this->redirect('index/register');
        } 
        session('registerEmail', $email);
        $this->assign('email', $email);
        
        $this->display('register_02');
    }

    /**
     * 检测短信|邮箱验证码
     */
    public function CheckMsg() {
        $data['msgCode'] = I('json.msgCode');
        if (!empty($_POST['country']) && $_POST['country'] == 'CN') {
            /*手机验证码*/
            $code = checkMessage($data['msgCode']);
        } else {
            //邮箱验证码
            $code = checkEmail($data['msgCode']);
        }
        if ($code['code'] != 200) {
            throw new CsException($code['msg'], 400);
        }
        $this->ajaxReturn('success');
    }

    /**
     * 忘记密码第一步
     */
    Public function forgetPasswordStep() {
        if (IS_AJAX) {
            $sessForget = [];       //存SESSION,当第二步的凭证

            $data['email'] = I('json.email');
            $data['captcha'] = I('json.captcha');
            $data['msgCode'] = I('json.msgCode');
            $sessForget = [2, $data['email']];
            //手机和EMAIL不能同时为空
            if (empty($data['email'])) {
                throw new CsException("parameter error", 400);
            }
            //图形验证码
            $verify = new \Think\Verify();
            if (!$verify->check($data['captcha'])) {
                throw new CsException("Incorrect Captcha Code.", 400);
            }
            $code = checkEmail($data['msgCode']);
            if ($code['code'] != 200) {
                throw new CsException($code['msg'], 400);
            }
            $member = D('member');
            if ($member->create($data)) {
                session('forget-validation', $sessForget);
                $this->ajaxReturn('success');
            } else {
                throw new CsException($member->getError(), 400);
            }
        }
        $this->display('forget-password');
    }

    /**
     * 忘记密码第二步
     */
    Public function forgetPasswordStep2() {
        $sess = session('forget-validation');
        if (IS_AJAX) {
            if (empty($sess)) {
                throw new CsException('Not verified', 400);
            }
            $member = D('member');
            $data['password'] = I('json.password');
            $data['repassword'] = I('json.repassword');
            $redis = \Think\Cache::getInstance('Redis');   
            if ($member->create($data)) {
                $uid = $redis->get("string:company:email:{$sess[1]}");
                $salt = $redis->hGet('hash:member:' . $uid, 'salt');
                $pass = passencrypt($data['repassword'], $salt);
                $data = $redis->hMSet("hash:member:{$uid}", ['password' => $pass]);
                if ($data) {
                    session('forget-validation', 2);
                    $this->ajaxReturn('success');
                }
            } else {
                throw new CsException($member->getError(), 400);
            }
        } else {
            if (empty($sess)) {
                $this->redirect('User/index/forgetPasswordStep');
            }
            $this->display('forget-password2');
        }
    }

    /**
     * 忘记密码第三步
     */
    Public function forgetPasswordStep3() {
        $sess = session('forget-validation');
        if (!$sess || $sess != 2) {
            $this->redirect('User/index/forgetPasswordStep');
        }
        session('forget-validation', null);
        $this->display('forget-password3');
    }

    /*登出*/
    Public function logout() {
        unset($_SESSION['memberName']);
        unset($_SESSION['Uid']);
        unset($_SESSION['userHeadImg']);
        cookie('auth_code', null);
        if (IS_AJAX){
        	$this->ajaxReturn('success');
        } else {
        	$this->redirect('/home/index/index');
        }
    }

    /*验证码*/
    Public function verify() {
        ob_end_clean();
        // 实例化Verify对象
        $verify = new \Think\Verify();
        // 配置验证码参数
        $verify->fontSize = 30;     // 验证码字体大小
        $verify->length = 4;        // 验证码位数
        $verify->imageH = 60;       // 验证码高度
        $verify->imageW = 480;       // 验证码宽度
        $verify->useImgBg = false;   // 开启验证码背景
        $verify->useNoise = false;  // 关闭验证码干扰杂点
        $verify->useCurve = false;
        $verify->fontttf = '6.ttf';
        $verify->entry();
    }

    /*获取手机验证码*/
    Public function sendSms() {
        if (IS_AJAX && IS_POST) {
            $data['mobile'] = I('post.phone');
            $data['act'] = I('post.act');
            $data['uv_r'] = I('post.uv_r');
            $ip = get_client_ip();
            $member = D('User/Member');
            if (empty($_SESSION['Send_Code']) || $data['uv_r'] != $_SESSION['Send_Code']) {
                $code['msg'] = 'parameter error';
                $code['code'] = '400';
                $code['data']['error'] = 'parameter error';
                $this->ajaxReturn($code);
            }
            $IpNum = $member->checkIp($ip);
            $num = $member->checkPhone($data['mobile']);

            if ($num < 4 && $IpNum < 100) {
                $code = sendMessage($data['mobile']);
                if ($code['code'] == 200) {
                    $code['data']['uv_r'] = mobileCache();      //每次成功后需要更新,防止并发
                    $this->ajaxReturn($code);
                } else {
                    $this->ajaxReturn($code);
                }
            } else {
                $code['msg'] = 'Three SMS Verification Codes a day only!';
                $code['code'] = '400';
                $code['data']['error'] = 'Three SMS Verification Codes a day only!';
                $this->ajaxReturn($code);
            }
        }
    }

    //注册成功结果页
    public function result() {
        $this->display('register-result');
    }

    public function test() {
        $this->ajaxReturn($_SERVER);
    }


    /**
     * 激活邮箱绑定的账号
     */
    public function certifiedMail(){
        $username = I('get.username');
        $time = I('get.time');
        $sign = I('get.sign');
        $redis = \Think\Cache::getInstance('Redis');
        $userId = $redis->get('member:' . $username);
        $bind = $redis->hget('hash:member:'. $userId, 'bind');
        $email = $redis->hget('hash:member:'. $userId, 'email');
        $validTime = 3600*48;
        if(isset($_SESSION['Uid'])){
            //系统提示邮件URL失效，在登陆状态页引导用户进入会员中心
            if(((time()-$time) > $validTime || $username == '' || $sign == '') && ($bind & C('STATUS_BIND')['BIND_EMAIL'])){
                $content['0'] = "Sorry! the validation link failed.<br>You have logined the keywa website.";
                $content['1'] = "Member Center >>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }
            //系统提示邮箱已验证过，在登陆状态页引导用户进入会员中心
            if ($bind & C('STATUS_BIND')['BIND_EMAIL']) {
                $content['0'] = "Sorry! your registered email had been validated. <br>You have logined the keywa website.";
                $content['1'] = "Member Center >>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }
            //系统提示邮件URL已失效，在登陆状态页引导点击触发邮件来验证
            if(((time()-$time) > $validTime || $username == '' || $sign == '') && !($bind & C('STATUS_BIND')['BIND_EMAIL'])){
                $content['0'] = "Sorry! the validation link failed.<br>You need to validate your registered email.<br>Please sell a confirmation mail to your mailbox,and activate it.";
                $content['1'] = "Send mail again>>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }

            $nowSign = hash_hmac('sha1', $username . $time . $userId, strrev($username)).substr(sha1($username), 0, 24);
            if($nowSign !== $sign){
                $content['0'] = "Sorry! the validation link failed.<br>You have logined the keywa website.";
                $content['1'] = "Member Center >>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }
            //系统恭喜邮件验证成功！在登陆状态页引导用户进入会员中心
            $content['0'] = "Congratulations to you to validate your registered email.<br>You have logined the keywa website.";
            $content['1'] = "Member Center >>";
            $url = U('User/Index/login');
            $newBind = $bind | C('STATUS_BIND')['BIND_EMAIL'];
            //通过上面的认证后，确认用户的邮箱绑定正常。
            $redis->hset('hash:member:' . $userId, 'bind', $newBind);
            $this->assign('content',$content);
            $this->assign('url',$url);
            $this->display('bindTips');
            exit();
        }else{
            $this->assign('notLogin','1');
            //系统提示邮件URL失效，非登陆状态页面引导用户登陆
            if(((time()-$time) > $validTime || $username == '' || $sign == '') && ($bind & C('STATUS_BIND')['BIND_EMAIL'])){
                $content['0'] = "Sorry! the validation link failed.<br>You can sign in the keywa website now.";
                $content['1'] = "Sign in keywa>>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }

            //系统提示邮件已验证过，非登陆状态页面引导用户登陆
            if ($bind & C('STATUS_BIND')['BIND_EMAIL']) {
                $content['0'] = "Sorry! your registered email had been validated. <br>You can sign in the keywa website now.";
                $content['1'] = "Sign in keywa>>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }

            //系统提示邮件URL已失效，在登陆状态页引导点击触发邮件来验证
            if(((time()-$time) > $validTime || $username == '' || $sign == '') && !($bind & C('STATUS_BIND')['BIND_EMAIL'])){
                $content['0'] = "Sorry! the validation link failed.<br>You need to sign in the keywa website,and validate your registered email.";
                $content['1'] = "Sign in keywa>>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }

            //系统恭喜邮件验证成功！在非登陆状态页引导用户登陆
            $nowSign = hash_hmac('sha1', $username . $time . $userId, strrev($username)).substr(sha1($username), 0, 24);
            if($nowSign !== $sign){
                $content['0'] = "Sorry! the validation link failed.<br>You have logined the keywa website.";
                $content['1'] = "Member Center >>";
                $url = U('User/Index/login');
                $this->assign('content',$content);
                $this->assign('url',$url);
                $this->display('bindTips');
                exit();
            }
            $content['0'] = "Congratulations to you to validate your registered email.<br>You can sign in the keywa website now.";
            $content['1'] = "Sign in keywa>>";
            $url = U('User/Index/login');
            $newBind = $bind | C('STATUS_BIND')['BIND_EMAIL'];
            //通过上面的认证后，确认用户的邮箱绑定正常。
            $redis->hset('hash:member:' . $userId, 'bind', $newBind);
            $this->assign('content',$content);
            $this->assign('url',$url);
            $this->display('bindTips');
        }
    }
    


}