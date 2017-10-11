<?php 
namespace User\Controller;
use Think\Controller;
use Common\Basic\CsException;
	/**
	 * Supply Offer Controller
	 */

class SupplyController extends CommonController{
	
	/**
	* 修改求购
	*
	*/
	public function modify(){
		$id = intval(I('json.id'));
		if($id < 1){
			$this->addSupply();
		}
		$data = [
			'id'=> $id,
			'title'=> I('json.title'),
			'type'=> I('json.type'),
			'expire'=> I('json.expire'),
			'content'=> I('json.content'),
			'location'=> I('json.location'),
			'image'=> I('json.image'),
			'updateTime'=> time(),
		];
		$param['data'] = $data;
		$param['uid']=$this->uid;
		$res=D('Supply')->modify($param);
		if($res){
			$this->ajaxReturn(array('success'));
		}else{
			throw new CsException("Failed", 400);
		}
	}

	public function getDetail() {
		$model = D('Supply');
		$param['id']=I("json.id");
		if($param['id'] > 0){
			$param['uid']=$this->uid;
			$res=$model->details($param);
			if (empty($res)) {
				throw new CsException("Buyoffer does not exist", 400);
			}
			
			if(!$res){
				$this->error('','BuyofferList',0);
			}
			$detail = [
				'id'=> $res['id'],
				'title'=> $res['title'],
				'type'=> $res['type'],
				'expire'=> $res['expire'],
				'content'=> $res['content'],
				'location'=> empty($res['location']) ? '' : $res['location'],
				'image'=> empty($res['image']) ? '' : $res['image'],
			];
		} else {
			$detail = [
				'id'=>0,
			];
		}
		
		$this->ajaxReturn([
			'detail'=> $detail,
			'typeList'=> D("Supply")->getSupplyType(),
			'expireList'=> C('FIND_GOOD_EXPIRE'),
		]);
	}
	
	

	/**
	 * 获取操作历史
	 *
	 */
	public function Operation(){
		$param['id']=I('id');
		$param['p']=I('p');
		$param['uid']=$this->uid;
		$model=D('Supply');
		$arr=$model->Opera($param);
		$this->assign('opera',$arr['res']);
		$this->assign('show',$arr['show']);
		$this->display('member-findGoods-operHistory');
	}
	
	/**
	 * Supply 详情
	 */

	public function SupplyDetails(){
		$param['id']=I('get.id');
		$model=D('Supply');
		$res=$model->details($param);
		$mold=$model->getSupplyType();
		$date=C('FIND_GOOD_EXPIRE');
		$check=C('FIND_GOODS_STATUS');
		$res['state']=$check[$res['state']];
		$res['type']=$mold[$res['type']];
		$res['expire']=$date[$res['expire']];
	
		//收藏数
		$collectType = 0;
		$collectCount = intval( D( 'User/Collect' )->getCount( array( 'id' => $param['id'], 'type' => $collectType ) ) );
		$isCollect = false;
		if( !empty( $this->uid ) ){
			$isCollect = D( 'User/Collect' )->getIsCollect( array( 'uid' => $this->uid, 'type' => $collectType , 'id' => $param['id'] ) );
		}
		$this->assign( 'loginUid', $this->uid  );
		$this->assign( 'collectCount', $collectCount );
		$this->assign( 'isCollect', $isCollect );
		$this->assign('mold',$mold);
		$this->assign('date',$date);
		$this->assign('res',$res);
		var_dump($res);exit;
		$this->display('find-goods-detail');
	}
	
	
	/**
	 * 列表页
	 */
	
	public function lists(){
		// $param['title']=I('title');
		// $param['type']=I('type');
		// $param['state']=I('state');
		// $model=D('Supply');
		// $param['p']=I('p');
		// $param['Uid']=$this->uid;
		// $res=$model->lists($param);
		$type=$model->getSupplyType();
		$state=C('FIND_GOODS_STATUS');
		// $companyName=D('Account')->SelectAccountInfo($this->uid,array('companyName'))['companyName'];
  //       //判断是否完成资料
  //       $IsCompleteInfo = D('User/Account') ->checkInfoIsComplete( array( 'id'=>$this->uid ) );
  //       $this->assign('IsCompleteInfo',intval( $IsCompleteInfo ) );
		// $this->assign('companyName',$companyName);
		// $this->assign('list',empty($res['list']) ? array() : $res['list']);
		// $this->assign('show',empty($res['show']) ? '' : $res['show']);
		// $this->assign('pageinfo',empty($res['pageinfo']) ? '' : $res['pageinfo']);
		$this->assign('type',$type);
		$this->assign('state',$state);
		$this->display("member-supply-list");
	}

	/**
	 * 求购列表
	 */
	public function getList(){
		$this->checkLogin();
		$param['title']=I('get.title');
		$param['type']=I('get.type');
		$param['state']=I('get.state');
		$model=D('Supply');
		$param['p']=I('get.page');
		$param['pageSize']=I('get.pageSize');
		$param['Uid']=$this->uid;
		$res=$model->lists($param);
		if (empty($res)) {
			$this->ajaxReturn(['count'=> 0, 'list'=> []]);
		}
		$list = [];
		$count = isset($res['pageinfo']['count']) && !empty($res['pageinfo']['count']) ? $res['pageinfo']['count'] : 0;
		foreach ($res['list'] as $key => $value) {
			$list[] = [
				'id'=> $value['id'],
				'title'=> $value['title'],
				'type'=> $value['type'],
				'state'=> $value['state'],
				'image'=> $value['image'],
			];
		}
		$this->ajaxReturn(['count'=> $count, 'list'=> $list]);
	}
	
	
	/**
	 * 新增SupplyOffer
	 */
	
	public function addSupply(){
		$data = [
			'title'=> I('json.title'),
			'type'=> I('json.type'),
			'expire'=> I('json.expire'),
			'content'=> I('json.content'),
			'location'=> I('json.location'),
			'image'=> I('json.image'),
			'updateTime'=> time(),
		];
		$data['Uid']=$this->uid;

		$data['createTime']=$data['updateTime']=time();
		$data['state']=2;
		$data['times']='';
        //        //生成求购编号,  1 QGYL 2 QGPF  3 QGZL 5 QGJS
        if($data['type'] == 1){
            $type='GYYL';
        }else if($data['type'] == 2){
            $type='GYPF';
        }else if($data['type'] == 3){
            $type='GYZL';
        }else if($data['type'] == 4){
            $type='GYJS';
        }else if($data['type'] == 5){
        	$type="GYQT";
        }
        $data['number']=$type.date('ymdH').rand(1,99999);
        $param['data']=$data;
		$model=D('Supply');
		$res=$model->addSupply($param);
		if($res){
			$this->ajaxReturn('success');
		}else{
			throw new CsException("Failed", 400);
		}
		
	}

	/**
	 * 删除求购
	 */
	public function delSupplyOffer(){
		$this->checkLogin();
		$id=I('json.id');
		//暂时指定uid
		$param['Uid']=$this->uid;
		$param['id']=explode(',', $id);
		$model=D('Supply');
		if($model->del($param)){
			$this->ajaxReturn(array('success'));
		}else{
			throw new CsException("Failed", 400);
		}
	}
}









?>