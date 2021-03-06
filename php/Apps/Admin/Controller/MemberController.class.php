<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Cache\Driver\Redis;
use Common\Basic\CsException;

//use Think\Controller;
class MemberController extends CommonController {
    //用户列表
    public function index(){
        $this->display();
    }

     /*
     * 企业认证操作历史
     * $id => id
     * */
    public function MemberCompanyDetail(){
        $id=I('get.id','','int');
         if(!empty($id)){
             $member = D('Member');
             $companyHistory=$member->getCompanyHistory($id);
             if(!empty($companyHistory)){
                 $res['total'] = count($companyHistory);
                 $res['rows'] = array_values( $companyHistory );
             }else{
                 $res['total'] = 0;
                 $res['rows'] = array();
             }
             $this->ajaxReturn($res);
         }else{
             $res['msg']='数据异常';
             $res['code'] = '400';
             $res['data']['error']='数据异常';
             $this->ajaxReturn($res);
         }
    }

     /*
     * 企业签约操作历史
     * $id=>id
     * */
    public function MemberSignDetail(){
        $id=I('get.id','','int');
        if(!empty($id)){
            $memberSign = D('MemberSign');
            $sign = $memberSign->getSignDetail($id);
            if(!empty($sign)){
                $res['total']  = count($sign['history']);
                $res['rows'] = array_values($sign['history']);
            }else{
                $res['total'] = 0;
                $res['rows'] = array();
            }
            $this->ajaxReturn($res);
        }else{
            $res['msg']='数据异常';
            $res['code'] = '400';
            $res['data']['error']='数据异常';
            $this->ajaxReturn($res);
        }
    }

    //会员详情
    public function memberDetail(){
        $id=I('json.id', 0, 'intval');
        if ($id < 1) {
            throw new CsException('参数错误', 400);
        }

        $redis = new Redis();
        $memberAll = $redis->hGetAll('hash:member:'.$id);
        if (empty($memberAll)) {
            throw new CsException('商家不存在', 400);
        }
        $result = [];
        $result['id']=$memberAll['id'];
        $result['username']=$memberAll['username'];
        $result['phone']=empty($memberAll['phone'])?'':$memberAll['phone'];
        $result['foxedPhone']=empty($memberAll['foxedPhone'])?'':$memberAll['foxedPhone'];
        $result['email']=empty($memberAll['email'])?'':$memberAll['email'];
        $result['companyName']=isset($memberAll['companyName'])?$memberAll['companyName']:'';
        $result['fullName']= $memberAll['firstName'] . ' ' . $memberAll['secondName'];
        if (isset($memberAll['addressList']) && !empty($memberAll['addressList'])) {
            $addressArr = explode(',', $memberAll['addressList']);
            $addressList = [];
            foreach ($addressArr as $areaId) {
                $addressList[] = getAreaName($areaId);
            }
            $result['addressList'] = implode('-', $addressList);
        } else {
            $result['addressList'] = '';
        }
        $result['addressDetail'] = $memberAll['addressDetail'];
        $result['addTime']=date('Y-m-d H:i:s', $memberAll['addTime']);
        $result['country']=$country[$memberAll['country']] ? $country[$memberAll['country']] : '';
        $result['status']=$memberAll['status'];
        $result['source'] = $memberAll['source'] == 'pc' ? '电脑' : '移动端';

        $this->ajaxReturn(['data'=> $result]);
    }


    //重置密码
    public function restPass(){
        $id=I('json.id');
        if ($id < 1) {
            throw new CsException('参数错误', 400);
        }
        $member = D('Member');
        //修改密码
        $rest=$member->resetPassword($id);
        if (!$rest) {
            throw new CsException('重置失败', 400);
        }
        $this->ajaxReturn(['message'=> '重置成功', 'password'=> C('REST_PASS')]);
    }

    //历史记录
    //type:member->商家历史记录;2company->企业历史记录；3sign->供应商签约记录
    public function historyrList(){
        $id=I('post.id','','int');
        $type=I('get.type','','string');
        if(empty($id))exit;
        if(empty($type))exit;

        if($type=='member' || $type=='company'){
            $member = D('Member');
            $data = $member->getHistoryList($type,$id);
        }elseif($type=='sign'){
            $memberSign = D('MemberSign');
            $data = $memberSign->getHistoryList($id);
        }

        $this->ajaxReturn($data);
    }

