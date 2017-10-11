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

class OrderModel extends CommonModel{

	public function __construct(){
	    $this->autoCheckFields = false;
        $this->redis = \Think\Cache::getInstance('Redis');
	}

    // 定义常量
    CONST START_STATE       = '0';
    CONST CONFIRM_STATE     = '1';
    CONST PAID_STATE        = '2';
    CONST SHIPPED_STATE     = '3';
    CONST COMPLETE_STATE    = '4';
    CONST CLOSED_STATE      = '5';

    // 获取订单状态名称
    public function getState($state='') {
        $ret = [
            self::START_STATE => 'Start Order',
            self::CONFIRM_STATE => 'Order confirmed',
            self::PAID_STATE => 'Certificate confirmation',
            self::SHIPPED_STATE => 'Shipped',
            self::COMPLETE_STATE => 'Complete',
            self::CLOSED_STATE => 'Closed',
        ];
        if ($state !== '') {
            return $ret[$state];
        }
        return $ret;
    }

    /**
     * 获取订单详情
     * @param $orderId 订单ID
     * @param $fields  查询的字段数组
     * @return array
     */
    public function detail($orderId, $fields=[]){
        $cacheKey = 'hash:order:'.$orderId;
        if (empty($fields)) {
            $ret = $this->redis->hgetall( $cacheKey );
        } else {
            $fields[] = 'status';
            $ret = $this->redis->hMGet( $cacheKey, $fields );
        }
        if (empty($ret) || $ret['status'] != 1) {
            return [];
        }
        unset($ret['status']);
        return $ret;
    }

