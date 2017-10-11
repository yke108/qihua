<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/9/14
 * Time: 9:30
 */

namespace Admin\Controller;
use       Think\Controller;
use Common\Basic\CsException;

class ProductController extends CommonController{
    /*有效的商品列表*/
    public function valid(){
        $this->display();
    }

    /*待审核的商品列表*/
    public function pending(){
        $this->display();
    }

    /*审核不通过的商品列表*/
    public function fail(){
        $this->display();
    }
    /*已撤销的商品列表*/
    public function soldout(){
        $this->display();
    }

    //获取详情页
    public function details(){
        $sell=D('Sell');
        $id=I('get.id');
        if( $id ){
            $arr=$sell->detail($id);
            if( !empty( $arr ) ){
                $states = C( 'PRODUCT.STATE' );
                $expires = C( 'PRODUCT.EXPIRE' );
                $weightUnits = C( 'WEIGHTUNIT' );
                $arr['weightUnitsTip'] = $weightUnits[$arr['weightUnit']];
                if( $arr['currency'] == 1 ){
                    $arr['currencyTip'] = '￥';
                }else{
                    $arr['currencyTip'] = '$';
                }
                $arr['stateTip'] = '';
                $arr['expire'] = isset($arr['expire'])?$arr['expire']:'';
                $arr['expireTip'] = isset($expires[$arr['expire']])?$expires[$arr['expire']]:'';
                switch( $arr['origin_state'] ){
                    case $states['REFUSE']:
                        $arr['stateTip'] = 'Audit not through';
                        break;
                    case $states['ACTIVE']:
                        $arr['stateTip'] = 'valid';
                        $arr['verifyTime'] = isset($arr['verifyTime'])?$arr['verifyTime']:0;
                        $arr['expire'] = isset($arr['expire'])?$arr['expire']:0;
                        $arr['expireTip'] = date( 'Y-m-d H:i:s', ( $arr['verifyTime'] + $arr['expire'] * 24 * 3600 ) );
                        break;
                    case $states['REVIEWING']:
                        $arr['stateTip'] = ' check pending';
                        break;
                    case $states['REVOKE']:
                        $arr['stateTip'] = 'sold out';
                        break;
                    case $states['SELLER_REVOKE']:
                        $arr['stateTip'] = 'Merchants from the shelves';
                        break;
                    case $states['ADMIN_REVOKE']:
                        $arr['stateTip'] = 'The staff from the shelves';
                        break;
                    case $states['SYSTEM_REVOKE']:
                        $arr['stateTip'] = 'System from the shelves';
                        break;
                }
//                $productDepot = D( 'Home/productDepot' )->getDetailByCode( array( 'code' => $arr['productDepotCode'] ) );
//                $arr['productDepot'] = array(
//                    'enName' => $arr['enName'],
//                    'cas' => $arr['cas'],
//                    'productCode' => $arr['productCode'],
//                );
                $arr['priceTip'] = $arr['currencyTip'].$arr['price'].'/'.$arr['weightUnitsTip'];
                $arr['moqTip'] = $arr['moq'].$arr['weightUnitsTip'];
                $arr['inventoryTip'] = '';// $arr['inventory'].$arr['weightUnitsTip'];
                if($arr['inventoryType'] == 1 && $arr['inventoryNum'] == 0){
                    $arr['inventoryTip'] = '有货';
                }elseif($arr['inventoryType'] == 1 && $arr['inventoryNum'] > 0 && $arr['inventory'] == 1){
                    $arr['inventoryTip'] = $arr['inventoryNum'].$arr['weightUnitsTip'];
                }elseif($arr['inventoryType'] == 1 && $arr['inventoryNum'] > 0 && $arr['inventory'] == 0){
                    $arr['inventoryTip'] = '有货';
                }elseif($arr['inventoryType'] == 2){
                    $arr['inventoryTip'] = '缺货';
                }
                $arr['paymentMethodTip'] = '合同约定'/*.'【'.($arr['paymentMethod'] == 1 ?'先货后款':'先款后货').'】'*/;
                $arr['logisticsMethodTip'] = '合同约定';
                if( !empty( $arr['history'] ) ){
                    $history = array();
                    foreach( $arr['history'] as $v ){
                        $history[] = unserialize( $v );
                    }
                    $arr['history'] = $history;
                }
            }
            $this->assign( "vo", $arr );
        }
        $this->display();
    }

