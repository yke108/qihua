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

class InquiryModel extends CommonModel{

	public function __construct(){
	    $this->autoCheckFields = false;
        $this->redis = \Think\Cache::getInstance('Redis');
	}

    /**
     * 获取详情
     * @return array
     */
    public function detail($inquiryId){
        $cacheKey = 'hash:inquiry:'.$inquiryId;
        $ret = $this->redis->hgetall( $cacheKey );
        if (empty($ret) || $ret['status'] != 1) {
            return [];
        }
        return $ret;
    }

    /**
     * 新增询盘
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insertInquiry( $data ){
        $inquiryId = $this->redis->incr('string:inquiry');//获取自增长id
        $data['inquiryId'] = $inquiryId;
        $data['inquirySn'] = $this->makeSn();

        $this->redis->hmset( 'hash:inquiry:'.$inquiryId, $data );
        $this->redis->sadd( 'set:inquiry:Uid:'.$data['Uid'], $data['inquiryId'] );//增加到用户集合
        $this->redis->sadd( 'set:inquiry:adminId:0', $data['inquiryId'] );//增加到未指派集合
        $this->redis->sadd( 'set:inquiry:read:0', $data['inquiryId'] );//增加到未读集合
        $this->redis->sadd( 'set:inquiry:status:1', $data['inquiryId'] );//增加到正常集合
        $this->redis->sadd( 'set:inquiry:state:0', $data['inquiryId'] );//增加到待处理集合
        $this->redis->zAdd( 'zset:inquiry:addTime', $data['addTime'], $data['inquiryId'] );
        $this->redis->set( 'string:inquiry:inquirySn:'.$data['inquirySn'], $data['inquiryId'] );

        return $inquiryId;
    }

    /**
     * 新增底盘商品
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function insertProduct( $data, $inquiryId ){
        foreach ($data as $key => $value) {
            $id = $this->redis->incr('string:inquiryProduct');//获取自增长id
            $value['id'] = $id;
            $value['inquiryId'] = $inquiryId;
            $this->redis->hmset( 'hash:inquiryProduct:'.$id, $value );
            $this->redis->sadd( 'set:inquiryProduct:inquiryId:'.$inquiryId, $id );
        }
        return true;
    }

    /**
     * 生成询盘编号
     * @param  array $data 数据array
     * @return array       详细数据
     */
    public function makeSn() {
        $inquirySn = date('Ymd') . rand(10000,99999);
        if ($this->redis->exists('string:inquiry:inquirySn:'.$inquirySn)) {
            return $this->makeSn();
        }
        return $inquirySn;
    }

    /**
     * 获取临时列表缓存 Cachekey
     * @param array $param <pre> array(
    )
     * @return string
     */
    protected function getTempListsCacheKey(){
        return 'tmp:set:inquiry:list:'.uniqid();
    }

    /**
     * 获取询盘列表
     * @param array
     * @return array
     */
    public function lists( $param ){
        $ret    = array();
        $param['page']      = empty( $param['page'] ) ? 1 : intval( $param['page'] );
        $param['pageSize'] = empty( $param['pageSize'] ) ? 10 : intval( $param['pageSize'] );
        $keyword = isset($param['keyword']) ? trim( $param['keyword'] ) : '';

        $offset = ( $param['page'] - 1 ) * $param['pageSize'];
        $limit = $param['pageSize'];

        $tempCacheKey = $this->getTempListsCacheKey();
        $keys = [];

        $keys[]='zset:inquiry:addTime';
        $keys[] = 'set:inquiry:status:1';
        $n=1;
        if( isset($param['Uid']) ){
            $keys[] = 'set:inquiry:Uid:'.$param['Uid'];
            $n++;
        }
        if ( isset($param['read']) ) {
            $keys[] = 'set:inquiry:read:'.$param['read'];
            $n++;
        }
        if ( isset($param['state']) ) {
            $states = explode(',', $param['state']);
            foreach ($states as $state) {
                $unionKeys[]='set:inquiry:state:'.$state;
            }
            $unionTempCacheKey = $this->getTempListsCacheKey();
            $this->redis->zUnion($unionTempCacheKey, $unionKeys);
            $this->redis->expire( $unionTempCacheKey, 60 );
            $keys[]=$unionTempCacheKey;
            $n++;
        }
        if ( isset($param['adminId']) ) {
            $keys[] = 'set:inquiry:adminId:'.$param['adminId'];
            $n++;
        }
        
        if( !empty( $keyword ) ){
            $otherTempCacheKey = $this->getTempListsCacheKey();
            $matchId = $this->redis->get('string:inquiry:inquirySn:'.$keyword);
            if (!empty($matchId)) {
                $this->redis->sadd( $otherTempCacheKey, $matchId );
            }
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
                $ret['list'][] = $this->redis->hGetAll('hash:inquiry:'.$vo);
            }
        }