    /**
     * 新增订单
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insertOrder( $data ){
        // 保存的订单
        if (isset($data['orderId']) && !empty($data['orderId'])) {
            $orderId = $data['orderId'];
            $detail = $this->detail($orderId, ['orderSn']);
            $this->redis->del('string:order:orderSn:'.$detail['orderSn']);//删除
            $this->redis->sRem('set:order:isSave:1', $orderId);//删除
        } else {
            $orderId = $this->redis->incr('string:order');//获取自增长id
        }
        $orderSn = $this->makeSn();

        // HASH数据
        $insertData = [
            'orderId'=> $orderId,
            'orderSn'=> $orderSn,
            'inquiryId'=> isset($data['inquiryId']) ? (int)$data['inquiryId'] : 0,
            'Uid'=> isset($data['Uid']) ? (int)$data['Uid'] : 0,
            'inquiryAdminId'=> isset($data['inquiryAdminId']) ? (int)$data['inquiryAdminId'] : 0,
            'followAdminId'=> isset($data['followAdminId']) ? (int)$data['followAdminId'] : 0,
            'skuPrice'=> isset($data['skuPrice']) ? $data['skuPrice'] : 0,
            'freight'=> isset($data['freight']) ? $data['freight'] : 0,
            'totalPrice'=> isset($data['totalPrice']) ? $data['totalPrice'] : 0,
            'provision'=> isset($data['provision']) ? (int)$data['provision'] : 1,
            'provisionNote'=> isset($data['provisionNote']) ? $data['provisionNote'] : '',
            'payway'=> isset($data['payway']) ? (int)$data['payway'] : 1,
            'paywayNote'=> isset($data['paywayNote']) ? $data['paywayNote'] : '',
            'transport'=> isset($data['transport']) ? (int)$data['transport'] : 1,
            'transportNote'=> isset($data['transportNote']) ? $data['transportNote'] : '',
            'freightway'=> isset($data['freightway']) ? (int)$data['freightway'] : 1,
            'freightwayNote'=> isset($data['freightwayNote']) ? $data['freightwayNote'] : '',
            'insurance'=> isset($data['insurance']) ? (int)$data['insurance'] : 1,
            'insuranceNote'=> isset($data['insuranceNote']) ? $data['insuranceNote'] : '',
            'governed'=> isset($data['governed']) ? $data['governed'] : '',
            'country'=> isset($data['country']) ? $data['country'] : '',
            'attachment'=> isset($data['attachment']) ? $data['attachment'] : '',
            'message'=> isset($data['message']) ? $data['message'] : '',
            'invoiceInfo'=> isset($data['invoiceInfo']) ? $data['invoiceInfo'] : '',
            'consigneeInfo'=> isset($data['consigneeInfo']) ? $data['consigneeInfo'] : '',
            'initiator'=> isset($data['initiator']) ? (int)$data['initiator'] : 1,
            'state'=> isset($data['state']) ? (int)$data['state'] : 0,
            'paymentFile'=> isset($data['paymentFile']) ? $data['paymentFile'] : '',
            'shippedFile'=> isset($data['shippedFile']) ? $data['shippedFile'] : '',
            'receiptFile'=> isset($data['receiptFile']) ? $data['receiptFile'] : '',
            'bcontract'=> isset($data['bcontract']) ? $data['bcontract'] : '',
            'scontract'=> isset($data['scontract']) ? $data['scontract'] : '',
            'bconfirm'=> isset($data['bconfirm']) ? (int)$data['bconfirm'] : 0,
            'sconfirm'=> isset($data['sconfirm']) ? (int)$data['sconfirm'] : 0,
            'isReceipt'=> isset($data['isReceipt']) ? (int)$data['isReceipt'] : 0,
            'confirmTime'=> isset($data['confirmTime']) ? (int)$data['confirmTime'] : 0,
            'payTime'=> isset($data['payTime']) ? (int)$data['payTime'] : 0,
            'shipTime'=> isset($data['shipTime']) ? (int)$data['shipTime'] : 0,
            'finishedTime'=> isset($data['finishedTime']) ? (int)$data['finishedTime'] : 0,
            'status'=> isset($data['status']) ? (int)$data['status'] : 1,
            'isSave'=> isset($data['isSave']) ? (int)$data['isSave'] : 0,
            'addTime'=> isset($data['addTime']) ? (int)$data['addTime'] : time(),
        ];

        // 集合数据
        $this->redis->hmset( 'hash:order:'.$orderId, $insertData );
        $this->redis->sadd( 'set:order:Uid:'.$insertData['Uid'], $orderId );//增加到用户集合
        $this->redis->sadd( 'set:order:inquiryAdminId:'.$insertData['inquiryAdminId'], $orderId );//增加到业务员集合
        $this->redis->sadd( 'set:order:followAdminId:0', $orderId );//增加到未指派集合
        $this->redis->sadd( 'set:order:status:1', $orderId );//增加到正常集合
        $this->redis->sadd( 'set:order:state:'.self::START_STATE, $orderId );//增加到状态集合
        $this->redis->sadd( 'set:order:isSave:'.$insertData['isSave'], $orderId );//增加到是否保存订单的集合
        $this->redis->zAdd( 'zset:order:addTime', $insertData['addTime'], $orderId );
        $this->redis->set( 'string:order:orderSn:'.$orderSn, $orderId );
        if ($insertData['inquiryId'] > 0) {
            $this->redis->set( 'string:order:inquiryId:'.$insertData['inquiryId'], $orderId );
        }

        return $orderId;
    }

    /**
     * 新增底盘商品
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insertProduct( $data, $orderId ){
        // 删除旧数据
        $ids = $this->redis->sMembers('set:orderProduct:orderId:'.$orderId);
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->redis->sRem('set:orderProduct:orderId:'.$orderId, $id);//删除
                $this->redis->del('hash:orderProduct:'.$id);
            }
        }
        // 添加
        foreach ($data as $key => $value) {
            $id = $this->redis->incr('string:orderProduct');//获取自增长id
            $value['id'] = $id;
            $value['orderId'] = $orderId;
            $this->redis->hmset( 'hash:orderProduct:'.$id, $value );
            $this->redis->sadd( 'set:orderProduct:orderId:'.$orderId, $id );
        }
        return true;
    }

    /**
     * 生成询盘编号
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function makeSn() {
        $date = date('Ymd');
        $orderSn = 'KW/EXP/'.$date;
        $dt = strtotime(date('Y-m-d')) + 86400 - time();//计算过期时间
        $autoId = $this->redis->incr('string:order:'.$date);//获取自增长id
        $this->redis->expire( 'string:order:'.$date, $dt );
        $orderSn .= str_pad($autoId, 5, '0', STR_PAD_LEFT);
        return $orderSn;
    }

    /**
     * 获取临时列表缓存 Cachekey
     * @param array $param <pre> array(
    )
     * @return string
     */
    protected function getTempListsCacheKey(){
        return 'tmp:set:order:list:'.uniqid();
    }