    //获取详情页

    public function goodsDetails(){
        $store=D('Sell');
        $id=I('get.id');
        if($id){
            $arr=$store->detail($id);
            if( !empty( $arr ) ){
                $states = C( 'PRODUCT.STATE' );
                $expires = C( 'PRODUCT.EXPIRE' );
                $weightUnits = C( 'WEIGHTUNIT' );
                $arr['weightUnitsTip'] = $weightUnits[$arr['weightUnit']];
                if( $arr['currency'] == 1 ){
                    $arr['currencyTip'] = '￥';
                }else{
                    $arr['currencyTip'] = '$';
                }
                $arr['stateTip'] = '';
                switch( $arr['origin_state'] ){
                    case $states['REFUSE']:
                        $arr['stateTip'] = 'Audit not through';
                        break;
                    case $states['ACTIVE']:
                        $arr['stateTip'] = 'valid';
                        break;
                    case $states['REVIEWING']:
                        $arr['stateTip'] = 'check pending';
                        break;
                    case $states['REVOKE']:
                        $arr['stateTip'] = 'sold out';
                        break;
                    case $states['SELLER_REVOKE']:
                        $arr['stateTip'] = 'Merchants from the shelves';
                        break;
                    case $states['ADMIN_REVOKE']:
                        $arr['stateTip'] = 'The staff from the shelves';
                        break;
                    case $states['SYSTEM_REVOKE']:
                        $arr['stateTip'] = 'System from the shelves';
                        break;
                }
                $productDepot = D( 'Home/productDepot' )->getDetailByCode( array( 'code' => $arr['productDepotCode'] ) );
                $arr['productDepot'] = array(
                    'cnName' => $productDepot['cnName'],
                    'cas' => $productDepot['cas'],
                    'productDepotCode' => $arr['productDepotCode'],
                );
                $arr['priceTip'] = $arr['currencyTip'].$arr['price'].'/'.$arr['weightUnitsTip'];
                $arr['moqTip'] = $arr['moq'].$arr['weightUnitsTip'];
                $arr['inventoryTip'] = $arr['inventory'].$arr['weightUnitsTip'];
                $arr['paymentMethodTip'] = '合同约定'.'【'.($arr['paymentMethod'] == 1 ?'先货后款':'先款后货').'】';
                $arr['logisticsMethodTip'] = '合同约定';
                if( !empty( $arr['history'] ) ){
                    $history = array();
                    foreach( $arr['history'] as $v ){
                        $history[] = unserialize( $v );
                    }
                    $arr['history'] = $history;
                }
            }
            $res['msg']='';
            $res['code']=200;
            $res['data']=$arr;
            $this->ajaxReturn($res);
        }else{
            $res['msg']='异常';
            $res['code']=400;
            $this->ajaxReturn($res);
        }

    }


