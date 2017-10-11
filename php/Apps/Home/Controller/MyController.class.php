<?php
namespace Home\Controller;

use Think\Controller;
use Think\Cache\Driver\Redis;

class MyController extends CommonController {
    public function _initialize(){
    	parent::_initialize();
    	//if ($this->uid < 1) $this->redirect('/user/index/login');
    }
    
    public function buying_collection(){
    	$this->display();
    }
    
    public function certified_enterprise(){
    	$this->display();
    }
    
    public function change_password(){
    	$this->display();
    }
    
    public function inquiry_order(){
    	$this->display();
    }
    
    public function inquiry_order_detaild(){
    	$this->display();
    }
    
    public function manage_email(){
    	$this->display();
    }
    
    public function my_purchase(){
    	$this->display();
    }
    
    public function my_supply(){
    	$this->display();
    }
    
    public function order_detaild(){
    	$this->display();
    }
    
    public function print_order(){
    	$this->display();
    }
    
    public function product_collection(){
    	$this->assign('url', C('KW_DOMAIN').'user/collect/index');
    	$this->assign('page_title', 'My Favorites - Product');
    	$this->display();
    }
    
    public function submit_success(){
    	$this->display();
    }
    
    public function purchase_form(){
    	$this->display();
    }
    
    public function supply_form(){
    	$this->display();
    }
    
    public function shipping_order(){
    	$this->display();
    }
    
    public function supply_collection(){
    	$this->display();
    }
    
    public function supply_material(){
    	$this->display();
    }
    
    public function system_infomation(){
    	$this->display();
    }
    
    public function system_infomation_detaild(){
    	$this->display();
    }
    
    public function user(){
    	$this->display();
    }
    
    public function write_inquiry(){
    	$this->display();
    }
    
    public function write_order(){
    	$this->display();
    }
}