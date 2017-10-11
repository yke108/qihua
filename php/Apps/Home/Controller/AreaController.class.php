<?php
namespace Home\Controller;
use Think\Controller;


class AreaController extends CommonController{
    /**
     * 地区联动 API
     */
	public function areas(){
        $model = D( 'Admin/Area' );
        $id = I('json.id', 0, 'intval' );
        if ($id > 0){
            $ret = $model->getChildArea( $id );
        }else{
            $ret = $model->getArea();
        }
        $list = [];
        if (!empty($ret)) {
            foreach ($ret as $key => $value) {
                $list[] = ['id'=> $value['id'], 'text'=> $value['text']];
            }
        }

        $this->ajaxReturn(['data'=> $list]);
	}

    /**
     * 自动生成地区
     */
    public function autoCreateArea(){
        $model = D( 'Admin/Area' );
        $array = require_once(APP_PATH.'/Common/Conf/country.php');
        $array = $array['country'];
        foreach( $array as $k => $v ){
            $id = $model->areaAdd( array( 'text' => $v, 'short' => $k ) );
        }
        /*
        foreach( $array as $v ){
            $id = $model->areaAdd( array( 'text' => $v['text'] ) );
            if( !empty( $v['children'] ) ){
                foreach( $v['children'] as $v1 ){
                    $id1 = $model->areaAdd( array( 'text' => $v1['text'], 'id' => $id ) );
                    if( !empty( $v1['children'] ) ){
                        foreach( $v1['children'] as $v2 ){
                            $id2 = $model->areaAdd( array( 'text' => $v2['text'], 'id' => $id1 ) );
                        }
                    }
                }
            }
        }
        */

    }

    public function allAreas(){
        $redis = \Think\Cache::getInstance('Redis');
        $idArr = $redis -> sMembers('set:area:status:1');
        $arr=array();
        if($idArr){
            foreach($idArr as $k=>$v){
                $arr[] = $redis->hMGet('hash:area:'.$v, ['id', 'title', 'parentId']);
            }
        }
        $this->ajaxReturn(['data'=> $arr]);
    }
}