    /*列表*/
    public function productList(){
        $shell=D('Home/Shell');
        $store=D('Sell');
        $param=I('json.');

        /*接收分页条件*/
        $page = isset($param['page']) && $param['page'] > 0 ? (int)$param['page'] : 1;
        $rows = isset($param['rows']) && $param['rows'] > 0 ? (int)$param['rows'] : 20;
        $offset=($page-1)*$rows;

        //交集key
        $keys = array('set:product:status:1');

        //状态
        $state = isset($param['state']) ? (int)$param['state'] : -1;
        if ($state != -1) {
            $keys[] = "set:product:state:{$state}";
        }

        //分类
        $param['category'] = isset($param['category']) ? (array)$param['category'] : [];
        if(!empty($param['category'])){
            $keys[]=$store->getCategoryKeys($param['category'][count($param['category'])-1]);
        }

        /*获取下架情况的集合*/
        if( !empty( $param['operateStatus'] ) ){
            $keys[] = D( 'Home/Product' )->getStateCacheKey( intval( $param['operateStatus'] ) );
        }

        //关键词搜索
        if(!empty($param['keyword'])){
            /*判断是否为信息编号*/
            $ret=$store->is_Code($param['keyword']);
            if($ret){
                $keys[]=$store->getCodeKeys($param['keyword']);
            }else{
                $keys[]=$shell->search('product:title',strtolower($param['keyword']),'set');
            }
        }

        /*交集之后取最后的列表结果*/
        $count=$store->getCount($keys);
        $listArrKeys=$store->getSinterstore($keys,$offset,$rows);

        if(!empty($listArrKeys)){
            $res['total']=$count;
            $res['rows']=$listArrKeys;
        }else{
            $res['total']=0;
            $res['rows']=0;
        }
        $this->ajaxReturn($res);
    }

    

    /*
     * 单条修改
     * 批量修改
     * 修改用户状态--审核通过*/
    public function examStatus(){
        $id=I('post.');
        $store=D('Sell');
        $uid=$_SESSION['userid'];
        $ret= $store->examStatus($id,$uid);

        if($ret === '2'){
            $res['code']=400;
            $res['msg']='用户企业认证未通过，不能修改';
            $this->ajaxReturn($res);
        }
        if($ret){
            $res['code']=200;
            $res['msg']='操作成功';
            $this->ajaxReturn($res);
        }else{
            $res['code']=400;
            $res['msg']='操作失败';
            $this->ajaxReturn($res);
        }
    }

    /*修改用户状态--审核不通过*/
    public function failStatus(){
        $data=I('post.');
        $store=D('Sell');
        $data=array(
            'id'=>$data['id'],
            'oid'=>$_SESSION['userid'],
            'opera'=>'Audit not through',
            'addTime' => time(),
            'state'=>'0',
            'otype'=>'admin',
            'reason'=>$data['reason']
        );
        $ret= $store->failStatus($data['id'],$data);
        if($ret){
            $res['code']=200;
            $res['msg']='操作成功';
            $this->ajaxReturn($res);
        }else{
            $res['code']=400;
            $res['msg']='操作失败';
            $this->ajaxReturn($res);
        }
    }

    //判断商品仓库状态  $id  商品id
    public function CheckDepotState($id){
        $id=I('id');
        $sell=D('Sell');
        $res=$sell->getDepotState($id);
        if($res!=1){
            $ret['msg']='商品仓库未通过审核';
            $ret['code']='400';
            $this->ajaxReturn($ret);
        }
    }

    /*重审通过*/
    public function rStatus(){
        $data=I('post.');
        $store=D('Sell');
        $uid=$_SESSION['userid'];
        $data=array(
            'id'=>$data['id'],
            'oid'=>$uid,
            'opera'=>'The review by',
            'addTime' => time(),
            'state'=>'1',
            'otype'=>'admin',
            'reason'=>isset($data['reason'])?$data['reason']:''
        );
        $ret= $store->rStatus($data['id'],$data);
        if($ret === '2'){
            $res['code']=400;
            $res['msg']='用户企业认证未通过，不能修改';
            $this->ajaxReturn($res);
        }
        if($ret){
            $res['code']=200;
            $res['msg']='操作成功';
            $this->ajaxReturn($res);
        }else{
            $res['code']=400;
            $res['msg']='操作失败';
            $this->ajaxReturn($res);
        }
    }

    //获取下架情况
    public function getRevoke(){
        $ret = array(
            'code' => 200,
            'msg' => '操作成功',
            'data' => '',
        );
        $states = C( 'PRODUCT.STATE' );
        $masterType = array(
            array( 'text' => '全部下架', 'id' => 0 ),
            array( 'text' => '商家下架', 'id' => $states['SELLER_REVOKE'] ),
            array( 'text' => '工作人员下架', 'id' => $states['ADMIN_REVOKE'] ),
            array( 'text' => '系统下架', 'id' => $states['SYSTEM_REVOKE'] ),
        );
        $ret['data'] = $masterType;
        $this->ajaxReturn( $ret );
    }

