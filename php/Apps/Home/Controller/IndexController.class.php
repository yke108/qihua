<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends CommonController {
    public function index() {
        $result = [];//结果输出数据
        $model = D( 'Home/Product' );
        $state = $model->getProductState();
        $currency = $model->getProductCurrency();
        $weightUnit = $model->getProductWeightUnit();
        // 热门商品，根据销量sales的倒序
        $param = array(
            'by' => 'hash:product:*->sales',
            'state' => $state['ACTIVE']['value'],
            'page_size' => 8,
        );
        $list = $model->lists( $param );
        $list = empty($list['lists'])?array():$list['lists'];
        $hotProducts = [];
        if( !empty( $list ) ){
            foreach( $list as $value ){
                $hotProducts[] = [
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'currency' => empty($currency[$value['currency']]['character']) ? '' : $currency[$value['currency']]['character'],
                    'price' => sprintf('%.2f', $value['price']),
                    'weightUnit' => $weightUnit[$value['weightUnit']]['name'],
                    'thumb' => unserialize($value['images'])[0],
                ];
            }
        }
        $result['hotProducts'] = $hotProducts;
        unset($list, $hotProducts);

        // 分类商品
        $categories = D( 'Home/Category' )->getCategory();
        $allowCategory = array( 'Daily Chemicals', 'Agrochemicals', 'Petrochemicals' );
        $categoryFloor = [];
        $param = array(
            'state' => $state['ACTIVE']['value'],
            'page_size' => 8,
        );
        foreach( $categories as $v ){
            if( !in_array( $v['text'], $allowCategory ) ){
                continue;
            }
            $param['category'] = $v['id'];
            $list = $model->lists( $param );
            $list = empty($list['lists'])?array():$list['lists'];
            $products = [];
            if( !empty( $list ) ){
                foreach( $list as $value ){
                    $products[] = [
                        'id' => $value['id'],
                        'title' => $value['title'],
                        'currency' => empty($currency[$value['currency']]['character']) ? '' : $currency[$value['currency']]['character'],
                        'price' => sprintf('%.2f', $value['price']),
                        'weightUnit' => $weightUnit[$value['weightUnit']]['name'],
                        'thumb' => unserialize($value['images'])[0],
                    ];
                }
            }
            $categoryFloor[] = [
                'id'=> $v['id'],
                'text'=> $v['text'],
                'products'=> $products
            ];
        }
        $result['categoryFloor'] = $categoryFloor;
        unset($list, $categoryFloor);

        // 购买需求
        // $buyoffer = D('Buyoffer')->detailList();
        // $result['buyoffers'] = empty($buyoffer['list']) ? [] : $buyoffer['list'];
        
        //合作伙伴
        $partners = D( 'Home/Partner' )->lists( array() );
        $result['partners'] = empty($partners['lists']) ? [] : $partners['lists'];
        unset($partners);

        // 分类
        $this->assign( 'result', $result );
        $this->display();
    }

    //首页商品分类
    function getcategory(){
    	$list = array_slice(getcategory(), 0, 8);
        $this->ajaxReturn(['data'=> $list]);
    }

    // 获取首页数据
    public function getIndex() {
        $result = [];//结果输出数据
        $model = D( 'Home/Product' );
        $state = $model->getProductState();
        $currency = $model->getProductCurrency();
        $weightUnit = $model->getProductWeightUnit();
        // 热门商品，根据销量sales的倒序
        $param = array(
            'by' => 'hash:product:*->sales',
            'state' => $state['ACTIVE']['value'],
            'page_size' => 8,
        );
        $list = $model->lists( $param );
        $list = empty($list['lists'])?array():$list['lists'];
        $hotProducts = [];
        if( !empty( $list ) ){
            foreach( $list as $value ){
                $hotProducts[] = [
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'currency' => empty($currency[$value['currency']]['character']) ? '' : $currency[$value['currency']]['character'],
                    'price' => sprintf('%.2f', $value['price']),
                    'weightUnit' => $weightUnit[$value['weightUnit']]['name'],
                    'thumb' => picurl(unserialize($value['images'])[0]),
                	'url'=>U('product/detail', ['id'=>$value['id']]),
                ];
            }
        }
        $result['hotProducts'] = [
        	'products'=>$hotProducts,
        	'ad'=>[
        		'img'=>ppic('Front/images/pic2.jpg'),
        	],
        ];
        unset($list, $hotProducts);

        // 分类商品
        $categories = D( 'Home/Category' )->getCategory();
        $allowCategory = array( 'Daily Chemicals', 'Agrochemicals', 'Petrochemicals' );
        $categoryFloor = [];
        $param = array(
            'state' => $state['ACTIVE']['value'],
            'page_size' => 8,
        );
        foreach( $categories as $v ){
            if( !in_array( $v['text'], $allowCategory ) ){
                continue;
            }
            $param['category'] = $v['id'];
            $list = $model->lists( $param );
            $list = empty($list['lists'])?array():$list['lists'];
            $products = [];
            if( !empty( $list ) ){
                foreach( $list as $value ){
                    $products[] = [
                        'id' => $value['id'],
                        'title' => $value['title'],
                        'currency' => empty($currency[$value['currency']]['character']) ? '' : $currency[$value['currency']]['character'],
                        'price' => sprintf('%.2f', $value['price']),
                        'weightUnit' => $weightUnit[$value['weightUnit']]['name'],
                        'thumb' => picurl(unserialize($value['images'])[0]),
                    	'url'=>U('product/detail', ['id'=>$value['id']]),
                    ];
                }
            }
            $ad_test = [2, 8, 10];
            $categoryFloor[] = [
                'id'=> $v['id'],
                'text'=> $v['text'],
                'products'=> $products,
            	'ad'=>[
            		'img'=>ppic('Front/images/pic'.$ad_test[intval($kx++)].'.jpg'),
            	],
            ];
        }
        $result['categoryFloor'] = $categoryFloor;
        unset($list, $categoryFloor);
        
        //合作伙伴
         $pl = D( 'Home/Partner' )->lists( array() );
         $partners = [];
         foreach ($pl['lists'] as $vo){
         	$vo['img'] = picurl($vo['img']);
         	$partners[] = $vo;
         }
        $result['partners'] = $partners;
        unset($partners);

        $this->ajaxReturn($result);
    }
}