<?php 
namespace Admin\Controller;
use Think\Controller;
use Common\Basic\CsException;

/**
 * 供应管理控制器
 */
class SupplyController extends CommonController{


	public function lists(){
        $model=D('Admin/Supply');
        $ret = $model->SupplyBaseData();

		$type=$ret['FIND_GOODS_TYPE'];
        $state=$ret['FIND_GOODS_STATE'];
        $this->assign('state',$state);
		$this->assign('type',$type);
		$this->display('list');
	}
	
	//供应列表
	public function findGoods(){
        $shell=D('Home/Shell');
        $model=D('Admin/Supply');
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
            $keys[] = $shell->search( "supply:title",$param['title'],'set' );
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
                    $newUsernameKeys[] = "set:supply:member:{$v}";
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
             'set:supply:state:0',
             'set:supply:state:1',
             'set:supply:state:2',
             'set:supply:state:3',
			 'set:supply:state:4',
            );
            $keys[] = $model->GetState( $param );

        }
           $keys[] = 'set:supply:status:1';

         $data = $model->GetAllSupply( $keys,$offset,$rows );
// var_dump($data);
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

		$model = D('Admin/Supply');
        $stateName = $model->SupplyBaseData()['FIND_GOODS_STATE'];
        $detail = $model->details($id);
        if (empty($detail)) {
            throw new CsException('供应信息不存在', 400);
        }
        // 验证状态是否合法
        if($state == 1){
            if ($detail['state'] == 1) {
                throw new CsException('该供应信息已通过，请勿重复审核', 400);
            }
        }elseif($state == 0){
            if ($detail['state'] != 2) {
                throw new CsException('只有待审核状态才能审核不通过', 400);
            }
        }elseif($state == 4){
            if ($detail['state'] != 1) {
                throw new CsException('该供应信息不是审核通过状态，不能撤销通过', 400);
            }
        }
        //申请结果通知用户
        D('Admin/Supply')->set_mess($id,$state,$reason);

        if($data['state'] == 1){
            //添加审核时间
            $arr = $model->details( $data['id'],array( 'createTime','expire') );
            $times = $arr['createTime'] + 86400*$arr['expire'];
            $model ->editSupply( $data['id'], array( 'times'=>$times ) );
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
		$id=I('json.id');
		if(empty($id))exit;
        $data=I('get.');
        $model=D('Admin/Supply');
        $pram = array('id','number','title','type','Uid','content','state','image','location');
		$data=$model->details( $data['id'],$pram );
        $ret = $model->SupplyBaseData();
        $type=$ret['FIND_GOODS_TYPE'];
        $state=$ret['FIND_GOODS_STATE'];
        $info = $model->GetMemberInfo( $data['Uid'],array( 'companyName','other' ) );
        $other = unserialize($info['other']);
        $pram = array( 'title' );
        $co  = $model->GetAreaTitle( $other['country'],$pram );
        $s  = $model->GetAreaTitle( $other['area_s'],$pram );
        $c  =$model->GetAreaTitle( $other['area_c'],$pram );

        $data['companyName'] = $info['companyName'];
        $data['area'] = empty($c['title']) ? '' : $c['title'] . ',';
        $data['area'] .= $s['title'].','.$co['title'];
        $data['type'] = $type[$data['type']];
        $data['state'] = $state[$data['state']];
        $data['image'] = $data['image'] ? $data['image'] : '';
        $data['location'] = $data['location'] ? $data['location'] : '';
        $this->ajaxReturn(['data' => $data]);
	}

    public function SupplyHistory(){
        $id=I('get.id', 0, 'intval');
        if($id < 1) {
            throw new CsException('参数错误', 400);
        }
        $model=D('Admin/Supply');
        $ret = $model->GetHistory( $id, 0, 1000 )['rows'];// 所有历史记录
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

}