    /*批量删除*/
    public function del(){
        $id=I('json.');
        $store=D('Sell');
        $uid=$_SESSION['userid'];
        $ret= $store->del($id,$uid);
        if(!$ret){
            throw new CsException('操作失败', 400);
        }else{
            $this->ajaxReturn('操作成功');
        }
    }

    /*下架*/
    public function changeOff(){
        $data = I('json.');
        $data=array(
            'id'=>$data['id'],
            'oid'=>$_SESSION['userid'],
            'opera'=>'The staff from the shelves',
            'addTime' => time(),
            'state'=>'5',
            'otype'=>'admin',
            'reason'=>$data['reason']
        );
        $ret = D('Sell')->changeOff($data['id'],$data);
        $this->ajaxReturn("操作成功");
    }

    /*上架*/
    public function renewStatus(){
        $data=I('json.');
        $store=D('Sell');
        $uid=$_SESSION['userid'];
        $data=array(
            'id'=>$data['id'],
            'oid'=>$uid,
            'opera'=>'staff-shelves',
            'addTime' => time(),
            'state'=>'1',
            'otype'=>'admin',
            'reason'=>$data['reason']
        );
        $ret= $store->renewStatus($data['id'],$data);
        if ($ret === '2') {
            throw new CsException('用户企业认证未通过，不能修改', 400);
        } elseif ($ret){
            $this->ajaxReturn('操作成功');
        } else {
            throw new CsException('操作失败', 400);
        }
    }

    /**
     * 添加商品
     */
    public function addProduct() {
        $data = I( 'json.' );
        $data['uid'] = 0;// 自营商品
        //得到商品关键指标, 并检测传入的参数是否合理
        $indicatorModel = D('Home/Indicator');
        $keyIndexs = $indicatorModel->search(array('get' => array('hash:product:keyIndex:*->cid')));
        if (!empty($keyIndexs['rows']) && !empty($data['keyIndex'])) {
            $keyIndexKeys = array_column($keyIndexs['rows'], 'cid');
            $kis = [];
            foreach ($data['keyIndex'] as $key => $value) {
                if (in_array($key, $keyIndexKeys, true) && !empty($value)) {
                    $kis[$key] = $value;
                }
            }
            $data['keyIndex'] = !empty($kis) ? json_encode($kis) : '';
        } else {
            $data['keyIndex'] = '';
        }
        // sku数据
        if (!isset($data['skuList']) || !is_array($data['skuList']) || count($data['skuList']) < 1) {
            throw new CsException('请输入规格信息', 400);
        }
        // 重量单位
        $model=D('Data');
        $weightUnit = $model->getWeightUnit();
        $skuList = [];
        for ($i = 0; $i < count($data['skuList']); $i++) {
            if (empty($data['skuList'][$i]['specification'])) {
                throw new CsException('请输入规格名称', 400);
            }
            if ($data['skuList'][$i]['price'] <= 0) {
                throw new CsException('价格不合法', 400);
            }
            if (!in_array($data['skuList'][$i]['inventoryType'], [1,2])) {
                throw new CsException('库存不合法', 400);
            }
            if ($data['skuList'][$i]['moq'] < 1) {
                throw new CsException('最少购买量必须大于0', 400);
            }
            if( isset($data['skuList'][$i]['inventoryNum']) && !empty($data['skuList'][$i]['inventoryNum']) && $data['skuList'][$i]['inventoryNum'] < $data['skuList'][$i]['moq'] ){
                $this->error = 'The inventory quantity should not be less than MOQ.';
                throw new CsException('库存不能少于最低购买量', 400);
            }
            if (!isset($weightUnit[$data['skuList'][$i]['weightUnit']])) {
                throw new CsException('重量单位不合法', 400);
            }
            if ($weightUnit[$data['skuList'][$i]['weightUnit']]['packWeight'] > 0) {
                $data['skuList'][$i]['packWeight'] = $weightUnit[$data['skuList'][$i]['weightUnit']]['packWeight'];
            }
            if ($data['skuList'][$i]['packWeight'] <= 0) {
                throw new CsException('包装重量必须大于0', 400);
            }
            
            $skuList[] = [
                'specification' => htmlspecialchars($data['skuList'][$i]['specification']),
                'price' => (float)$data['skuList'][$i]['price'],
                'inventoryType' => (int)$data['skuList'][$i]['inventoryType'],
                'inventory' => isset($data['skuList'][$i]['inventory']) & in_array($data['skuList'][$i]['inventory'], [1,2]) ? (int)$data['skuList'][$i]['inventory'] : 1,
                'inventoryNum' => isset($data['skuList'][$i]['inventoryNum']) ? (int)$data['skuList'][$i]['inventoryNum'] : 0,
                'moq' => (int)$data['skuList'][$i]['moq'],
                'weightUnit' => (int)$data['skuList'][$i]['weightUnit'],
                'packWeight' => (float)$data['skuList'][$i]['packWeight'],
                'status' => 1,
                'isDefault' => ($i == 0 ? 1 : 0),
                'addTime' => time(),
            ];
        }
        // 第一个sku更新到产品
        $data['price'] = $skuList[0]['price'];
        $data['inventoryType'] = $skuList[0]['inventoryType'];
        $data['inventory'] = $skuList[0]['inventory'];
        $data['inventoryNum'] = $skuList[0]['inventoryNum'];
        $data['moq'] = $skuList[0]['moq'];
        $data['weightUnit'] = $skuList[0]['weightUnit'];

        // 添加到产品表
        $productModel = D( 'Home/Product' );
        $id = $productModel->insert( $data );
        if( !$id ){
            throw new CsException($productModel->getError(), 400);
        }
        // 添加到SKU表
        $skuModel = D( 'Home/Sku' );
        foreach ($skuList as $key => $value) {
            $value['productId'] = $id;
            $skuModel->insert($value);
        }
        $this->ajaxReturn(['id'=> $id]);
    }

