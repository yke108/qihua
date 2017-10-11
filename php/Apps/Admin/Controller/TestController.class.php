<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class TestController extends CommonController {

    public function index(){
    	$list = [];
    	$result = [
    		'user'=>$user,
    	];
    	$this->assign('jsobj', json_encode($result));
        $this->display();
    }
    
    public function login(){
    	$data = [
    		'user'=>[
    			'username'=>'小明',
    		],
    	];
    	$this->ajaxReturn($data);
    }
    
    public function lst(){
    	$result = [
    		'dturl'=>U('getData'),
    		'places'=>[],
    		'datas'=>[],
    	];
    	$this->assign('jsobj', json_encode($result));
    	$this->display();
    }

    public function getData(){
    	$result = [
    		['date'=>'2017-09-09', 'name'=>'小小', 'address'=>'太家人', 'id'=>1],
    		['date'=>'2017-09-09', 'name'=>'小小', 'address'=>'太家人', 'id'=>2],
    		['date'=>'2017-09-09', 'name'=>'小小', 'address'=>'太家人', 'id'=>3],
    		['date'=>'2017-09-09', 'name'=>'小小', 'address'=>'太家人', 'id'=>4],
    		['date'=>'2017-09-09', 'name'=>'小小', 'address'=>'太家人', 'id'=>5],
    		['date'=>'2017-09-09', 'name'=>'小小', 'address'=>'太家人', 'id'=>6],
    	];
    	$data = [
    		'datas'=>$result,
    		'page'=>1,
    		'pageSize'=>20,
    		'totalCount'=>1000,
    	];
    	$this->ajaxReturn($data);
    }
    
    public function edit(){
    	$post = I('post.');
    	$data = [
    		'data'=>$post,
    	];
    	$this->ajaxReturn($data);
    }
    
    public function del(){
    	$this->ajaxReturn('操作成功');
    }
    
    public function getOptions(){
    	$result = [
    		['value'=>'440000', 'label'=>'广东省', 'children'=>[
    			['value'=>'440100', 'label'=>'广州市', 'children'=>[
    				['value'=>'440101', 'label'=>'天河区'],
    				['value'=>'440102', 'label'=>'越秀区'],
    			]],
    			['value'=>'440200', 'label'=>'深圳市', 'children'=>[
    				['value'=>'440202', 'label'=>'南山区'],
    			]],
    		]],
    	];
    	$data = [
    		'datas'=>$result,
    		'places'=>[
    			['value'=>'440000', 'label'=>'广东省'],
    			['value'=>'330000', 'label'=>'江西省'],
    			['value'=>'360000', 'label'=>'湖南省'],
    		],
    	];
    	$this->ajaxReturn($data);
    }
    
    public function user(){
    	$data = [
    		'datas'=>[
    			'name'=>'小二',
    			'identity'=>'超级管理员',
    		]
    	];
    	$this->ajaxReturn($data);
    }
    
    public function upload(){
    	$data = ['id'=>1];
    	$this->ajaxReturn($data);
    }
}