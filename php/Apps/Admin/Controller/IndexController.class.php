<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class IndexController extends CommonController {
	public function _initialize(){
		
	}
	
    public function index(){
    	$list = [];
    	$list[] = [
    		'id'=>9900, 'txt'=>'统计分析', 'cls'=>'',
    		'children'=>[
    			['id'=>9901,'txt'=>'概况','url'=>U('index/main')],
    		]
    	];
    	$list[] = [
    		'id'=>1000, 'txt'=>'用户管理', 'cls'=>'',
    		'children'=>[
    			['id'=>1001,'txt'=>'用户审核列表','url'=>U('auth/index')],
    			['id'=>1002,'txt'=>'用户列表','url'=>U('test/index')],
    		]
    	];
    	$list[] = [
    		'id'=>2000, 'txt'=>'商品管理', 'cls'=>'',
    		'children'=>[
                ['id'=>2001,'txt'=>'商品列表','url'=>U('Product/valid')],
    			['id'=>2002,'txt'=>'添加商品','url'=>U('Product/addProduct')],
    		]
    	];
        $list[] = [
            'id'=>3000, 'txt'=>'求供管理', 'cls'=>'',
            'children'=>[
                ['id'=>3001,'txt'=>'求供列表','url'=>U('BuyOffer/lists')],
            ]
        ];
        $list[] = [
            'id'=>4000, 'txt'=>'供应管理', 'cls'=>'',
            'children'=>[
                ['id'=>4001,'txt'=>'供应列表','url'=>U('Supply/lists')],
            ]
        ];
        $list[] = [
            'id'=>5000, 'txt'=>'内容管理', 'cls'=>'',
            'children'=>[
                ['id'=>5001,'txt'=>'关于我们','url'=>U('Content/aboutUs')],
                ['id'=>5002,'txt'=>'用户服务协议','url'=>U('Content/protocol')],
                ['id'=>5003,'txt'=>'合作伙伴','url'=>U('Content/partner')],
            ]
        ];
        $list[] = [
            'id'=>6000, 'txt'=>'数据管理', 'cls'=>'',
            'children'=>[
                ['id'=>6001,'txt'=>'地区管理','url'=>U('Data/area')],
                ['id'=>6002,'txt'=>'生产商','url'=>U('Data/producer')],
                ['id'=>6003,'txt'=>'品牌','url'=>U('Data/brand')],
                ['id'=>6004,'txt'=>'商品类别','url'=>U('Data/category')],
                ['id'=>6005,'txt'=>'所在行业','url'=>U('Data/trade')],
                ['id'=>6006,'txt'=>'单位性质','url'=>U('Data/property')],
                ['id'=>6007,'txt'=>'经营模式','url'=>U('Data/model')],
                ['id'=>6008,'txt'=>'年营业额','url'=>U('Data/turnover')],
                ['id'=>6009,'txt'=>'单位人数','url'=>U('Data/employees')],
                ['id'=>6010,'txt'=>'微信密码','url'=>U('Data/wxappsecret')],
                ['id'=>6011,'txt'=>'手机白名单','url'=>U('Data/phone')],
                ['id'=>6012,'txt'=>'关键指标','url'=>U('Data/indicator')],
            ]
        ];


    	$user = [
    		'name'=>'管理员',
    		'identity'=>'系统管理员'
    	];
    	$result = [
    		'user'=>$user,
    		'menu_items'=>$list,
    	];
    	$this->assign('jsobj', json_encode($result));
        $this->display();
    }
    
    public function main(){
    	echo 'abc';
    }
}