    //商家列表
    public function memberList(){
        //搜索条件
        $username=I('json.username','','string');//用户名
        $phone=I('json.phone','','string');//手机
        $startDate=I('json.startDate','','string');//注册开始时间
        $endDate=I('json.endDate','','string');//注册结束时间
        $startDate=!empty($startDate)?strtotime($startDate):'';
        $endDate=!empty($endDate)?strtotime($endDate):'';
        $areaId=I('json.areaId');//地区ID
        $areaId = !empty($areaId) ? $areaId[count($areaId)-1] : '';
        $status = I('json.status',0,'intval');//状态
        $source = I( 'json.source' );//来源 pc wap
        $page = I('json.page',1,'intval');//页数
        $rows=I('json.rows',20,'intval');//条数

        $shell = D('Home/Shell');
        $redis = new Redis();

        $whereArr=array();
        if(!empty($startDate) || !empty($endDate)){
            !empty($endDate) ? $endDate = $endDate + 24*3600-1 : '';  //搜索的结束时间为当前日期的23时59分59秒。
        }
        $whereArr[]='zset:member:addTime';//时间有序集合
        //地区id搜索
        if (!empty($areaId)) {
            $whereArr[]='set:area:member:'.$areaId;
        }
        //状态
        if(empty($status)){
            $statusWhereArr[]='set:member:status:1';
            $statusWhereArr[]='set:member:status:2';
            $randomkey = rand(0,9999);
            $tmpStatusZset='tmp:zset:member:status:'.$randomkey;
            $count = $redis->zUnion($tmpStatusZset,$statusWhereArr);
            if($count)$whereArr[]=$tmpStatusZset;
        }else{
            $whereArr[]='set:member:status:'.$status;
            $randomkey = rand(0,9999);
            $tmpStatusZset='tmp:zset:member:status:'.$randomkey;
        }
        //电话号码搜索
        $tmpUidPhoneSet = '';
        if(!empty($phone)){
            $uid = $redis->get('string:phone:'.$phone);
            if(!empty($uid)){
                $tmpUidPhoneSet = 'tmp:set:uid:phone:'.rand(0,9999);
                $redis->sadd($tmpUidPhoneSet, $uid);
                $whereArr[] = $tmpUidPhoneSet;
            }else{
                $whereArr[]= false;
            }
        }
        //用户名搜索
        $tmpUidUsernameSet = '';
        if(!empty($username)){
            $uid = $redis->get('member:'.$username);
            if(!empty($uid)){
                $tmpUidUsernameSet = 'tmp:set:uid:username:'.rand(0,9999);
                $redis->sadd($tmpUidUsernameSet, $uid);
                $whereArr[]=$tmpUidUsernameSet;
            }else{
                $whereArr[]= false;
            }
        }
        //来源搜索
        if( !empty( $source ) ){
            $whereArr[] = 'set:member:source:'.$source;
        }

        $whereRandKey = rand(0,9999);//随机数
        $tmpSet = 'tmp:zset:member:search:'.$whereRandKey;//交集临时集合
        $redis->zInter($tmpSet, $whereArr);//条件交集

        $offset=($page-1)*$rows;//位置
        $limit = array($offset, $rows);//分页数组

        $list=array();
        $list=array();
        $startDate = !empty($startDate) ? $startDate : '-inf';
        $endDate = !empty($endDate) ? $endDate : '+inf';
        $count = $redis->zCount($tmpSet,$startDate,$endDate);
        $count = !empty($count) ? $count : 0;
        $idArr = $redis->zRevRangeByScore($tmpSet,$endDate,$startDate,array('limit' => $limit));
        if($idArr){
            $country = $redis->hgetAll("hash:country:name");
            foreach($idArr as $key=>$vo){
                $memberAll = $redis->hGetAll('hash:member:'.$vo);
                $list[$key]['id']=$memberAll['id'];
                $list[$key]['username']=$memberAll['username'];
                $list[$key]['phone']=empty($memberAll['phone'])?'':$memberAll['phone'];
                $list[$key]['foxedPhone']=empty($memberAll['foxedPhone'])?'':$memberAll['foxedPhone'];
                $list[$key]['email']=empty($memberAll['email'])?'':$memberAll['email'];
                $list[$key]['companyName']=isset($memberAll['companyName'])?$memberAll['companyName']:'';
                $list[$key]['fullName']= $memberAll['firstName'] . ' ' . $memberAll['secondName'];
                if (isset($memberAll['addressList']) && !empty($memberAll['addressList'])) {
                    $addressArr = explode(',', $memberAll['addressList']);
                    $addressList = [];
                    foreach ($addressArr as $areaId) {
                        $addressList[] = getAreaName($areaId);
                    }
                    $list[$key]['addressList'] = implode('-', $addressList);
                } else {
                    $list[$key]['addressList'] = '';
                }
                $list[$key]['addTime']=date('Y-m-d H:i:s', $memberAll['addTime']);
                $list[$key]['country']=$country[$memberAll['country']] ? $country[$memberAll['country']] : '';
                $list[$key]['status']=$memberAll['status'];
                $list[$key]['statusTip']=$memberAll['status']==1?'Normal':'Invalid';
                $list[$key]['source'] = $memberAll['source'] == 'pc' ? '电脑' : '移动端';
            }
        }
        //删除临时集合
        $redis->del($tmpStatusZset,$tmpUidPhoneSet,$tmpSet,$tmpUidUsernameSet);
        $this->ajaxReturn(['total'=> $count, 'rows'=> $list]);
    }

    //禁用商家/取消禁用商家
    public function memberOperate(){
        $id = I('json.id', 0,  'intval');
        $status = I('json.status', 0,  'intval');
        $reason = I('json.reason');
        if ($id < 1 || empty($reason) || !in_array($status, [1,2])) {
            throw new CsException('参数错误', 400);
        }
        $member = D('Member');
        if ($status == 2) {
            $re = $member->RemoveDeletUser($id);
        } else {
            $re = $member ->RevokeUser($id);
        }
        if (!$re) {
            throw new CsException('操作失败!', 400);
        }
        //启用操作
        $result = $member->memberOperateUpdate($id,$status,$reason);
        if (!$result) {
            throw new CsException('操作失败!', 400);
        }
        $this->ajaxReturn('操作成功!');
    }

    //删除商家
    public function memberOperateDel(){
        $id = I('json.id', 0,  'intval');
        $reason = I('json.reason');
        $status = 0;
        if ($id < 1 || empty($reason)) {
            throw new CsException('参数错误', 400);
        }

        $member = D('Member');
        //修改用户下的商品，求购，供应的状态
        $re = $member ->RemoveDeletUser($id);
        if(!$re){
            throw new CsException('操作失败!', 400);
        }
        //启用操作
        $result = $member->memberOperateDel($id,$status,$reason);
        if(!$result){
            throw new CsException('操作失败!', 400);
        }
        $this->ajaxReturn('操作成功!');
    }

    //用户组页面
    public function companyAuth(){
        $this->display();
    }

