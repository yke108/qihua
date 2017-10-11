<?php 
namespace Admin\Controller;
use Think\Controller;
use Common\Basic\CsException;

/**
 * 求购管理控制器
 */
class BuyOfferController extends CommonController{


	public function lists(){
        $model=D('Admin/BuyOffer');
         $ret = $model->BuyOfferBaseData();
		$type=$ret['FIND_GOODS_TYPE'];
        $state=$ret['FIND_GOODS_STATE'];
        $this->assign('state',$state);
		$this->assign('type',$type);
		$this->display('list');
	}
	
	//求购列表
	public function findGoods(){
        $shell=D('Home/Shell');
        $model=D('Admin/BuyOffer');
        $param=I('json.');

        /*接收分页条件*/
        $page = I('json.page',1,'int');
        $rows=I('json.rows',20,'int');
        $offset=($page-1)*$rows;

        $keys = array();
        //信息编号
        if( !empty( $param['number'] ) ){
            $keys[] = $model->GetNumber( $param['number'] );
        }

        //标题
        if( !empty( $param['title'] ) ){
            $keys[] = $shell->search( "buyoffer:title",$param['title'],'set' );
        }

        //类型
        if( !empty( $param['type'] ) ){
            $keys[] = $model->GetType( $param['type'] );
        }

        //用户名
        if( !empty( $param['username'] ) ){
            $usernameKeys = $shell->search( "member:username",$param['username'],'array');
            if( !empty( $usernameKeys ) ){
                 $usernameKeys = explode( ',',$usernameKeys );
                $newUsernameKeys = array();
                foreach($usernameKeys as $v ){
                    $newUsernameKeys[] = "set:buyoffer:member:{$v}";
                }
            }
            $keys[] = $model->GetMember( $newUsernameKeys );
        }

        //状态
        if( isset( $param['state'] ) && $param['state'] !== '' ){
            $param['state'] = intval( $param['state'] );
            $keys[] = $model->GetState( $param['state'] );
        }else{
            //所有状态的并集

            $param = array(
             'set:buyoffer:state:0',
             'set:buyoffer:state:1',
             'set:buyoffer:state:2',
             'set:buyoffer:state:3',
			 'set:buyoffer:state:4',
            );
            $keys[] = $model->GetState( $param );

        }
        $keys[] = 'set:buyoffer:status:1';
        $data = $model->GetAllBuyOffer( $keys,$offset,$rows );
        if( $data ){
            $res['total'] = $data['total'];
            $res['rows'] = $data['rows'];
            $this->ajaxReturn($res);
        }else{
            $res['total'] = 0;
            $res['rows'] = 0;
            $this->ajaxReturn($res);
        }
    }
	
	
	//审核
	public function review(){
        $id = I('json.id', 0, 'intval');
        $state = I('json.state', -1, 'intval');
        $reason = I('json.reason', '', 'trim,htmlspecialchars');
        if ($id < 1 || !in_array($state, [0,1,4])) {
            throw new CsException('参数错误', 400);
        }
        // state=0,4时原因不能为空
        if (in_array($state, [0,4]) && empty($reason)) {
            throw new CsException('原因不能为空', 400);
        }
        $model = D('Admin/BuyOffer');
        $stateName = $model->BuyOfferBaseData()['FIND_GOODS_STATE'];
        $detail = $model->details($id);
        if (empty($detail)) {
            throw new CsException('求购信息不存在', 400);
        }
        if ($detail['state'] == 3) {
            throw new CsException('该求购信息已过期不能修改状态', 400);
        }
        $param['uid'] = $detail['Uid'];
        // 验证状态是否合法
        if($state == 1){
            if ($detail['state'] == 1) {
                throw new CsException('该求购信息已通过，请勿重复审核', 400);
            }
            $param['content']="Your purchase inquiry [{$detail['number']}] authentication approved!";
        }elseif($state == 0){
            if ($detail['state'] != 2) {
                throw new CsException('只有待审核状态才能审核不通过', 400);
            }
            $param['content']="Your purchase inquiry [{$detail['number']}] authentication unapproved! [why: {$reason}]";
        }elseif($state == 4){
            if ($detail['state'] != 1) {
                throw new CsException('该求购信息不是审核通过状态，不能撤销通过', 400);
            }
            $param['content']="Your purchase inquiry [{$detail['number']}] has been revoked! [why: {$reason}]";
        }
        $param['sender']="Webmaster";
        D("User/Message")->createSystem($param);

        if($state == 1){
            //审核通过添加过期时间
            $times = $detail['createTime'] + 86400*$detail['expire'];
            $model->editBuyOffer( $id, array( 'times'=> $times ) );
        }
        $ret = $model->editState( $id,array( 'state'=> $state ) );
        $art = $model->insertHistory( $id, [
            'addTime' => time(),
            'opera' => $stateName[$state],
            'otype' => 'Webmaster',
            'oid' => session('userid'),
            'state' => $state,
            'reason' => $reason,
        ] );
        if( !$ret || !$art){
            throw new CsException('操作失败', 400);
        }else{
            $this->ajaxReturn('操作成功');
        }
	}
	
	
	//detail
	public function details(){
		$id=I('json.id', 0, 'intval');
		if($id < 1) {
            throw new CsException('参数错误', 400);
        }
        $model = D('Admin/BuyOffer');
        $pram = array('id','number','title','type','Uid','content','state','image','location');
		$data = $model->details( $id, $pram );
        $ret = $model->BuyOfferBaseData();
        $type = $ret['FIND_GOODS_TYPE'];
        $state = $ret['FIND_GOODS_STATE'];
        $info = $model->GetMemberInfo( $data['Uid'],array( 'companyName','other' ) );
        $other = unserialize($info['other']);
        $pram = array( 'title' );
        $co  = $model->GetAreaTitle( $other['country'],$pram );
        $s  = $model->GetAreaTitle( $other['area_s'],$pram );
        $c  = $model->GetAreaTitle( $other['area_c'],$pram );
        $data['companyName'] = $info['companyName'];
        $data['area'] = empty($c['title']) ? '' : $c['title'] . ',';
        $data['area'] .= $s['title'].','.$co['title'];
        $data['type'] = $type[$data['type']];
        $data['state'] = $state[$data['state']];
        $data['image'] = $data['image'] ? $data['image'] : '';
        $data['location'] = $data['location'] ? $data['location'] : '';
        $this->ajaxReturn(['data' => $data]);
	}

