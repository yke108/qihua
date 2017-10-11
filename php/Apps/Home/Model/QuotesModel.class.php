<?php
// +----------------------------------------------------------------------
// | Keywa Inc.
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.keywa.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: vii
// +----------------------------------------------------------------------
namespace Home\Model;
use Think\Model;

class QuotesModel extends CommonModel{

	public function __construct(){
	    $this->autoCheckFields = false;
        $this->redis = \Think\Cache::getInstance('Redis');
	}

    /**
     * 获取详情
     * @return array
     */
    public function detail($quotesId){
        $cacheKey = 'hash:quotes:'.$quotesId;
        $ret = $this->redis->hgetall( $cacheKey );
        if (empty($ret)) {
            return [];
        }
        return $ret;
    }

    /**
     * 新增报价单
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insertQuotes( $data ){
        $quotesId = $this->redis->incr('string:quotes');//获取自增长id
        $data['quotesId'] = $quotesId;
        $data['quotesSn'] = $this->makeSn();

        $this->redis->hmset( 'hash:quotes:'.$quotesId, $data );
        $this->redis->sadd( 'set:quotes:inquiryId:'.$data['inquiryId'], $data['quotesId'] );//增加到用户集合
        $this->redis->set( 'string:quotes:quotesSn:'.$data['quotesSn'], $data['quotesId'] );

        return $quotesId;
    }

    /**
     * 新增报价商品
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insertProduct( $data, $quotesId ){
        foreach ($data as $key => $value) {
            $id = $this->redis->incr('string:quotesProduct');//获取自增长id
            $value['id'] = $id;
            $value['quotesId'] = $quotesId;
            $this->redis->hmset( 'hash:quotesProduct:'.$id, $value );
            $this->redis->sadd( 'set:quotesProduct:quotesId:'.$quotesId, $id );
        }
        return true;
    }

    /**
     * 生成报价单编号
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function makeSn() {
        $quotesSn = date('Ymd') . rand(10000,99999);
        if ($this->redis->exists('string:quotes:quotesSn:'.$quotesSn)) {
            return $this->makeSn();
        }
        return $quotesSn;
    }

    /**
     * 获取临时列表缓存 Cachekey
     * @param array $param <pre> array(
    )
     * @return string
     */
    protected function getTempListsCacheKey(){
        return 'tmp:set:quotes:list:'.uniqid();
    }

    /**
     * 获取报价单列表或者最新一条
     * @param $inquiryId 查询ID
     * @param $getOne 是否获取最新的一条
     * @return array
     */
    public function lists( $inquiryId, $getOne = false ){
        $quotesIds = $this->redis->sMembers('set:quotes:inquiryId:'.$inquiryId);
        if (empty($quotesIds)) {
            return [];
        }
        rsort($quotesIds);
        // 获取最新的一个报价单
        if ($getOne) {
            $quotesId = $quotesIds[count($quotesIds)-1];
            $ret = $this->redis->hGetAll('hash:quotes:'.$quotesId);
            if (empty($ret)) {
                return [];
            }
            $ret['productList'] = $this->getProductList($quotesId);
            return $ret;
        }
        // 获取报价单列表
        $list = [];
        foreach ($quotesIds as $id) {
            $ret = $this->redis->hGetAll('hash:quotes:'.$id);
            $ret['productList'] = $this->getProductList($id);
            if (!empty($ret)) {
                $list[] = $ret;
            }
        }
        return $list;
    }

    // 获取报价单的商品列表
    public function getProductList($quotesId) {
        $ids = $this->redis->sMembers('set:quotesProduct:quotesId:'.$quotesId);
        if (empty($ids)) {
            return [];
        }
        $list = [];
        foreach ($ids as $id) {
            $list[] = $this->redis->hGetAll('hash:quotesProduct:'.$id);
        }
        return $list;
    }
}