    //企业认证列表
    function companyAuthList(){
        //搜索条件
        $map=array();
        $companyName=I('post.companyName','','string');//公司名称
        $certType=I('post.certType','','int');//证照类型
        $state=I('post.state','','int');//企业认证

        $page = I('post.page',1,'int');
        $rows=I('post.rows',20,'int');
        $offset=($page-1)*$rows;

        $shell = D('Home/Shell');
        $redis = new Redis();

        $whereArr=array();
        !empty($companyName)?$whereArr[]=$shell->search('member:companyName',strtolower($companyName),'set'):'';
        !empty($certType)?$whereArr[]='set:member:company:certType:'.$certType:'';

        if($state!==''){
            $whereArr[]='set:company:state:'.$state;
        }else{
            $stateArr=array('set:company:state:0','set:company:state:1','set:company:state:2','set:company:state:3');
            $randomkey = rand(0,9999);
            $tmpStateList='tmp:zset:member:state:'.$randomkey;
            $redis->zUnion($tmpStateList,$stateArr);
            $whereArr[]=$tmpStateList;
        }
//        !empty($state)?$whereArr[]='set:company:state:'.$state:'';


        $statusWhereArr[]='set:member:status:1';
        $statusWhereArr[]='set:member:status:2';
        $randomkey = rand(0,9999);
        $tmpStatusZset='tmp:zset:member:status:'.$randomkey;
        $count = $redis->zUnion($tmpStatusZset,$statusWhereArr);
        if($count)$whereArr[]=$tmpStatusZset;

        $whereRandKey = rand(0,9999);

        $tmpSet='tmp:zset:company:search:'.$whereRandKey;
        $redis->zInter($tmpSet,$whereArr);//多个条件相交

        $info=array();

        $count=$redis->zCount($tmpSet,0,999999);
        $limit = array($offset, $rows);//分页数组
        $sort_option['id']=array(
            'by'=>'hash:member:info:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:info:*->id'),
        );
        $sort_option['companyName']=array(
            'by'=>'hash:member:info:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:info:*->companyName'),
        );
        $sort_option['cert']=array(
            'by'=>'hash:member:info:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:info:*->cert'),
        );
        $sort_option['state']=array(
            'by'=>'hash:member:info:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:info:*->state'),
        );
        $sort_option['addTime']=array(
            'by'=>'hash:member:info:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:*->addTime'),
        );

        //循环取数据
        $tmpKey=array();
        foreach($sort_option as $key=>$vo){
            $tmpKey[$key] = $redis->sort($tmpSet,$vo);
        }

        //删除临时集合
        $redis->del($tmpStateList,$tmpStatusZset,$tmpSet);

        $info=array();
        for($i=0;$i<count($tmpKey['id']);$i++){
            $info[$i]['id']=$tmpKey['id'][$i];
            $info[$i]['companyName']=$tmpKey['companyName'][$i];
            $tmpCertArr=unserialize($tmpKey['cert'][$i]);
            $info[$i]['businessCert']=isset($tmpCertArr['businessCert'])?$tmpCertArr['businessCert']:'';
            $info[$i]['accountCert']=isset($tmpCertArr['accountCert'])?$tmpCertArr['accountCert']:'';
            $info[$i]['codeCert']=isset($tmpCertArr['codeCert'])?$tmpCertArr['codeCert']:'';
            $info[$i]['taxCert']=isset($tmpCertArr['taxCert'])?$tmpCertArr['taxCert']:'';
            $info[$i]['type']=$tmpCertArr['type'];
            $info[$i]['addTime']=$tmpKey['addTime'][$i];
            $tmpStrArr = unserialize($tmpKey['cert'][$i]);
            $info[$i]['state']=$tmpKey['state'][$i];
            $historyId = $redis->hLen('hash:company:operation:history:'.$info[$i]['id']);
            $tmpHistory = $redis->hGet('hash:company:operation:history:'.$info[$i]['id'],$historyId);
            $tmpHistoryArr = unserialize($tmpHistory);
            $info[$i]['opera']=$tmpHistoryArr['opera'];
            $info[$i]['reason']=isset($tmpHistoryArr['reason'])?$tmpHistoryArr['reason']:'';
        }

        if(!empty($info)){
            $data['total']=$count;
            $data['rows']=$info;
        }else{
            $data['total']=0;
            $data['rows']=0;
        }