    /**
     * 商品详情
     */
    public function getProductInfo() {
        $id = I( 'json.id' );
        $data = D('Home/Product')->detail( array( 'id' => $id ) );
        if( empty( $data ) ){
           throw new CsException('商品不存在', 400);
        }
        $data = array_merge($data, unserialize($data['attribute']));
        unset($data['attribute']);
        $data['images'] = unserialize( $data['images'] );
        $data['keyIndex'] = isset($data['keyIndex']) && !empty($data['keyIndex']) ? json_decode( $data['keyIndex'], true ) : [];
        // sku
        $data['skuList'] = D('Home/Sku')->getProductSku($id);
        $data['placeList'] = explode(',', $data['placeList']);
        $data['categoryList'] = explode(',', $data['categoryList']);
        $data['seatList'] = explode(',', $data['seatList']);
        // 品牌
        if (!empty($data['brandId'])) {
            $brandDetail = D('Home/Brand')->detail(['id'=> $data['brandId']]);
            $data['brandId'] = explode(',', $brandDetail['parentList']);
        } else {
            $data['brandId'] = [];
        }
        // 生产商
        if (!empty($data['producerId'])) {
            $brandDetail = D('Home/Producer')->detail(['id'=> $data['producerId']]);
            $data['producerId'] = explode(',', $brandDetail['parentList']);
        } else {
            $data['producerId'] = [];
        }
        $data['detail'] = htmlspecialchars_decode($data['detail']);
        $data['faq'] = htmlspecialchars_decode($data['faq']);
        $result = [
            'title'=> $data['title'],
            'enName'=> $data['enName'],
            'enAlias'=> $data['enAlias'],
            'categoryList'=> $data['categoryList'],
            'producerId'=> $data['producerId'],
            'brandId'=> $data['brandId'],
            'placeList'=> $data['placeList'],
            'seatList'=> $data['seatList'],
            'keyIndex'=> $data['keyIndex'],
            'cas'=> $data['cas'],
            'format'=> $data['format'],
            'character'=> $data['character'],
            'qualityGradeID'=> $data['qualityGradeID'],
            'pack'=> $data['pack'],
            'einecsNO'=> $data['einecsNO'],
            'smell'=> $data['smell'],
            'melting'=> $data['melting'],
            'boiling'=> $data['boiling'],
            'flash'=> $data['flash'],
            'ph'=> $data['ph'],
            'density'=> $data['density'],
            'solubility'=> $data['solubility'],
            'formula'=> $data['title'],
            'model'=> $data['model'],
            'msds'=> $data['msds'],
            'tds'=> $data['tds'],
            'coa'=> $data['coa'],
            'summary'=> $data['summary'],
            'purpose'=> $data['purpose'],
            'condition'=> $data['condition'],
            'images'=> $data['images'],
            'skuList'=> $data['skuList'],
            'detail'=> $data['detail'],
            'faq'=> $data['faq'],
        ];
        $this->ajaxReturn(['data'=> $result]);
    }