    public function BuyOfferHistory(){
        $id=I('json.id', 0, 'intval');
        if($id < 1) {
            throw new CsException('参数错误', 400);
        }
        $model=D('Admin/BuyOffer');
        $ret = $model->GetHistory( $id, '', '' );
        $data = [];
        if (!empty($ret)) {
            foreach ($ret as $key => $value) {
                $data[] = [
                    'addTimeTip' => $value['addTime'],
                    'operaTip' => $value['state'],
                    'operatorTip' => $value['oid'],
                ];
            }
        }
        $this->ajaxReturn(['data' => $data]);
    }

    //求购数据--导出
    public function expFind(){
        $shell=D('Home/Shell');
        $model=D('Admin/BuyOffer');
        $Account = D('User/Account');
        $redis = \Think\Cache::getInstance('Redis');
        $param=I('get.');

        $keys = array();
        //信息编号
        if( !empty( $param['number'] ) ){
            $keys[] = $model->GetNumber( $param['number'] );
        }

        //标题
        if( !empty( $param['title'] ) ){
            $keys[] = $shell->search( "buyoffer:title",$param['title'],'set' );
        }

        //类型
        if( !empty( $param['type'] ) ){
            $keys[] = $model->GetType( $param['type'] );
        }

        //用户名
        if( !empty( $param['username'] ) ){
            $usernameKeys = $shell->search( "member:username",$param['username'],'array');
            if( !empty( $usernameKeys ) ){
                $usernameKeys = explode( ',',$usernameKeys );
                $newUsernameKeys = array();
                foreach($usernameKeys as $v ){
                    $newUsernameKeys[] = "set:buyoffer:member:{$v}";
                }
            }
            $keys[] = $model->GetMember( $newUsernameKeys );
        }

        //状态
        if( isset( $param['state'] ) && $param['state'] !== '' ){
            $param['state'] = intval( $param['state'] );
            $keys[] = $model->GetState( $param['state'] );
        }else{
            //所有状态的并集

            $param = array(
                'set:buyoffer:state:0',
                'set:buyoffer:state:1',
                'set:buyoffer:state:2',
                'set:buyoffer:state:3',
                'set:buyoffer:state:4',
            );
            $keys[] = $model->GetState( $param );

        }
        $keys[] = 'set:buyoffer:status:1';
        $data = $model->GetAllBuyOffer( $keys);

        $state=array(
            '2'=>'待审核',
            '1'=>'正常',
            '3'=>'已过期',
            '4'=>'已撤销',
            '0'=>'审核不通过',
        );

        foreach($data as $k=>$v){
            $History =$model ->GetHistory($v['id']);
            ksort($History);
            foreach ($History as $k2 => $v2) {
                    if ($v2['states'] == 2) {
                        unset($History[$k2]);
                    }

            }

            $firstDate = array_slice($History,0,1)[0];
            $lastDate = end($History);
            $data[$k]['first']=$firstDate['addTime'];//初审时间
            $data[$k]['firstName']=$firstDate['oid'];//初审人
            $data[$k]['lastName']=$lastDate['oid'];//最新审核人
            $data[$k]['last']=$lastDate['addTime'];//最新审核时间
            $data[$k]['createTime']=$v['createTime'];//创建时间
            $data[$k]['updateTime']=$v['updateTime'];//最新修改时间
            $data[$k]['timeup']=empty($v['Times'])?'':$v['Times'];//有效期截止时间
            $data[$k]['username'] =$redis->hget("hash:member:{$v['Uid']}",'username');
            $data[$k]['companyName']=$v['companyName'];
            $pram=array('other');
            $areaData=$Account->SelectAccountInfo( $v['Uid'],$pram );

            if( !empty( $areaData ) ){
                $area['country'] = $Account->GetAreaTitle( $areaData['other']['country'],array( 'title' ) )['title'];//国家
                $area['area_s']  = $Account->GetAreaTitle( $areaData['other']['area_s'],array( 'title' ) )['title'];//地区
                $area['area_c']  = $Account->GetAreaTitle( $areaData['other']['area_c'],array( 'title' ) )['title'];//城市
            }

            $data[$k]['area']=trim(implode('-',$area),'-');
            if(empty($v['reason'])){
                $data[$k]['review']=$state[$v['state']];
            }else{
                $data[$k]['review']=$state[$v['state']].'【原因：】'.$v['reason'];
            }

            $data[$k]['type']=$v['type'];
        }

        $xlsName  = "求购列表";
        $xlsCell  = array(
            array('number','信息编号'),
            array('title','标题'),
            array('type','信息类型'),
            array('content','求购内容'),
            array('timeup','有效期截止'),
            array('username','用户名'),
            array('companyName','公司名称'),
            array('area','所在地区'),
            array('review','状态'),
            array('createTime','创建时间'),
            array('updateTime','最新修改时间'),
            array('first','初始审核时间'),
            array('firstName','初始审核人'),
            array('last','最新审核时间'),
            array('lastName','最新审核人')

        );
        $xlsData = $data;//读取列表
        exportExcel($xlsName,$xlsCell,$xlsData);

    }

}