//        print_r($data);exit;
        $this->ajaxReturn($data);
    }

    public function companyVerify(){
        if(!IS_POST) exit;
        $id = I('post.id','','int');
        $prevState = I('post.prevState','','int');
        $state = I('post.state','','int');
        if(empty($id))exit;
        if(!isset($prevState))exit;
        if(!isset($state))exit;

        $redis = new redis();
        $companyName = $redis->hGet('hash:member:info:'.$id,'companyName');
        $companyType = $redis->get('string:company:'.$companyName);
//        echo $companyName;exit;
        /*   if($companyType && $state==1){
                $return['msg']=$companyName.'已认证过';
                $return['code']=400;
                $this->ajaxReturn($return);
            }*/

        $reason = I('post.reason','','string');

        $member = D('Member');
        //启用操作
        $result = $member->companyVerifyUpdate($id,$prevState,$state,$reason);
        if(!$result){
            $return['msg']='执行失败!';
            $return['code']=400;
        }else{
            $return['msg']='执行成功!';
            $return['code']=200;
        }
        $this->ajaxReturn($return);
    }

    public function memberSign(){
        $this->display();
    }

    public function signAdd(){
        if(!IS_POST) exit;
        $memberSign = D('MemberSign');
        $data = I('post.');
        $data['addTime']=time();


        if(empty($data['companyName'])){
            $return['msg']='公司名称不能为空';
            $return['code']=400;
            $this->ajaxReturn($return);
        }elseif($data['uid']=$memberSign->companyGetUid($data['companyName'])){
            unset($data['companyName']);
        }else{
            $return['msg']='公司名称错误';
            $return['code']=400;
            $this->ajaxReturn($return);
        }


        if (!$data=$memberSign->create($data)){
            $return['msg']=$memberSign->getError();
            $return['code']=400;
        }else{
            $data['contractTime']=strtotime($data['contractTime']);
            $data['expireTime']=strtotime($data['expireTime']);
            $addResult = $memberSign->memberSignAdd($data);
            if(!$addResult){
                $return['msg']='添加失败!';
                $return['code']=400;
            }else{
                $return['msg']='添加成功!';
                $return['code']=200;
            }
        }

        $this->ajaxReturn($return);
    }

    //修改签约
    public function signSave(){
        if(!IS_POST) exit;

        $memberSign = D('MemberSign');
        $data = I('post.');
        $data['addTime']=time();

        if(empty($data['companyName'])){
            $return['msg']='公司名称不能为空';
            $return['code']=400;
            $this->ajaxReturn($return);
        }elseif($data['uid']=$memberSign->companyGetUid($data['companyName'])){
            unset($data['companyName']);
        }else{
            $return['msg']='公司名称错误';
            $return['code']=400;
            $this->ajaxReturn($return);
        }

        if (!$data=$memberSign->create($data)){
            $return['msg']=$memberSign->getError();
            $return['code']=400;
        }else{
            $data['contractTime']=strtotime($data['contractTime']);
            $data['expireTime']=strtotime($data['expireTime']);
            $addResult = $memberSign->memberSignSave($data);
            if(!$addResult){
                $return['msg']='修改失败!';
                $return['code']=400;
            }else{
                $return['msg']='修改成功!';
                $return['code']=200;
            }
        }

        $this->ajaxReturn($return);
    }

    //签约认证列表
    function memberSignList(){
        //搜索条件
        $map=array();
        $code=I('post.code','','string');//合同编号
        $companyName=I('post.companyName','','string');//公司名称
//        $startCooperation=I('post.startCooperation','','string');
//        $endCooperation=I('post.endCooperation','','string');
//        $startCooperation=!empty($startCooperation)?strtotime($startCooperation):'';
//        $endCooperation=!empty($endCooperation)?strtotime($endCooperation):'';
//        $provinceId=I('post.provinceId','','int');//省份
//        $cityId=I('post.cityId','','int');//市份
//        $districtId=I('post.districtId','','int');//区份
//        $startContractTime =I('post.startContractTime','','string');
//        $endContractTime=I('post.endContractTime','','string');
//        $startContractTime=!empty($startContractTime)?strtotime($startContractTime):'';
//        $endContractTime=!empty($endContractTime)?strtotime($endContractTime):'';
//        $startExpireTime =I('post.startExpireTime','','string');
//        $endExpireTime=I('post.endExpireTime','','string');
//        $startExpireTime=!empty($startExpireTime)?strtotime($startExpireTime):'';
//        $endExpireTime=!empty($endExpireTime)?strtotime($endExpireTime):'';
        $state=I('post.state','','int');//签约认证

        $page = I('post.page',1,'int');
        $rows=I('post.rows',20,'int');
        $offset=($page-1)*$rows;

        $shell = D('Home/Shell');
        $redis = new Redis();

        $whereArr=array();

//        if(!empty($startCooperation) || !empty($endCooperation)){
//            $whereArr[]='zset:member:addTime';
//        }

        !empty($companyName)?$whereArr[]=$shell->search('member:companyName',$companyName,'set'):'';
        !empty($code)?$whereArr[]='set:member:sign:code:'.$code:'';
        if($state!==''){
            $whereArr[]='set:member:sign:state:'.$state;
        }else{
            $stateArr=array('set:member:sign:state:0','set:member:sign:state:1','set:member:sign:state:2','set:member:sign:state:3');
            $randomkey = rand(0,9999);
            $tmpStateList='tmp:zset:member:state:'.$randomkey;
            $redis->zUnion($tmpStateList,$stateArr);
            $whereArr[]=$tmpStateList;
        }


        $statusWhereArr[]='set:member:sign:status:1';
        $statusWhereArr[]='set:member:sign:status:2';
        $randomkey = rand(0,9999);
        $tmpStatusZset='tmp:zset:member:sign:status:'.$randomkey;
        $count = $redis->zUnion($tmpStatusZset,$statusWhereArr);
        if($count)$whereArr[]=$tmpStatusZset;

        $whereRandKey = rand(0,9999);

        $tmpSet='tmp:zset:memberSign:search:'.$whereRandKey;

        $redis->zInter($tmpSet,$whereArr);//多个条件相交
        $count=$redis->zCount($tmpSet,0,999999);
        $limit = array($offset, $rows);//分页数组
        $sort_option['id']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->id'),
        );
        $sort_option['code']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->code'),
        );
        $sort_option['companyName']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:info:*->companyName'),
        );
        $sort_option['other']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:info:*->other'),
        );
        $sort_option['cooperation']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->cooperation'),
        );
        $sort_option['contractTime']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->contractTime'),
        );
        $sort_option['content']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->content'),
        );
        $sort_option['expireTime']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->expireTime'),
        );
        $sort_option['signatory']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->signatory'),
        );
        $sort_option['state']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->state'),
        );
        $sort_option['attachment']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->attachment'),
        );
        $sort_option['state']=array(
            'by'=>'hash:member:sign:*->id',
            'limit' => $limit,
            'sort'=>'desc',
            'get'=>array('hash:member:sign:*->state'),
        );

        //循环取数据
        $tmpKey=array();
        foreach($sort_option as $key=>$vo){
            $tmpKey[$key] = $redis->sort($tmpSet,$vo);
        }
//        print_r($tmpKey);exit;
        $redis->del($tmpStateList,$tmpStatusZset,$tmpSet);//删除临时集合
//        print_r($tmpKey);exit;
        $info=array();
        for($i=0;$i<count($tmpKey['id']);$i++){
            $info[$i]['id']=$tmpKey['id'][$i];
            $info[$i]['code']=$tmpKey['code'][$i];
            $info[$i]['companyName']=$tmpKey['companyName'][$i];
            //获取省市区start
            $tmpMemberArr = unserialize($tmpKey['other'][$i]);
            $info[$i]['area']=$tmpMemberArr['area_s']?getAreaName($tmpMemberArr['area_s']):'';
            $info[$i]['area'].=$info[$i]['area']?'-'.getAreaName($tmpMemberArr['area_c']):'';
            $info[$i]['area'].=$info[$i]['area']?'-'.getAreaName($tmpMemberArr['area_x']):'';
            //end
            $info[$i]['cooperation']=$tmpKey['cooperation'][$i];
            $info[$i]['contractTime']=$tmpKey['contractTime'][$i];
            $info[$i]['expireTime']=$tmpKey['expireTime'][$i];
            $info[$i]['signatory']=$tmpKey['signatory'][$i];
            $info[$i]['addTime']=$tmpKey['addTime'][$i];
            $info[$i]['content']=$tmpKey['content'][$i];
//            if($info[$i]['expireTime']<time())$info[$i]['state']=5;
//            else $info[$i]['state']=$tmpKey['state'][$i];
            $info[$i]['state']=$tmpKey['state'][$i];

            if(!empty($tmpKey['attachment'][$i])){
                $tmpAttachmentArr=unserialize($tmpKey['attachment'][$i]);
                $attachment = implode(',',$tmpAttachmentArr);
            }else{
                $attachment='';
            }
            $info[$i]['attachment']=$attachment;
        }
     //或者最后一次的操作历史
        foreach($info as $k=>$v){
            $history=$redis->hgetAll("hash:member:sign:operation:history:{$v['id']}");
            $hit = array();
            foreach($history as $k1=>$v1){
                $hit[$k1]=unserialize($history[$k1]);
            }
            ksort($hit);
           $info[$k]['reason']=end($hit)['reason'];
        }

        if(!empty($info)){
            $data['total']=$count;
            $data['rows']=$info;
        }else{
            $data['total']=0;
            $data['rows']=0;
        }