        // $ret = ['count'=> 0, 'list'=> []];
        // if( $result && $this->redis->expire( $tempCacheKey, 60 ) ){
        //     $array = array(
        //         'get' => array(
        //             'hash:inquiry:*' => array(
        //                 'inquiryId', 'inquirySn', 'Uid', 'adminId', 'message', 'attachment', 'skuPrice', 'freight','totalPrice','provision', 'payway', 'transport', 'country', 'read', 'state', 'addTime'
        //             )
        //         ),
        //         'limit' => array( $offset, $limit ),
        //         'sort' => $sort,
        //         'by' => $by,
        //     );
        //     $data = $this->getListsByRedisSort( $tempCacheKey, $array );
        //     if( !empty( $data ) ){

        //     }
        //     $count = $this->redis->zCard( $tempCacheKey );
        //     $ret = array(
        //         'count' => $count,
        //         'lists' => $data,
        //     );
        // }
        return $ret;
    }

    // 获取询盘的商品列表
    public function getProductList($inquiryId) {
        $ids = $this->redis->sMembers('set:inquiryProduct:inquiryId:'.$inquiryId);
        if (empty($ids)) {
            return [];
        }
        $list = [];
        foreach ($ids as $key => $id) {
            $list[] = $this->redis->hGetAll('hash:inquiryProduct:'.$id);
        }
        return $list;
    }

    // 更新询盘状态
    public function updateState($inquiryId, $state) {
        $inquiryInfo = $this->detail($inquiryId);
        if (empty($inquiryInfo)) {
            return false;
        }
        $this->redis->sRem('set:inquiry:state:'.$inquiryInfo['state'], $inquiryId);//删除
        $this->redis->sadd( 'set:inquiry:state:'.$state, $inquiryId );//添加
        $this->redis->hmset( 'hash:inquiry:'.$inquiryId, ['state'=> $state] );
        return true;
    }

    // 更新询盘状态
    public function updateRead($detail) {
        if (empty($detail) || $detail['read'] == 1) {
            return false;
        }
        $this->redis->sRem('set:inquiry:read:0', $detail['inquiryId']);//删除
        $this->redis->sadd( 'set:inquiry:read:1', $detail['inquiryId'] );//添加
        $this->redis->hmset( 'hash:inquiry:'.$detail['inquiryId'], ['read'=> 1] );
        return true;
    }

    // 批量删除询盘 array
    public function deleteInquiry($ids) {
        foreach ($ids as $id) {
            $this->redis->sRem( 'set:inquiry:status:1', $id );
            $this->redis->sadd( 'set:inquiry:status:0', $id );
            $this->redis->hmset( 'hash:inquiry:'.$id, ['status'=> 0] );
        }
        return true;
    }

    // 获取询盘状态
    public function getState($state='') {
        $ret = [
            '0' => 'Drafting',
            '1' => 'Seller confirms',
            '2' => 'Ship',
            '3' => 'Completed',
            '4' => 'Closed',
        ];
        if ($state !== '') {
            return $ret[$state];
        }
        return $ret;
    }

    // 获取运输方式
    public function getTransport($param='') {
        if (!empty($param)) {
            return C('TRANSPORT')[$param]['name'];
        }
        return C('TRANSPORT');
    }

    // 获取支付条款
    public function getProvision($param='') {
        if (!empty($param)) {
            return C('PROVISION')[$param]['name'];
        }
        return C('PROVISION');
    }

    // 获取支付方式
    public function getPayway($param='') {
        if (!empty($param)) {
            return C('PAYWAP')[$param]['name'];
        }
        return C('PAYWAP');
    }

    // 获取运费方式
    public function getFreightway($param='') {
        if (!empty($param)) {
            return C('FREIGHTWAY')[$param]['name'];
        }
        return C('FREIGHTWAY');
    }

    // 获取保险方式
    public function getInsurance($param='') {
        if (!empty($param)) {
            return C('INSURANCE')[$param]['name'];
        }
        return C('INSURANCE');
    }

    // 发送询盘聊天
    public function insertMessage($param) {
        $id = $this->redis->incr('string:inquiryMessage');//获取自增长id
        $param['id'] = $id;
        $this->redis->hmset( 'hash:inquiryMessage:'.$id, $param );
        $ret = $this->redis->sadd( 'set:inquiryMessage:inquiryId:'.$param['inquiryId'], $id );
        return $ret;
    }

    // 获取询盘聊天记录
    public function getMessageList($inquiryId, $id=0) {
        $ids = $this->redis->sMembers('set:inquiryMessage:inquiryId:'.$inquiryId);
        if (empty($ids)) {
            return [];
        }
        $idArr = [];
        foreach ($ids as $value) {
            if ($value > $id) {
                $idArr[] = $value;
            }
        }
        if (empty($idArr)) {
            return [];
        }
        $list = [];
        sort($idArr);
        foreach ($idArr as $key => $id) {
            $list[] = $this->redis->hGetAll('hash:inquiryMessage:'.$id);
        }
        return $list;
    }
}