    //获取商品操作历史
    public function getGoodsHistories(){
        $id = I( 'json.id' );
        if( empty( $id ) ){
            throw new CsException('商品不存在', 400);
        }
        $data = D( 'Sell' )->getHistory( $id );
        $res = array();
        if( !empty( $data ) ){
            foreach( $data as $v ){
                $history = unserialize( $v );
                switch( $history['otype'] ){
                    case 'system':
                        $operatorTip = '系统';
                        break;
                    case 'seller':
                        $operatorTip = '商家';
                        break;
                    default:
                        $operatorTip = '网站管理员';
                        break;
                }
                $res[] = array(
                    'addTimeTip' => date( 'Y-m-d H:i:s', $history['addTime'] ),
                    'operaTip' => $history['opera'].( empty( $history['reason'] )?'':'【原因:'.$history['reason'].'】' ),
                    'operatorTip' => $operatorTip,
                );
            }
        }
        $this->ajaxReturn(['data'=> $res]);
    }

    /**
     * 修改商品
     */
    public function editProduct() {
        $data = I( 'json.' );
        $id = (int)$data['id'];
        $oldData = D('Home/Product')->detail( array( 'id' => $id ) );
        if (empty($oldData)) {
            throw new CsException('商品不存在', 400);
        }
        $oldData['attribute'] = unserialize( $oldData['attribute'] );
        $oldData['images'] = unserialize( $oldData['images'] );
        $oldData['keyIndex'] = isset($oldData['keyIndex']) && !empty($oldData['keyIndex']) ? json_decode( $oldData['keyIndex'], true ) : [];

        //得到商品关键指标, 并检测传入的参数是否合理
        $indicatorModel = D('Home/Indicator');
        $keyIndexs = $indicatorModel->search(array('get' => array('hash:product:keyIndex:*->cid')));
        if (!empty($keyIndexs['rows']) && !empty($data['keyIndex'])) {
            $keyIndexKeys = array_column($keyIndexs['rows'], 'cid');
            $kis = [];
            foreach ($data['keyIndex'] as $key => $value) {
                if (in_array($key, $keyIndexKeys, true) && !empty($value)) {
                    $kis[$key] = $value;
                }
            }
            $data['keyIndex'] = !empty($kis) ? json_encode($kis) : '';
        } else {
            $data['keyIndex'] = '';
        }
        // sku数据
        if (!isset($data['skuList']) || !is_array($data['skuList']) || count($data['skuList']) < 1) {
            throw new CsException('请输入规格信息', 400);
        }
        // 重量单位
        $model=D('Data');
        $weightUnit = $model->getWeightUnit();
        $skuList = [];
        for ($i = 0; $i < count($data['skuList']); $i++) {
            if (empty($data['skuList'][$i]['specification'])) {
                throw new CsException('请输入规格名称', 400);
            }
            if ($data['skuList'][$i]['price'] <= 0) {
                throw new CsException('价格不合法', 400);
            }
            if (!in_array($data['skuList'][$i]['inventoryType'], [1,2]) || !in_array($data['skuList'][$i]['inventory'], [1,2])) {
                throw new CsException('库存不合法', 400);
            }
            if ($data['skuList'][$i]['moq'] < 1) {
                throw new CsException('最少购买量必须大于0', 400);
            }
            if( isset($data['skuList'][$i]['inventoryNum']) && !empty($data['skuList'][$i]['inventoryNum']) && $data['skuList'][$i]['inventoryNum'] < $data['skuList'][$i]['moq'] ){
                $this->error = 'The inventory quantity should not be less than MOQ.';
                throw new CsException('库存不能少于最低购买量', 400);
            }

            if (!isset($weightUnit[$data['skuList'][$i]['weightUnit']])) {
                throw new CsException('重量单位不合法', 400);
            }
            if ($weightUnit[$data['skuList'][$i]['weightUnit']]['packWeight'] > 0) {
                $data['skuList'][$i]['packWeight'] = $weightUnit[$data['skuList'][$i]['weightUnit']]['packWeight'];
            }
            if ($data['skuList'][$i]['packWeight'] <= 0) {
                throw new CsException('包装重量必须大于0', 400);
            }

            $skuList[] = [
                'skuId' => (int)$data['skuList'][$i]['skuId'],
                'productId' => $id,
                'specification' => htmlspecialchars($data['skuList'][$i]['specification']),
                'price' => (float)$data['skuList'][$i]['price'],
                'inventoryType' => (int)$data['skuList'][$i]['inventoryType'],
                'inventory' => isset($data['skuList'][$i]['inventory']) & in_array($data['skuList'][$i]['inventory'], [1,2]) ? (int)$data['skuList'][$i]['inventory'] : 1,
                'inventoryNum' => isset($data['skuList'][$i]['inventoryNum']) ? (int)$data['skuList'][$i]['inventoryNum'] : 0,
                'moq' => (int)$data['skuList'][$i]['moq'],
                'weightUnit' => (int)$data['skuList'][$i]['weightUnit'],
                'packWeight' => (float)$data['skuList'][$i]['packWeight'],
                'status' => 1,
                'isDefault' => ($i == 0 ? 1 : 0),
                'addTime' => time(),
            ];
        }
        // 第一个sku更新到产品
        $data['price'] = $skuList[0]['price'];
        $data['inventoryType'] = $skuList[0]['inventoryType'];
        $data['inventory'] = $skuList[0]['inventory'];
        $data['inventoryNum'] = $skuList[0]['inventoryNum'];
        $data['moq'] = $skuList[0]['moq'];
        $data['weightUnit'] = $skuList[0]['weightUnit'];

        // 获取商品的旧SKU
        $oldSku = D('Home/Sku')->getProductSku($id);
        foreach ($skuList as $key => $value) {
            $temp = false;
            foreach ($oldSku as $k => $v) {
                if ($v['skuId'] == $value['skuId']) {
                    $skuList[$key]['addTime'] = $v['addTime'];
                    unset($oldSku[$k]);
                    $temp = true;
                    break;
                }
            }
            $skuList[$key]['skuId'] = $temp ? $value['skuId'] : 0;
        }

        // 更新到产品表
        $productModel = D( 'Home/Product' );
        $data['Uid'] = 0;
        $ret = $productModel->edit( $id, $data, $oldData );
        if( !$ret ){
            throw new CsException($productModel->getError(), 400);
        }
        // 更新到SKU表
        $skuModel = D( 'Home/Sku' );
        foreach ($oldSku as $key => $value) {
            $skuModel->del($value);//删除
        }
        foreach ($skuList as $key => $value) {
            if ($value['skuId'] == 0) {
                $skuModel->insert($value);//添加
            } else {
                $skuModel->edit($value);//编辑
            }
        }
        // 更新SKUID到PRODUCT表
        $redis = \Think\Cache::getInstance('Redis');
        $skuIds = $redis->sMembers('set:sku:productId:'.$id);
        $skuId = 0;
        foreach ($skuIds as $key => $value) {
            $ret = $redis->hMGet('hash:sku:'.$value, ['status','isDefault']);
            if ($ret['status'] == 1 && $ret['isDefault'] == 1) {
                $skuId = $value;
                break;
            }
        }
        $redis->hSet('hash:product:'.$id, 'skuId', $skuId);
        $this->ajaxReturn("编辑成功");
    }

