<?php
namespace Home\Controller;

use Think\Controller;
use Think\Page;
use Common\Basic\CsException;

/**
 * 求购控制器
 */

class SupplyController extends CommonController{
	
	
	/**
	 * 首页Supply列表
	 */
	
	public function index(){
		$keyword = trim( urldecode( I( 'json.keyword' ) ) );
		$type = intval( I( 'json.type' ) );
		$page = empty( I('json.page') ) ? 1 : intval( I('json.page') );
        $pageSize = empty( I('json.pageSize') ) ? 5 : intval( I('json.pageSize') );

        $res = D('Supply')->indexlist([
        	'page' => $page,
        	'pageSize' => $pageSize,
        	'title' => $keyword,
        	'type' => $type,
        ]);

        $keyword = trim( urldecode( I( 'get.keyword' ) ) );
		$type = intval( I( 'get.type' ) );
		$page = empty( I('get.p') ) ? 1 : intval( I('get.p') );
        $pageSize = empty( I('get.pageSize') ) ? 5 : intval( I('get.pageSize') );

        $result = D('Supply')->indexlist([
        	'page' => $page,
        	'pageSize' => $pageSize,
        	'title' => $keyword,
        	'type' => $type,
        ]);
        $result['type'] = $type;
        $PageObject = new \Think\Page($result['count'], $pageSize);
        $result['pageCount'] = $PageObject->pageCount();
        $result['page'] = $PageObject->page();
        $result['show'] = $PageObject->show();
        $result['typeList'] = D("User/Supply")->getSupplyType();
        $this->assign('result', $result);
		// $model=D('Supply');
		// $param='';
		// !empty($_GET['p'])?$param['p']=I('p'):$param['p']=1;
		// !empty($_GET['keyword'])?$param['title']=I('keyword'):null;
		// !empty($_GET['companyModels'])?$param['type']=I('companyModels'):null;
		// $res=$model->indexlist($param);
		// $type=D("User/Supply")->getSupplyType();
		// $this->assign('type',$type);
		// $companyName=D('User/Account')->SelectAccountInfo($this->uid,array('companyName'))['companyName'];
		// $this->assign('companyName',$companyName);
		// $this->assign('list',empty($res['list'])?array():$res['list']);
		// $this->assign('show',empty($res['show'])?'':$res['show']);
		// if(!empty($param['type'])){
		// 	$gettype=explode(',', $param['type']);
		// 	$str='';
		// 	foreach ($gettype as $v){
		// 		if(empty($str)){
		// 			$str.=$type[$v];
		// 		}else{
		// 			$str.='+'.$type[$v];
		// 		}
				
		// 	}
		// 	$this->assign('gettype',$gettype);
		// 	$this->assign('str',$str);
		// }
		// $this->assign('pageinfo',empty($res['pageinfo'])?array():$res['pageinfo']);
		// $this->assign( 'loginUid', $this->uid  );
  		// $this->assign( 'cate', getcategory() );
		$this->display('find-goods');
	}
	
	public function lists() {
		$this->assign('url', C('KW_DOMAIN').'supply/search');
		$this->assign('url_search_data', C('KW_DOMAIN').'supply/getSearchData');
		
		$this->assign('page_title', 'Supply offers');
		$this->display('supply_offers');
	}

	/**
	 * 搜索供应列表
	 */
	
	public function search(){
		// 获取参数
		$keyword = trim( urldecode( I( 'json.keyword' ) ) );
		$type = intval( I( 'json.type' ) );
		$page = empty( I('json.page') ) ? 1 : intval( I('json.page') );
		$p = I('get.page');
		if (isset($p)) {
			$page = empty( $p ) ? 1 : intval( $p );
		}
        $pageSize = empty( I('json.pageSize') ) ? 5 : intval( I('json.pageSize') );

        $res = D('Supply')->indexlist([
        	'page' => $page,
        	'pageSize' => $pageSize,
        	'title' => $keyword,
        	'type' => $type,
        ]);
        
        foreach ($res['list'] as $key => $value) {
        	$res['list'][$key]['image'] = $value['image'] ? picurl($value['image']) : ppic('Front/images/placeholder.jpg');
        	$res['list'][$key]['url'] = U('SupplyDetails', array('id'=>$value['id']));
        }
        
        //分页
        $pageHtml = new Page( $res['count'], $pageSize );
        $pageHtml->setConfig( 'prev', '<i class="icon-prev"></i>Previous Page' );
        $pageHtml->setConfig( 'next', 'Next Page<i class="icon-next"></i>' );
        $page_html = $pageHtml->show();
        $res['page_html'] = $page_html;
        $res['page'] = $page;
        $res['page_count'] = $pageHtml->pageCount();
        
        $this->ajaxReturn($res);
	}

	/**
     * 获取供应搜索的参数数据
     */
    public function getSearchData(){
    	$type = D("User/Supply")->getSupplyType();
    	$data = [];
    	foreach ($type as $key => $value) {
    		$data[] = ['type'=> $key, 'text'=> $value];
    	}
		$this->ajaxReturn(['data'=> $data]);
    }
	
	
	/**
	 * Supply详情
	 */
	public function detail(){
		$this->display('supply_offers_detaild');
	}
	
	public function SupplyDetails($id = 0) {
		$this->assign('id', $id);
		$this->assign('url', C('KW_DOMAIN').'Supply/getDetail');
		
		$this->assign('page_title', 'Supply offers - Detail');
		$this->display('detail');
	}
	
	/**
	 * 获取供应详情
	 */
	public function getDetail(){
		$param['id'] = I('json.id');
		$model = D('Supply');
		$res = $model->details($param);
		if (empty($res)) {
			throw new CsException("Supply does not exist", 400);
		}
		$mold=D("User/Supply")->getSupplyType();
		$date=C('FIND_GOOD_EXPIRE');
		$check=C('FIND_GOODS_STATUS');
		$member = D('User/Member')->get($res['Uid']);

		$result = [];
		$result['title'] = $res['title'];
		$result['content'] = htmlspecialchars_decode($res['content']);
		$result['number'] = $res['number'];
		$result['postTime'] = date('F d,Y', $res['updateTime']);
		$result['expireTime'] = date('F d,Y', $res['times']);
		$result['type'] = $mold[$res['type']];
		$result['location'] = $res['location'];
		$result['image'] = picurl($res['image']);
		$result['img'] = empty($member['img']) ? '/Public/Home/images/logo.png' : $member['img'];
		$result['username'] = $member['username'];
		$result['companyName'] = $member['companyName'];
		$result['showContact'] = empty($this->uid) || $this->uid != $res['Uid'] ? 1 : 0;
		$result['isLogin'] = $this->uid ? 1 : 0;
		$result['isCollect'] = 0;
        if( !empty( $this->uid ) ){
            $isCollect = D( 'User/Collect' )->getIsCollect( array( 'uid' => $this->uid, 'type' => 2 , 'id' => $param['id'] ) );
            if ($isCollect) {
            	$result['isCollect'] = 1;
            }
        }
		$this->ajaxReturn(['data'=> $result]);
	}
	
}