<?php

namespace Home\Model;
use Think\Model;

class SkuModel extends CommonModel{

	public function __construct(){
	    $this->autoCheckFields = false;
        $this->redis = \Think\Cache::getInstance('Redis');
	}

    /**
     * 新增SKU商品
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insert( $data ){
        $data['skuId'] = $this->redis->incr( 'string:sku' );//获取自增长id
        $this->redis->hmset( 'hash:sku:'.$data['skuId'], $data );
        $this->redis->sadd( 'set:sku:productId:'.$data['productId'], $data['skuId'] );
        // 如果是默认SKU，需要更新到产品表
        if ($data['isDefault'] == 1) {
            $this->redis->hSet( 'hash:product:'.$data['productId'], 'skuId', $data['skuId'] );
        }
        return $data['skuId'];
    }

    /**
     * 删除SKU商品
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function del( $data ){
        $this->redis->hSet('hash:sku:'.$data['skuId'],'status', 0);
        $this->redis->srem( 'set:sku:productId:'.$data['productId'], $id );//库存状态
        return true;
    }

    /**
     * 编辑SKU商品
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function edit( $data ){
        $data['skuId'] = isset($data['skuId']) ? (int)$data['skuId'] : 0;
        if (empty($data['skuId'])) {
            return false;
        }
        $this->redis->hmset( 'hash:sku:'.$data['skuId'], $data );
        return true;
    }

    /**
     * 获取SKU列表
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function getProductSku( $productId ){
        $result = [];
        $list = $this->redis->sMembers('set:sku:productId:'.$productId);
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $detail = $this->detail($value);
                if (!empty($detail) && $detail['status'] == 1) {
                    $result[] = $detail;
                }
            }
        }
        return $result;
    }

    /**
     * 获取SKU列表
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function detail( $skuId ){
        $skuId = (int)$skuId;
        $detail = $this->redis->hgetall( 'hash:sku:'.$skuId );
        if (empty($detail) || $detail['status'] == 0) {
            return [];
        }
        return $detail;
    }
}