    /*获取分类列表*/
    public function getCategory(){
        $model = D('Category');
        $ret = $model->getCategory();
        $res = $this->switchLinkageData($ret);
        $this->ajaxReturn( ['data' => $res] );
    }

    /*获取品牌列表*/
    public function getBrand(){
        $model = D('Brand');
        $ret = $model->getBrand();
        $res = $this->switchLinkageData($ret);
        $this->ajaxReturn( ['data' => $res] );
    }

    /*获取地区列表*/
    public function getArea(){
        $model = D('Area');
        $ret = $model->getAreaAll();
        $res = $this->switchLinkageData($ret);
        $this->ajaxReturn( ['data' => $res] );
    }

    /*输出分类，品牌，地区联动数组*/
    public function getLinkageData(){
        //分类
        $result = [];
        $model = D('Category');
        $ret = $model->getCategory();
        $result['category'] = $this->switchLinkageData($ret);
        //品牌
        $model = D('Brand');
        $ret = $model->getBrand();
        $result['band'] = $this->switchLinkageData($ret);
        //地区
        $model = D('Area');
        $ret = $model->getAreaAll();
        $result['area'] = $this->switchLinkageData($ret);
        $this->ajaxReturn( $result );
    }

    /**
     * 获取商品质量等级
     * @return string
     */
    public function getQualityGrade(){
        $ret = D( 'Home/Product' )->getProductQualityGrade();
        $res = [];
        foreach ($ret as $key => $value) {
            $res[] = ['value'=> (string)$value['value'], 'label'=> $value['enTitle']];
        }
        $this->ajaxReturn( ['data'=> $res] );
    }

