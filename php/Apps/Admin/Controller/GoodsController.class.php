<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class GoodsController extends CommonController {
	public function _initialize(){
		
	}
	
    public function index(){
    	$list = [];
    	$result = [
    		'user'=>$user,
    	];
    	$this->assign('jsobj', json_encode($result));
        $this->display();
    }
}