    /**
     * 获取订单列表
     * @param array (page,pageSize,)
     * @return array
     */
    public function lists( $param ){
        $ret    = array();
        $param['page']      = empty( $param['page'] ) ? 1 : intval( $param['page'] );
        $param['pageSize'] = empty( $param['pageSize'] ) ? 4 : intval( $param['pageSize'] );
        $keyword = isset($param['keyword']) ? trim( $param['keyword'] ) : '';

        $offset = ( $param['page'] - 1 ) * $param['pageSize'];
        $limit = $param['pageSize'];

        $tempCacheKey = $this->getTempListsCacheKey();
        $keys = [];

        $keys[]='zset:order:addTime';
        $keys[] = 'set:order:status:1';
        $n=1;
        if( isset($param['Uid']) ){
            $keys[] = 'set:order:Uid:'.$param['Uid'];
            $n++;
        }
        if ( isset($param['isSave']) ) {
            $keys[] = 'set:order:isSave:'.$param['isSave'];
            $n++;
        }
        if ( isset($param['state']) ) {
            $states = explode(',', $param['state']);
            foreach ($states as $state) {
                $unionKeys[]='set:order:state:'.$state;
            }
            $unionTempCacheKey = $this->getTempListsCacheKey();
            $this->redis->zUnion($unionTempCacheKey, $unionKeys);
            $this->redis->expire( $unionTempCacheKey, 60 );
            $keys[]=$unionTempCacheKey;
            $n++;
        }
        if ( isset($param['inquiryAdminId']) ) {
            $keys[] = 'set:order:inquiryAdminId:'.$param['inquiryAdminId'];
            $n++;
        }
        if ( isset($param['followAdminId']) ) {
            $keys[] = 'set:order:followAdminId:'.$param['followAdminId'];
            $n++;
        }
        
        if( !empty( $keyword ) ){
            $otherTempCacheKey = $this->getTempListsCacheKey();
            $matchId = $this->redis->get('string:order:orderSn:'.$keyword);
            $this->redis->sadd( $otherTempCacheKey, $matchId );
            $keys[] = $otherTempCacheKey;
            $n++;
            $this->redis->expire( $otherTempCacheKey, 60 );
        }
        $result = $this->redis->zInter( $tempCacheKey, $keys );

        $start = isset($param['start']) ? ($param['start']+$n):"-inf";
        $end = isset($param['end']) ? ($param['end']+$n):"+inf";

        $res = $this->redis->zRevRangeByScore($tempCacheKey, $end, $start, ['limit'=>[$offset, $limit]]);
        $count = $this->redis->zCount($tempCacheKey, $start, $end);
        $count = empty($count) ? 0 : $count;
        $ret = ['count'=> $count, 'list'=> []];
        if (!empty($res)) {
            foreach ($res as $vo) {
                $ret['list'][] = $this->redis->hGetAll('hash:order:'.$vo);
            }
        }
        return $ret;
    }

    // 获取订单的商品列表
    public function getProductList($orderId) {
        $ids = $this->redis->sMembers('set:orderProduct:orderId:'.$orderId);
        if (empty($ids)) {
            return [];
        }
        $list = [];
        foreach ($ids as $key => $id) {
            $list[] = $this->redis->hGetAll('hash:orderProduct:'.$id);
        }
        return $list;
    }

    // 更新订单状态
    public function updateState($orderId, $state) {
        $orderInfo = $this->detail($orderId, ['state']);
        if (empty($orderInfo)) {
            return false;
        }
        $this->redis->sRem('set:order:state:'.$orderInfo['state'], $orderId);//删除
        $this->redis->sadd( 'set:order:state:'.$state, $orderId );//添加
        $this->redis->hmset( 'hash:order:'.$orderId, ['state'=> $state] );
        return true;
    }

    // 批量删除订单 array
    public function deleteInquiry($id) {
        if ($this->redis->exists('hash:order:'.$id)) {
            $this->redis->sRem( 'set:order:status:1', $id );
            $this->redis->sadd( 'set:order:status:0', $id );
            $this->redis->hmset( 'hash:order:'.$id, ['status'=> 0] );
            return true;
        } else {
            return false;
        }
    }

    // 根据询盘ID获取订单ID
    public function getOrderIdByInquiryId($inquiryId) {
        return $this->redis->get('string:order:inquiryId:'.$inquiryId);
    }

    // 编辑订单
    public function editOrder($data, $orderId ) {
        return $this->redis->hmset( 'hash:order:'.$orderId, $data );
    }

    /**
     * 新增订单状态日志
     * @param  array $data 数据array
     * @return int       id
     */
    public function addOrderStateLog( $data ){
        $id = $this->redis->incr('string:orderStateLog');//获取自增长id
        // HASH数据
        $insertData = [
            'id'=> $id,
            'orderId'=> $data['orderId'],
            'type'=> $data['type'],
            'oid'=> $data['oid'],
            'username'=> $data['username'],
            'beforeState'=> $data['beforeState'],
            'afterState'=> $data['afterState'],
            'remark'=> $data['remark'],
            'addTime'=> time(),
        ];
        // 集合数据
        $this->redis->hmset( 'hash:orderStateLog:'.$id, $insertData );
        $this->redis->sadd( 'set:orderStateLog:orderId:'.$insertData['orderId'], $id );//增加到用户集合
        return $id;
    }

    /**
     * 获取订单状态日志列表
     * @param  array $orderId 数据array
     * @return array
     */
    public function getOrderStateLogList( $orderId ){
        $ids = $this->redis->sMembers('set:orderStateLog:orderId:'.$orderId);
        if (empty($ids)) {
            return [];
        }
        rsort($ids);
        $list = [];
        foreach ($ids as $key => $id) {
            $list[] = $this->redis->hGetAll('hash:orderStateLog:'.$id);
        }
        return $list;
    }
}