//        print_r($data);exit;
        $this->ajaxReturn($data);
    }

    //签约操作
    public function signVerify(){
        if(!IS_POST) exit;
        $id = I('post.id','','int');
        $state = I('post.state','','int');
        $prevState = I('post.prevState','','int');
        if(empty($id))exit;
        if(!isset($state))exit;
        if(!isset($state))exit;

        $reason = I('post.reason','','string');
        $member = D('MemberSign');
        //启用操作
        $result = $member->signVerifyUpdate($id,$prevState,$state,$reason);
        if(!$result){
            $return['msg']='执行失败!';
            $return['code']=400;
        }else{
            $return['msg']='执行成功!';
            $return['code']=200;
        }
        $this->ajaxReturn($return);
    }

    public function getChildArea($id){
//        if(!IS_POST) exit;
        $id = I('get.id','','int');
        if(!isset($id))exit;
        $area=D('Area');
        $info = $area->getChildArea($id);
        $this->ajaxReturn($info);
    }

    //商家列表--数据导出
    public function expMember(){
        $companyState = I( 'get.companyState' );//来源
        $m_status = I( 'get.status' );//类型

        $redis = \Think\Cache::getInstance('Redis');
        /*set:member:status:1,2并集*/
        $status=array(
            '1'=>'正常',
            '2'=>'禁用'
        );
        $state=array(
            '0'=>'审核不通过',
            '1'=>'审核通过',
            '2'=>'待审核',
            '3'=>'已撤销',
        );
        $signs=array(
            '0'=>'审核不通过',
            '1'=>'审核通过',
            '2'=>'待审核',
            '3'=>'已撤销',
            '4'=>'已过期'
        );
        $tid=uniqid();
         $count=$redis->SUNIONSTORE("tmp:member:".$tid,'set:member:status:1','set:member:status:2');
         $whereArr[] = 'tmp:member:'.$tid;
        if( !empty( $companyState ) ){
            $whereArr[] = 'set:company:state:'.$companyState;
        }
        if( !empty( $m_status ) ){
            $whereArr[] = 'set:member:status:'.$m_status;
        }
        $redis->zInter("tmp:member:".$tid,$whereArr);//条件交集
        if($count && $redis->expire("tmp:member:".$tid,60)){
            $member_options=array(
                'get'=>array(
                    'hash:member:*->id','hash:member:*->username','hash:member:*->phone',
                    'hash:member:*->email','hash:member:*->addTime','hash:member:*->status'
                )
            );
            $memberArr=$redis->sort("tmp:member:".$tid,$member_options);
            if($memberArr){
                $num=0;
                foreach($memberArr as $k=>$v){
                     if($k%6==0){
                         $member[$num]['id']=$v;
                     }elseif($k%6==1){
                         $member[$num]['username']=$v;
                     }elseif($k%6==2){
                         $member[$num]['phone']=empty($v)?'未填写':' '.$v;
                     }elseif($k%6==3){
                         $member[$num]['email']=empty($v)?'未填写':' '.$v;
                     }elseif($k%6==4){
                         $member[$num]['addTime']=date('Y-m-d H:i:s',$v);
                     }elseif($k%6==5){
                         $member[$num]['status']=$status[$v];
                         $num++;
                     }
                }
            }

            if(!empty($_GET['id'])){
                $id=explode(',', I('id'));
            }
            $arr=array();
            if(!empty($id)){
                foreach ($member as $k){
                    if(in_array($k['id'], $id)){
                        $arr[]=$k;
                    }
                }
                $member=$arr;
            }

            //拼接企业信息
            foreach($member as $k1=>$v1){
                $infoArr=$redis->hmget("hash:member:info:{$v1['id']}",array('companyName','contact','other','state'));
                $member[$k1]['companyName']=empty($infoArr['companyName'])?'未填写':$infoArr['companyName'];
                $member[$k1]['contact']=empty($infoArr['contact'])?'未填写':$infoArr['contact'];
                $member[$k1]['state']=$state[$infoArr['state']];
                $other=unserialize($infoArr['other']);
                $area_s=$redis->hget("hash:area:{$other['area_s']}",'title');
                $area_c=$redis->hget("hash:area:{$other['area_c']}",'title');
                if(isset($other['area_x'])){
                    $area_x=$redis->hget("hash:area:{$other['area_x']}",'title');
                }else{
                    $area_x='';
                }
                $member[$k1]['area']=$area_s.'-'.$area_c.'-'.$area_x;
                $member[$k1]['area'] = trim($member[$k1]['area'],'-');
                $member[$k1]['area'] = empty($member[$k1]['area'])?'未填写':$member[$k1]['area'];
                //签约
                $sign=$redis->hget("hash:member:sign:{$v1['id']}",'state');
               if($sign){
                   $member[$k1]['sign']=$signs[$sign['state']];
               }else{
                   $member[$k1]['sign']='未添加';
               }

                if($redis->exists("hash:member:info:{$v1['id']}")){
                    $member[$k1]['complete']='是';
                }else{
                    $member[$k1]['complete']='否';
                }
            }


        }
        $xlsName  = "商家认证列表";
        $xlsCell  = array(
            array('username','用户名'),
            array('email','邮箱'),
            array('phone','手机'),
            array('companyName','公司名称'),
            array('contact','指定联系人'),
            array('area','所在地区'),
            array('complete','完善资料'),
            array('state','企业认证'),
            array('sign','签约为联营供应商'),
            array('addTime','注册时间'),
            array('status','状态')

        );
        $xlsData = $member;//读取列表
        exportExcel($xlsName,$xlsCell,$xlsData);
    }  


        //商家认证--数据导出
    public function expAuth()
    {
        /*先把所有的认证状态并集，然后与正常状态的用户交集*/
        $user = D('User');

        $_certType = C('CERT_TYPE');
        $AuthState = C('AUTH_STATE');
        //搜索条件
        $map = array();
        $companyName = I('get.companyName', '', 'string');//公司名称
        $certType = I('get.certType', '', 'int');//证照类型
        $state = I('get.state', '', 'int');//企业认证

        $shell = D('Home/Shell');
        $redis = new Redis();

        $whereArr = array();
        !empty($companyName) ? $whereArr[] = $shell->search('member:companyName', $companyName, 'set') : '';

        !empty($certType) ? $whereArr[] = 'set:member:company:certType:' . $certType : '';

        if ($state !== '') {
            $whereArr[] = 'set:company:state:' . $state;
        } else {
            $stateArr = array('set:company:state:0', 'set:company:state:1', 'set:company:state:2', 'set:company:state:3');
            $randomkey = rand(0, 9999);
            $tmpStateList = 'tmp:zset:member:state:' . $randomkey;
            $redis->zUnion($tmpStateList, $stateArr);
            $whereArr[] = $tmpStateList;
        }
//        !empty($state)?$whereArr[]='set:company:state:'.$state:'';

        $statusWhereArr[] = 'set:member:status:1';
        $statusWhereArr[] = 'set:member:status:2';
        $randomkey = rand(0, 9999);
        $tmpStatusZset = 'tmp:zset:member:status:' . $randomkey;
        $count = $redis->zUnion($tmpStatusZset, $statusWhereArr);
        if ($count) $whereArr[] = $tmpStatusZset;

        $whereRandKey = rand(0, 9999);

        $tmpSet = 'tmp:zset:company:search:' . $whereRandKey;

        $redis->zInter($tmpSet, $whereArr);//多个条件相交

        $count = $redis->zCount($tmpSet, 0, 999999);
        $sort_option['id'] = array(
            'by' => 'hash:member:info:*->id',
            'sort' => 'desc',
            'get' => array('hash:member:info:*->id'),
        );
        $sort_option['companyName'] = array(
            'by' => 'hash:member:info:*->id',
            'sort' => 'desc',
            'get' => array('hash:member:info:*->companyName'),
        );
        $sort_option['cert'] = array(
            'by' => 'hash:member:info:*->id',
            'sort' => 'desc',
            'get' => array('hash:member:info:*->cert'),
        );
        $sort_option['state'] = array(
            'by' => 'hash:member:info:*->id',
            'sort' => 'desc',
            'get' => array('hash:member:info:*->state'),
        );
        $sort_option['addTime'] = array(
            'by' => 'hash:member:info:*->id',
            'sort' => 'desc',
            'get' => array('hash:member:*->addTime'),
        );

        //循环取数据
        $tmpKey = array();
        foreach ($sort_option as $key => $vo) {
            $tmpKey[$key] = $redis->sort($tmpSet, $vo);
        }
        //删除临时集合
        $redis->del($tmpStateList, $tmpStatusZset, $tmpSet);

        $info = array();
        for($i=0;$i<count($tmpKey['id']);$i++){
            $info[$i]['id']=$tmpKey['id'][$i];
            $info[$i]['companyName']=$tmpKey['companyName'][$i];
            $tmpCertArr=unserialize($tmpKey['cert'][$i]);
            $info[$i]['businessCert']=$tmpCertArr['businessCert'];
            $info[$i]['accountCert']=$tmpCertArr['accountCert'];
            $info[$i]['codeCert']=$tmpCertArr['codeCert'];
            $info[$i]['taxCert']=$tmpCertArr['taxCert'];
            $info[$i]['type']=$tmpCertArr['type'];
            $info[$i]['addTime']=$tmpKey['addTime'][$i];
            $tmpStrArr = unserialize($tmpKey['cert'][$i]);
            $info[$i]['state']=$tmpKey['state'][$i];
            $historyId = $redis->hLen('hash:company:operation:history:'.$info[$i]['id']);
            $tmpHistory = $redis->hGet('hash:company:operation:history:'.$info[$i]['id'],$historyId);
            $tmpHistoryArr = unserialize($tmpHistory);
            $info[$i]['opera']=$tmpHistoryArr['opera'];
            $info[$i]['reason']=$tmpHistoryArr['reason'];
        }

        if(!empty($_GET['id'])){
            $id=explode(',', I('id'));
        }
        $arr=array();
        if(!empty($id)){
            foreach ($info as $k){
                if(in_array($k['id'], $id)){
                    $arr[]=$k;
                }
            }
            $info=$arr;
        }

        foreach ($info as $k1 => $v1) {
            $info[$k1]['type'] = $_certType[$info[$k1]['type']];
            $info[$k1]['state'] = $AuthState[$info[$k1]['state']];
            $info[$k1]['businessCert'] = empty($info[$k1]['businessCert']) ? '×' : '√';//营业执照
            $info[$k1]['codeCert'] = empty($info[$k1]['codeCert']) ? '×' : '√';//组织机构代码证
            $info[$k1]['taxCert'] = empty($info[$k1]['taxCert']) ? '×' : '√';//税务登记证
            $info[$k1]['accountCert'] = empty($info[$k1]['accountCert']) ? '×' : '√';//开户许可证
            //添加时间
            $addTime = $redis->hget("hash:member:{$v1['id']}", 'addTime');
            $info[$k1]['addTime'] = empty(date('Y-m-d H:i:s', $addTime))?'':date('Y-m-d H:i:s', $addTime);
            $info[$k1]['updateTime'] = empty(date('Y-m-d H:i:s', $addTime))?'':date('Y-m-d H:i:s', $addTime);
            $history = $redis->hgetAll("hash:company:operation:history:{$v1['id']}");
            ksort($history);
            foreach ($history as $k2 => $v2) {
                $hits[$k2] = unserialize($v2);
                foreach ($hits as $k3 => $v3) {
                    if ($v3['state'] == 2) {
                        unset($hits[$k3]);
                    }
                }
            }

            $first = array_slice($hits,0,1)[0];
            $last = end($hits);
            $info[$k1]['firstTime'] = empty($first['addTime']) ? '' : date('Y-m-d H:i:s', $first['addTime']);
            $firstName = $user->field('username,realname')->find($first['oid']);
            $info[$k1]['firstName'] = $firstName['realname'];
            $info[$k1]['lastTime'] = empty($last['addTime']) ? '' : date('Y-m-d H:i:s', $last['addTime']);
            $lastName = $user->field('username,realname')->find($last['oid']);
            $info[$k1]['lastName'] = $lastName['realname'];
             }

            $xlsName  = "企业认证列表";
            $xlsCell  = array(
                array('companyName','公司名称'),
                array('type','证件类型'),
                array('businessCert','营业执照'),
                array('codeCert','组织机构代码证'),
                array('taxCert','税务登记证'),
                array('accountCert','开户许可证'),
                array('state','状态'),
                array('addTime','创建时间'),
                array('updateTime','最新修改时间'),
                array('firstTime','初始审核时间'),
                array('firstName','初始审核人'),
                array('lastTime','最新审核时间'),
                array('lastName','最新审核人')
            );
            $xlsData = $info;//读取列表
            exportExcel($xlsName,$xlsCell,$xlsData);
    }


    //录入签约--数据导出
    public function expSign(){
        /*交集得到有效的用户id*/
        $redis = \Think\Cache::getInstance('Redis');
        $user=D('User');
        //签约状态
        $signState=C('SIGN_STATE');
        //合作年度
        $coopDate=C('COOPERATION_DATA');
        $tid=uniqid();
        $sunion=$redis->SUNIONSTORE("tmp:member:list:".$tid,'set:member:sign:state:0','set:member:sign:state:1','set:member:sign:state:2','set:member:sign:state:3');
        $count=$redis->SINTERSTORE("set:tmp:sign:{$tid}",'set:member:sign:status:1',"tmp:member:list:".$tid);
        if($count && $redis->expire("set:tmp:sign:{$tid}",60) && $sunion && $redis->expire("tmp:member:list:".$tid,60)){
            $member_sign=array(
                'get'=>array(
                    'hash:member:sign:*->id', 'hash:member:sign:*->code', 'hash:member:sign:*->cooperation',
                    'hash:member:sign:*->contractTime','hash:member:sign:*->expireTime', 'hash:member:sign:*->addTime',
                    'hash:member:sign:*->state'
                )
            );
            $signArr=$redis->sort("set:tmp:sign:{$tid}",$member_sign);
            $num=0;
            if($signArr){
                foreach($signArr as $k1=>$v1){
                    if($k1%7==0){
                        $sign[$num]['id']=$v1;
                    }elseif($k1%7==1){
                        $sign[$num]['code']=$v1;
                    }elseif($k1%7==2){
                        $sign[$num]['cooperation']=$coopDate[$v1]['title'];
                    }elseif($k1%7==3){
                        $sign[$num]['contractTime']=date('Y-m-d H:i:s',$v1);
                    }elseif($k1%7==4){
                        $sign[$num]['expireTime']=date('Y-m-d H:i:s',$v1);
                    }elseif($k1%7==5){
                        $sign[$num]['addTime']=date('Y-m-d H:i:s',$v1);
                    }elseif($k1%7==6){
                        $sign[$num]['state']=$signState[$v1];
                        $num++;
                    }
                }
            }
            foreach($sign as $k2=>$v2){
                //公司名
                $member=$redis->hmget("hash:member:info:{$v2['id']}",array('companyName','other'));
                $sign[$k2]['companyName']=$member['companyName'];
                $other=unserialize($member['other']);
                $area_s=$redis->hget("hash:area:{$other['area_s']}",'title');
                $area_c=$redis->hget("hash:area:{$other['area_c']}",'title');
                $area_x=$redis->hget("hash:area:{$other['area_x']}",'title');
                $sign[$k2]['area']=$area_s.'-'.$area_c.'-'.$area_x;
                //获取所有的操作记录,待审核的为初始添加
                $history=$redis->hgetAll("hash:member:sign:operation:history:{$v2['id']}");
                ksort($history);
                foreach($history as $k3=>$v3){
                    $InSignHits[$k3]=unserialize($v3);
                    //第一条为初始添加，最后一条为最新修改
                   foreach($InSignHits as $k4=>$v4){
                        if($v4['state']){
                            unset($InSignHits[$k4]);
                            ksort($InSignHits);
                        }
                    }
                    $SignHits[$k3]=unserialize($v3);
                    //第一条为初始添加，最后一条为最新修改
                    foreach($SignHits as $k5=>$v5){
                        if($v5['state']==null){
                            unset($SignHits[$k5]);
                            array_keys($SignHits);
                        }
                    }
                }
               /* echo '<pre>';
                var_dump($SignHits);*/

                //初始录入
                $start=current($InSignHits);
                //最新录入
                $end=end($InSignHits);
                $sign[$k2]['startTime']=empty($start['addTime'])?'':date('Y-m-d H:i:s',$start['addTime']);
                $startUserName=$user->field('username,realname')->find($start['oid']);
                $sign[$k2]['startName']=$startUserName['realname'];
                $sign[$k2]['endTime']=empty($end['addTime'])?'':date('Y-m-d H:i:s',$end['addTime']);
                $endUserName=$user->field('username,realname')->find($end['oid']);
                $sign[$k2]['endName']=$endUserName['realname'];

                //最初审核
                $first=reset($SignHits);
                $last=end($SignHits);
                $sign[$k2]['firstTime']=empty($first['addTime'])?'':date('Y-m-d H:i:s',$first['addTime']);
                $firstUserName=$user->field('username,realname')->find($first['oid']);
                $sign[$k2]['firstName']=$firstUserName['realname'];
                $sign[$k2]['lastTime']=empty($last['addTime'])?'':date('Y-m-d H:i:s',$last['addTime']);
                $lastUserName=$user->field('username,realname')->find($last['oid']);
                $sign[$k2]['lastName']=$lastUserName['realname'];
            }

            /*echo '<pre>';
            var_dump($sign);exit;*/
            $xlsName  = "录入签约";
            $xlsCell  = array(
                array('code','合同编号'),
                array('companyName','公司名称'),
                array('area','所在地区'),
                array('cooperation','合作年度'),
                array('contractTime','合同签约时间'),
                array('expireTime','合同到期时间'),
                array('state','状态'),
                array('startTime','初始录入时间'),
                array('startName','初始录入人'),
                array('endTime','最新修改时间'),
                array('endName','最新修改人'),
                array('firstTime','初始审核时间'),
                array('firstName','初始审核人'),
                array('lastTime','最新审核时间'),
                array('lastName','最新审核人')
            );

            $xlsData = $sign;//读取列表
            exportExcel($xlsName,$xlsCell,$xlsData);
        }

    }

    //录入审核--数据导出
    public function expSignAuth(){
        /*交集得到有效的用户id*/
        $redis = \Think\Cache::getInstance('Redis');
        $user=D('User');
        //签约状态
        $signState=C('SIGN_STATE');
        //合作年度
        $coopDate=C('COOPERATION_DATA');
        $tid=uniqid();
        $sunion=$redis->SUNIONSTORE("tmp:member:list:".$tid,'set:member:sign:state:0','set:member:sign:state:1','set:member:sign:state:2','set:member:sign:state:3');
        $count=$redis->SINTERSTORE("set:tmp:sign:{$tid}",'set:member:sign:status:1',"tmp:member:list:".$tid);
        if($count && $redis->expire("set:tmp:sign:{$tid}",60) && $sunion && $redis->expire("tmp:member:list:".$tid,60)){
            $member_sign=array(
                'get'=>array(
                    'hash:member:sign:*->id', 'hash:member:sign:*->code', 'hash:member:sign:*->cooperation',
                    'hash:member:sign:*->contractTime','hash:member:sign:*->expireTime', 'hash:member:sign:*->addTime',
                    'hash:member:sign:*->state'
                )
            );
            $signArr=$redis->sort("set:tmp:sign:{$tid}",$member_sign);
            $num=0;
            if($signArr){
                foreach($signArr as $k1=>$v1){
                    if($k1%7==0){
                        $sign[$num]['id']=$v1;
                    }elseif($k1%7==1){
                        $sign[$num]['code']=$v1;
                    }elseif($k1%7==2){
                        $sign[$num]['cooperation']=$coopDate[$v1]['title'];
                    }elseif($k1%7==3){
                        $sign[$num]['contractTime']=date('Y-m-d H:i:s',$v1);
                    }elseif($k1%7==4){
                        $sign[$num]['expireTime']=date('Y-m-d H:i:s',$v1);
                    }elseif($k1%7==5){
                        $sign[$num]['addTime']=date('Y-m-d H:i:s',$v1);
                    }elseif($k1%7==6){
                        $sign[$num]['state']=$signState[$v1];
                        $num++;
                    }
                }
            }
            foreach($sign as $k2=>$v2){
                //公司名
                $member=$redis->hmget("hash:member:info:{$v2['id']}",array('companyName','other'));
                $sign[$k2]['companyName']=$member['companyName'];
                $other=unserialize($member['other']);
                $area_s=$redis->hget("hash:area:{$other['area_s']}",'title');
                $area_c=$redis->hget("hash:area:{$other['area_c']}",'title');
                $area_x=$redis->hget("hash:area:{$other['area_x']}",'title');
                $sign[$k2]['area']=$area_s.'-'.$area_c.'-'.$area_x;
                //获取所有的操作记录,待审核的为初始添加
                $history=$redis->hgetAll("hash:member:sign:operation:history:{$v2['id']}");
                ksort($history);
                foreach($history as $k3=>$v3){
                    $InSignHits[$k3]=unserialize($v3);
                    //第一条为初始添加，最后一条为最新修改
                    foreach($InSignHits as $k4=>$v4){
                        if($v4['state']){
                            unset($InSignHits[$k4]);
                            ksort($InSignHits);
                        }
                    }
                    $SignHits[$k3]=unserialize($v3);
                    //第一条为初始添加，最后一条为最新修改
                    foreach($SignHits as $k5=>$v5){
                        if($v5['state']==null){
                            unset($SignHits[$k5]);
                            array_keys($SignHits);
                        }
                    }
                }
                /* echo '<pre>';
                 var_dump($SignHits);*/

                //初始录入
                $start=current($InSignHits);
                //最新录入
                $end=end($InSignHits);
                $sign[$k2]['startTime']=empty($start['addTime'])?'':date('Y-m-d H:i:s',$start['addTime']);
                $startUserName=$user->field('username,realname')->find($start['oid']);
                $sign[$k2]['startName']=$startUserName['realname'];
                $sign[$k2]['endTime']=empty($end['addTime'])?'':date('Y-m-d H:i:s',$end['addTime']);
                $endUserName=$user->field('username,realname')->find($end['oid']);
                $sign[$k2]['endName']=$endUserName['realname'];

                //最初审核
                $first=reset($SignHits);
                $last=end($SignHits);
                $sign[$k2]['firstTime']=empty($first['addTime'])?'':date('Y-m-d H:i:s',$first['addTime']);
                $firstUserName=$user->field('username,realname')->find($first['oid']);
                $sign[$k2]['firstName']=$firstUserName['realname'];
                $sign[$k2]['lastTime']=empty($last['addTime'])?'':date('Y-m-d H:i:s',$last['addTime']);
                $lastUserName=$user->field('username,realname')->find($last['oid']);
                $sign[$k2]['lastName']=$lastUserName['realname'];
            }

            /*echo '<pre>';
            var_dump($sign);exit;*/
            $xlsName  = "录入审核";
            $xlsCell  = array(
                array('code','合同编号'),
                array('companyName','公司名称'),
                array('area','所在地区'),
                array('cooperation','合作年度'),
                array('contractTime','合同签约时间'),
                array('expireTime','合同到期时间'),
                array('state','状态'),
                array('startTime','初始录入时间'),
                array('startName','初始录入人'),
                array('endTime','最新修改时间'),
                array('endName','最新修改人'),
                array('firstTime','初始审核时间'),
                array('firstName','初始审核人'),
                array('lastTime','最新审核时间'),
                array('lastName','最新审核人')
            );

            $xlsData = $sign;//读取列表
            exportExcel($xlsName,$xlsCell,$xlsData);
        }

    }
}