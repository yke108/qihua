<?php
namespace User\Controller;
use Think\Controller;
use Common\Basic\CsException;


/**
 * 我的收藏
 */

class CollectController extends CommonController{
	
	
	//收藏列表
	public function index(){
		$this->checkLogin();
		$param = [];
		$param['type'] = I('json.type', 0, 'intval');
		$param['page'] = I('json.page', 1, 'intval');
		$param['pageSize'] = I('json.pageSize', 10, 'intval');
		$param['title'] = I('json.title');
		$param['uid'] = $this->uid;
		$res=D('Collect')->lists($param);
		$this->ajaxReturn($res);
	}
	
	
	//加入收藏
	
	public function addCollect(){
		$this->checkLogin();
		//接受参数，ID，uid，还有是求购还是商品
		$data['id'] = I('json.id', 0, 'intval');
		$data['type'] = I('json.type', 0, 'intval');
		$data['uid'] = $this->uid;
		if($data['id'] < 1 || !in_array($data['type'], [0,1,2])){
			throw new CsException("Paramer is error", 400);
		}
		$model=D('Collect');
		$res=$model->addcollect($data);
		if($res){
			$this->ajaxReturn('success');
		}else{
			throw new CsException("Failed", 400);
		}
	}
	
	
	//删除收藏
	public function  delCollect(){
		$this->checkLogin();
		$id = I('json.id');
		$type = I('json.type', 0, 'intval');
		if (empty($id) || !in_array($type, [0,1,2])) {
			throw new CsException("Paramer is error", 400);
		}
		$data['id'] = explode(',', $id);
		$data['type'] = $type;
		$data['uid'] = $this->uid;
		$model = D('Collect');
		$res = $model->delcollect($data);
		if($res){
			$this->ajaxReturn('success');
		}else{
			throw new CsException("Failed", 400);
		}
	}
}