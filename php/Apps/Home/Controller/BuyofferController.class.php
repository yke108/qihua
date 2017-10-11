<?php
namespace Home\Controller;

use Think\Controller;
use Think\Page;
use Common\Basic\CsException;

/**
 * 求购控制器
 */

class BuyofferController extends CommonController{
	
	/**
	 * 首页求购列表
	 */
	public function indexlist() {
		$this->assign('url', C('KW_DOMAIN').'buyoffer/search');
		$this->assign('url_search_data', C('KW_DOMAIN').'buyoffer/getSearchData');
		
		$this->assign('page_title', 'Buy Offers');
		$this->display('demand');
	}
	
	/**
	 * 首页求购列表
	 */
	
	public function index(){
		// 获取参数
		$keyword = trim( urldecode( I( 'get.keyword' ) ) );
		$type = intval( I( 'get.type' ) );
		$page = empty( I('get.p') ) ? 1 : intval( I('get.p') );
        $pageSize = empty( I('get.pageSize') ) ? 5 : intval( I('get.pageSize') );

        $result = D('Buyoffer')->indexlist([
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
        $result['typeList'] = C('FIND_GOODS_TYPE');
        $this->assign('result', $result);
// print_r($result);exit;
		// $model=D('Buyoffer');
		// $param='';
		// !empty($_GET['p'])?$param['p']=I('p'):$param['p']=1;
		// !empty($_GET['keyword'])?$param['title']=I('keyword'):null;
		// !empty($_GET['companyModels'])?$param['type']=I('companyModels'):null;
		// $res=$model->indexlist($param);
		// $type=C('FIND_GOODS_TYPE');
		// $this->assign('type',$type);
		// $companyName=D('User/Account')->SelectAccountInfo($this->uid,array('companyName'))['companyName'];
		// $this->assign('companyName',$companyName);
		// $this->assign('list',empty($res['list']) ? array() : $res['list']);
		// $this->assign('show',empty($res['show']) ? '' : $res['show']);
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
		// 	//$this->assign('gettype',empty($gettype)?'':$gettype);
		// 	$this->assign('str',$str);
		// }
		// $this->assign('gettype',empty($gettype)?'':$gettype);
		// $this->assign('pageinfo',empty($res['pageinfo'])?array():$res['pageinfo']);
		// $this->assign( 'loginUid', $this->uid  );
  //       $this->assign( 'cate', getcategory() );
		$this->display('find-goods');
	}

	/**
	 * 首页求购列表
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

        $res = D('Buyoffer')->indexlist([
        	'page' => $page,
        	'pageSize' => $pageSize,
        	'title' => $keyword,
        	'type' => $type,
        ]);
        
        foreach ($res['list'] as $key => $value) {
        	$res['list'][$key]['image'] = $value['image'] ? $value['image'] : __ROOT__.'Site/Public/Front/images/placeholder.jpg';
        	$res['list'][$key]['url'] = U('BuyOfferDetails', array('id'=>$value['id']));
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
     * 获取求购搜索的参数数据
     */
    public function getSearchData(){
    	$type = C('FIND_GOODS_TYPE');
    	$data = [];
    	foreach ($type as $key => $value) {
    		$data[] = ['type'=> $key, 'text'=> $value];
    	}
		$this->ajaxReturn(['data'=> $data]);
    }
	
	
	/**
	 * 求购详情
	 */
	public function detail(){
		// $param['id']=I('get.id');
		// $model=D('Buyoffer');
		// $res=$model->details($param);
		// $mold=C("FIND_GOODS_TYPE");
		// $date=C('FIND_GOOD_EXPIRE');
		// $check=C('FIND_GOODS_STATUS');
		// $member=D('User/Member')->get($res['Uid']);
		// $companyName=D('User/Account')->SelectAccountInfo($this->uid,array('companyName'))['companyName'];
		
		// if($res['Uid']==$this->uid){
		// 	$companyName=-1;
		// }
		// /**var_dump($this->uid);
		// var_dump($companyName);
		// var_dump($res['Uid']);
		// exit;*/
		// $this->assign('companyName',$companyName);
		// $user['username']=$member['username'];
		// $user['uid']=$res['Uid'];
		// $user['companyName']=$member['companyName'];
		// $user['country']=$member['country'];
		// $user['img']=$member['img'];
		// $res['state']=$check[$res['state']];
		// $res['type']=$mold[$res['type']];
		// $res['expire']=$date[$res['expire']];

  //       //收藏数
  //       $collectType = 0;
  //       $collectCount = intval( D( 'User/Collect' )->getCount( array( 'id' => $param['id'], 'type' => $collectType ) ) );
  //       $isCollect = false;
  //       if( !empty( $this->uid ) ){
  //           $isCollect = D( 'User/Collect' )->getIsCollect( array( 'uid' => $this->uid, 'type' => $collectType , 'id' => $param['id'] ) );
  //       }
  //       $this->assign( 'loginUid', $this->uid  );
  //       $this->assign( 'collectCount', $collectCount );
  //       $this->assign( 'isCollect', $isCollect );

		// $this->assign('mold',$mold);
		// $this->assign('date',$date);
		// $this->assign('res',$res);
		// $this->assign('user',$user);
  //       $this->assign( 'cate', getcategory() );
		$this->display('find-goods-detail');
	}
	
	public function BuyOfferDetails($id = 0) {
		$this->assign('id', $id);
		$this->assign('url', C('KW_DOMAIN').'Buyoffer/getDetail');
		
		$this->assign('page_title', 'Buy Offers - Detail');
		$this->display('demand_detail');
	}

	/**
	 * 获取求购详情
	 */
	public function getDetail(){
		$param['id'] = I('json.id');
		$model = D('Buyoffer');
		$res = $model->details($param);
		if (empty($res)) {
			throw new CsException("Buy offers does not exist", 400);
		}
		$mold = C("FIND_GOODS_TYPE");
		$date = C('FIND_GOOD_EXPIRE');
		$check = C('FIND_GOODS_STATUS');
		$member = D('User/Member')->get($res['Uid']);

		$result = [];
		$result['title'] = $res['title'];
		$result['content'] = htmlspecialchars_decode($res['content']);
		$result['number'] = $res['number'];
		$result['postTime'] = date('F d,Y', $res['updateTime']);
		$result['expireTime'] = date('F d,Y', $res['times']);
		$result['type'] = $mold[$res['type']];
		$result['location'] = $res['location'];
		$result['image'] = isset($res['image']) && !empty($res['image']) ? $res['image'] : __ROOT__.'Site/Public/Front/images/placeholder.jpg';
		$result['img'] = empty($member['img']) ? '/Public/Home/images/logo.png' : $member['img'];
		$result['username'] = $member['username'];
		$result['companyName'] = $member['companyName'];
		$result['showContact'] = empty($this->uid) || $this->uid != $res['Uid'] ? 1 : 0;
		$result['isLogin'] = $this->uid ? 1 : 0;
		$result['isCollect'] = 0;
        if( !empty( $this->uid ) ){
            $isCollect = D( 'User/Collect' )->getIsCollect( array( 'uid' => $this->uid, 'type' => 0 , 'id' => $param['id'] ) );
            if ($isCollect) {
            	$result['isCollect'] = 1;
            }
        }
		$this->ajaxReturn(['data'=> $result]);
	}

}