    /**
     * 生产商列表
     */
    public function getProducer(){
        $model = D('Producer');
        $ret = $model->getProducer();
        $res = $this->switchLinkageData($ret);
        $this->ajaxReturn( ['data' => $res] );
    }

    /**
     * 经营模式
     */
    public function getModel(){
        $model=D('Companydata');
        $ret = $model->getList('model');
        $res = [];
        foreach ($ret[0]['children'] as $key => $value) {
            $res[] = ['value'=> $value['id'], 'label'=> $value['text']];
        }
        $this->ajaxReturn( ['data' => $res] );
    }

    /**
     * 关键指标
     */
    public function getIndicator(){
        $params = array(
            'by'    => 'hash:product:keyIndex:*->id',
            'sort'  => 'desc',
            'get'   => array('hash:product:keyIndex:*->cid', 'hash:product:keyIndex:*->name'),
        );
        $res = D('Admin/Indicator')->search($params)['rows'];
        $this->ajaxReturn( ['data' => $res] );
    }

    /**
     * 重量单位列表
     */
    public function getWeightUnit(){
        $model=D('Data');
        $ret = $model->getWeightUnit();
        $this->ajaxReturn( ['data' => array_values($ret)] );
    }

    /*转换联动数组格式*/
    private function switchLinkageData ($data) {
        $res = [];
        foreach ($data as $key => $value) {
            $res[$key]['value'] = $value['id'];
            $res[$key]['label'] = $value['text'];
            foreach ($value['children'] as $kk => $val) {
                $res[$key]['children'][$kk]['value'] = $val['id'];
                $res[$key]['children'][$kk]['label'] = $val['text'];
                foreach ($val['children'] as $k => $v) {
                    $res[$key]['children'][$kk]['children'][$k]['value'] = $v['id'];
                    $res[$key]['children'][$kk]['children'][$k]['label'] = $v['text'];
                }
            }
        }
        return $res;
    }
} 