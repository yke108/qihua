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
use Think\Cache\Driver\Redis;

class OrderController extends CommonController{

    // 填写订单表单的数据
    public function ajaxWriteOrder() {
    	// 接参
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $orderId = I('json.orderId', 0, 'intval');
        $order = D('Home/Order');
        $redis = new Redis();

        // 预定义输出结果
        $result = [];
		$productList = [];
		if ($inquiryId > 0) {
			// 询盘下单
			$inquiryInfo = D('Home/Inquiry')->detail($inquiryId);
			if (empty($inquiryInfo)) {
				throw new CsException("Inquiry does not exist");
			}
			$quotesInfo = D('Home/quotes')->lists($inquiryId, true);
			if (empty($quotesInfo)) {
				throw new CsException("Quotes does not exist");
			}
            $result['orderInfo'] = [
            	'Uid' => $inquiryInfo['Uid'],
            	'username'=> $redis->hGet('hash:member:'.$inquiryInfo['Uid'],'username'),
            	'message' => '',
				'attachment' => '',
				'payway' => $quotesInfo['payway'],
				'paywayNote' => '',
				'provision' => $quotesInfo['provision'],
				'provisionNote' => '',
				'transport' => $quotesInfo['transport'],
				'transportNote' => '',
				'freightway' => '1',
				'freightwayNote' => '',
				'insurance' => '1',
				'insuranceNote' => '',
				'governed' => C('GOVERNED'),
				'freight' => sprintf('%.2f', $quotesInfo['freight']),
				'inquiryId' => $inquiryId,
				'productList' => [],
            ];
            foreach ($quotesInfo['productList'] as $k => $v) {
                $result['orderInfo']['productList'][] = [
                    'skuId'=> $v['skuId'],
                    'productId'=> $v['productId'],
                    'title'=> $v['title'],
                    'price'=> sprintf('%.2f', $v['inquiryPrice']),
                    'skuNumber'=> $v['skuNumber'],
                    'moq'=> $v['moq'],
                    'image'=> picurl($v['image']),
                    'weightUnit'=> $v['weightUnit'],
                    'weightUnitTip'=> D('Home/Product')->getProductWeightUnit($v['weightUnit']),
                ];
            }
		} elseif ($orderId > 0) {
			$orderInfo = $order->detail($orderId);
			if (empty($orderInfo) || $orderInfo['state'] != $order::START_STATE) {
				throw new CsException("Order does not exist");
			}
			// 编辑订单
			$result['orderInfo'] = [
				'Uid' => $orderInfo['Uid'],
				'orderId' => $orderInfo['orderId'],
				'orderSn' => $orderInfo['orderSn'],
				'username' => $redis->hGet('hash:member:'.$orderInfo['Uid'],'username'),
				'message' => $orderInfo['message'],
				'attachment' => $orderInfo['attachment'],
				'payway' => $orderInfo['payway'],
				'paywayNote' => $orderInfo['paywayNote'],
				'provision' => $orderInfo['provision'],
				'provisionNote' => $orderInfo['provisionNote'],
				'transport' => $orderInfo['transport'],
				'transportNote' => $orderInfo['transportNote'],
				'freightway' => $orderInfo['freightway'],
				'freightwayNote' => $orderInfo['freightwayNote'],
				'insurance' => $orderInfo['insurance'],
				'insuranceNote' => $orderInfo['insuranceNote'],
				'governed' => $orderInfo['governed'],
				'freight' => $orderInfo['freight'],
				'inquiryId' => $orderInfo['inquiryId'],
				'productList' => [],
			];
			$productList = $order->getProductList($orderId);
            foreach ($productList as $k => $v) {
                $result['orderInfo']['productList'][] = [
                    'skuId'=> $v['skuId'],
                    'productId'=> $v['productId'],
                    'title'=> $v['title'],
                    'price'=> sprintf('%.2f', $v['orderPrice']),
                    'skuNumber'=> $v['skuNumber'],
                    'moq'=> $v['moq'],
                    'image'=> picurl($v['image']),
                    'weightUnit'=> $v['weightUnit'],
                    'weightUnitTip'=> D('Home/Product')->getProductWeightUnit($v['weightUnit']),
                ];
            }
		} else {
			// 普通下单
			$result['orderInfo'] = [
				'Uid' => '',
				'username' => '',
				'message' => '',
				'attachment' => '',
				'payway' => '1',
				'paywayNote' => '',
				'provision' => '1',
				'provisionNote' => '',
				'transport' => '1',
				'transportNote' => '',
				'freightway' => '1',
				'freightwayNote' => '',
				'insurance' => '1',
				'insuranceNote' => '',
				'governed' => C('GOVERNED'),
				'freight' => '',
				'inquiryId' => '0',
				'productList' => [],
			];
		}

		$result['paywayArr'] = D('Home/Inquiry')->getPayway();
		$result['provisionArr'] = D('Home/Inquiry')->getProvision();
		$result['transportArr'] = D('Home/Inquiry')->getTransport();
		$result['freightwayArr'] = D('Home/Inquiry')->getFreightway();
		$result['insuranceArr'] = D('Home/Inquiry')->getInsurance();

		$this->ajaxReturn($result);
    }

    // 提交订单
    public function postOrder() {
        // 接收参数
        $message = I('json.message');
        $attachment = I('json.attachment');
        $payway = I('json.payway', 1, 'intval');
        $provision = I('json.provision', 1, 'intval');
        $transport = I('json.transport', 1, 'intval');
        $freightway = I('json.freightway', 1, 'intval');
        $insurance = I('json.insurance', 1, 'intval');

        $paywayNote = I('json.paywayNote');
        $provisionNote = I('json.provisionNote');
        $transportNote = I('json.transportNote');
        $freightwayNote = I('json.freightwayNote');
        $insuranceNote = I('json.insuranceNote');
        $governed = I('json.governed');

        $freight = I('json.freight', 0, 'floatval');
        $freight = $freight < 0 ? 0 : $freight;
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $Uid = I('json.Uid', 0, 'intval');
        $skuList = I('json.productList');
        $orderId = 0;

        $skuModel = D('Home/Sku');
        $productModel = D('Home/Product');
        $inquiryModel = D('Home/Inquiry');
        $orderModel = D('Home/Order');

        // 判断参数是否合法
        $paywayArr = $inquiryModel->getPayway();
        if (!isset($paywayArr[$payway])) {
            throw new CsException("Payway is error", 400);
        }
        $provisionArr = $inquiryModel->getProvision();
        if (!isset($provisionArr[$provision])) {
            throw new CsException("Provision is error", 400);
        }
        $transportArr =$inquiryModel->getTransport();
        if (!isset($transportArr[$transport])) {
            throw new CsException("Transport is error", 400);
        }
        $freightwayArr =$inquiryModel->getFreightway();
        if (!isset($freightwayArr[$freightway])) {
            throw new CsException("Freightway is error", 400);
        }
        $insuranceArr =$inquiryModel->getInsurance();
        if (!isset($insuranceArr[$insurance])) {
            throw new CsException("Insurance is error", 400);
        }
        if (!is_array($skuList) || empty($skuList)) {
            throw new CsException("Product is empty", 400);
        }

        // 验证是不是询盘订单
        if ($inquiryId > 0) {
        	$inquiryInfo = $inquiryModel->detail($inquiryId);
        	if (empty($inquiryInfo)) {
        		throw new CsException("Inquiry does not exist", 400);
        	}
        	// 判断是否有保存的订单
        	$tempId = $orderModel->getOrderIdByInquiryId($inquiryId);
        	if (!empty($tempId)) {
        		$orderInfo = $orderModel->detail($tempId, ['isSave']);
        		if ($orderInfo['isSave'] == 0) {
        			throw new CsException("Inquiry is bound to order", 400);
        		}
        		$orderId = $tempId;
        	}
        }

        // 询盘商品
        $orderProduct = [];
        $nowTime = time();
        $skuPrice = 0;
        foreach ($skuList as $value) {
            $skuNumber = (int)$value['skuNumber'];
            if ($skuNumber < 1) {
                throw new CsException("Product qty is error", 400);
            }
            $orderPrice = sprintf('%.2f', (float)$value['price']);
            $skuPrice = bcadd($skuPrice, bcmul($orderPrice, $skuNumber, 2), 2);
            if ($orderPrice <= 0) {
                throw new CsException("Product price is error", 400);
            }
            $skuInfo = $skuModel->detail($value['skuId']);
            if (empty($skuInfo)) {
                throw new CsException("Product does not exist", 400);
            }
            $productInfo = $productModel->detail(['id'=> $skuInfo['productId']]);
            $orderProduct[] = [
                'skuId'=> $value['skuId'],
                'productId'=> $skuInfo['productId'],
                'skuNumber'=> $skuNumber,
                'title'=> $productInfo['title'],
                'image'=> picurl(unserialize($productInfo['images'])[0]),
                'price'=> $skuInfo['price'],
                'orderPrice'=> $orderPrice,
                'orderTotalPrice'=> bcmul($orderPrice, $skuNumber, 2),
                'moq'=> $skuInfo['moq'],
                'packWeight'=> $skuInfo['packWeight'],
                'weightUnit'=> $skuInfo['weightUnit'],
                'addTime'=> $nowTime,
            ];
        }
        $userInfo = D('User/Member')->detail(['id'=> $Uid]);
        if (empty($userInfo)) {
        	throw new CsException("User does not exist", 400);
        }
        $orderInfo = [
            'orderId'=> $orderId,
            'inquiryId'=> $inquiryId,
            'Uid'=> $Uid,
            'inquiryAdminId'=> isset($inquiryInfo['adminId']) ? $inquiryInfo['adminId'] : 0,
            'followAdminId'=> 0,
            'skuPrice'=> $skuPrice,
            'freight'=> $freight,
            'totalPrice'=> bcadd($skuPrice, $freight, 2),
            'message'=> $message,
            'attachment'=> $attachment,

            'provision'=> $provision,
            'payway'=> $payway,
            'transport'=> $transport,
            'freightway'=> $freightway,
            'insurance'=> $insurance,
            'paywayNote'=> $paywayNote,
            'provisionNote'=> $provisionNote,
            'transportNote'=> $transportNote,
            'freightwayNote'=> $freightwayNote,
            'insuranceNote'=> $insuranceNote,
            'governed'=> $governed,

            'country'=> $userInfo['country'],
            'invoiceInfo'=> '',
            'consigneeInfo'=> '',
            'initiator'=> 2,
            'state'=> $orderModel::START_STATE,
            'isSave'=> 0,
            'bconfirm'=> 0,
            'sconfirm'=> 0,
            'status'=> 1,
            'addTime'=> $nowTime,
        ];
        $orderId = $orderModel->insertOrder($orderInfo);
        $orderModel->insertProduct($orderProduct, $orderId);
        $this->ajaxReturn(['orderId'=> $orderId]);
    }

    // 编辑订单
    public function editOrder() {
        // 接收参数
        $message = I('json.message');
        $attachment = I('json.attachment');
        $payway = I('json.payway', 1, 'intval');
        $provision = I('json.provision', 1, 'intval');
        $transport = I('json.transport', 1, 'intval');
        $freightway = I('json.freightway', 1, 'intval');
        $insurance = I('json.insurance', 1, 'intval');

        $paywayNote = I('json.paywayNote');
        $provisionNote = I('json.provisionNote');
        $transportNote = I('json.transportNote');
        $freightwayNote = I('json.freightwayNote');
        $insuranceNote = I('json.insuranceNote');
        $governed = I('json.governed');

        $freight = I('json.freight', 0, 'floatval');
        $freight = $freight < 0 ? 0 : $freight;
        $orderId = I('json.orderId', 0, 'intval');
        $skuList = I('json.productList');

        $skuModel = D('Home/Sku');
        $productModel = D('Home/Product');
        $inquiryModel = D('Home/Inquiry');
        $orderModel = D('Home/Order');

        $orderInfo = $orderModel->detail($orderId, ['state']);
		if (empty($orderInfo) || $orderInfo['state'] != $orderModel::START_STATE) {
			throw new CsException("Order does not exist", 400);
		}

        // 判断参数是否合法
        $paywayArr = $inquiryModel->getPayway();
        if (!isset($paywayArr[$payway])) {
            throw new CsException("Payway is error", 400);
        }
        $provisionArr = $inquiryModel->getProvision();
        if (!isset($provisionArr[$provision])) {
            throw new CsException("Provision is error", 400);
        }
        $transportArr =$inquiryModel->getTransport();
        if (!isset($transportArr[$transport])) {
            throw new CsException("Transport is error", 400);
        }
        $freightwayArr =$inquiryModel->getFreightway();
        if (!isset($freightwayArr[$freightway])) {
            throw new CsException("Freightway is error", 400);
        }
        $insuranceArr =$inquiryModel->getInsurance();
        if (!isset($insuranceArr[$insurance])) {
            throw new CsException("Insurance is error", 400);
        }
        if (!is_array($skuList) || empty($skuList)) {
            throw new CsException("Product is empty", 400);
        }

        // 询盘商品
        $orderProduct = [];
        $nowTime = time();
        $skuPrice = 0;
        foreach ($skuList as $value) {
            $skuNumber = (int)$value['skuNumber'];
            if ($skuNumber < 1) {
                throw new CsException("Product qty is error", 400);
            }
            $orderPrice = sprintf('%.2f', (float)$value['price']);
            $skuPrice = bcadd($skuPrice, bcmul($orderPrice, $skuNumber, 2), 2);
            if ($orderPrice <= 0) {
                throw new CsException("Product price is error", 400);
            }
            $skuInfo = $skuModel->detail($value['skuId']);
            if (empty($skuInfo)) {
                throw new CsException("Product does not exist", 400);
            }
            $productInfo = $productModel->detail(['id'=> $skuInfo['productId']]);
            $orderProduct[] = [
                'skuId'=> $value['skuId'],
                'productId'=> $skuInfo['productId'],
                'skuNumber'=> $skuNumber,
                'title'=> $productInfo['title'],
                'image'=> picurl(unserialize($productInfo['images'])[0]),
                'price'=> $skuInfo['price'],
                'orderPrice'=> $orderPrice,
                'orderTotalPrice'=> bcmul($orderPrice, $skuNumber, 2),
                'moq'=> $skuInfo['moq'],
                'packWeight'=> $skuInfo['packWeight'],
                'weightUnit'=> $skuInfo['weightUnit'],
                'addTime'=> $nowTime,
            ];
        }

        $updateData = [
            'skuPrice'=> $skuPrice,
            'freight'=> $freight,
            'totalPrice'=> bcadd($skuPrice, $freight, 2),
            'message'=> $message,
            'attachment'=> $attachment,
            'provision'=> $provision,
            'payway'=> $payway,
            'transport'=> $transport,
            'freightway'=> $freightway,
            'insurance'=> $insurance,
            'paywayNote'=> $paywayNote,
            'provisionNote'=> $provisionNote,
            'transportNote'=> $transportNote,
            'freightwayNote'=> $freightwayNote,
            'insuranceNote'=> $insuranceNote,
            'governed'=> $governed,
        ];
        $orderModel->editOrder($updateData, $orderId);
        $orderModel->insertProduct($orderProduct, $orderId);
        $this->ajaxReturn("Success");
    }

    // 获取订单列表
    public function getOrderList() {
        $state = I('json.state', -1, 'intval');
        $keyword = I('json.keyword');
        $username = I('json.username');
        $page = I('json.page', 1, 'intval');
        $pageSize = I('json.pageSize', 4, 'intval');
        $startDate = I('json.startDate');
        $endDate = I('json.endDate');

        $order = D('Home/Order');
        $param = [
        	'page'=> $page, 
        	'pageSize'=> $pageSize, 
        	'keyword'=> $keyword,
        	'isSave'=> 0,
        ];
        // 状态
        if ($state != -1) $param['state'] = $state;
        // 根据用户名搜索
        $redis = new Redis();
        if (!empty($username)) {
            $Uid = $uid = $redis->get('member:'.$username);
            if ($Uid) {
                $param['Uid'] = $Uid;
            } else {
                $param['Uid'] = -1;
            }
        }
        // 根据时间筛选
        if (!empty($startDate)) {
            $param['start'] = strtotime($startDate);
        }
        if (!empty($endDate)) {
            $param['end'] = strtotime($endDate) + 86399;
        }

        // 查询 列表
        $ret = $order->lists($param);
        $list = [];
        $user = D('Admin/User');
        foreach ($ret['list'] as $key => $value) {
            $row = [
                'orderId'=> $value['orderId'],
                'orderSn'=> $value['orderSn'],
                'Uid'=> $value['Uid'],
                'addTime'=> date('Y-m-d H:i', $value['addTime']),
                'username'=> $redis->hGet('hash:member:'.$value['Uid'],'username'),
                'isSave'=> $value['isSave'],
                'state'=> $value['state'],
                'stateName'=> $order->getState($value['state']),
                'skuPrice'=> sprintf('%.2f', $value['skuPrice']),
                'freight'=> sprintf('%.2f', $value['freight']),
                'totalPrice'=> sprintf('%.2f', $value['totalPrice']),
                'inquiryAdminName'=> $value['inquiryAdminId'] == 0 ? '' : $user->getUserName($value['inquiryAdminId']),
                'followAdminName'=> $value['followAdminId'] == 0 ? '' : $user->getUserName($value['followAdminId']),
            ];
            $list[] = $row;
        }
        $result = ['list'=> $list, 'count'=> $ret['count']];
        $this->ajaxReturn($result);
    }

    // 获取订单详情
    public function getOrderDetail() {

        $orderId = I('json.orderId', 0, 'intval');
        $result = [];
        $order = D('Home/Order');
        $inquiry = D('Home/Inquiry');

        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        $redis = new Redis();
        // 订单信息
        $result['orderInfo'] = [
        	'Uid' => $detail['Uid'],
            'username'=> $redis->hGet('hash:member:'.$detail['Uid'],'username'),
            'orderId'=> $detail['orderId'],
            'orderSn'=> $detail['orderSn'],
            'inquiryId'=> $detail['inquiryId'],
            'skuPrice'=> sprintf('%.2f', $detail['skuPrice']),
            'freight'=> sprintf('%.2f', $detail['freight']),
            'totalPrice'=> sprintf('%.2f', $detail['totalPrice']),
            'transport'=> $detail['transport']==0 ? $detail['transportNote'] : $inquiry->getTransport($detail['transport']),
            'payway'=> $detail['payway']==0 ? $detail['paywayNote'] : $inquiry->getPayway($detail['payway']),
            'provision'=> $detail['provision']==0 ? $detail['provisionNote'] : $inquiry->getProvision($detail['provision']),
            'freightway'=> $detail['freightway']==0 ? $detail['freightwayNote'] : $inquiry->getFreightway($detail['freightway']),
            'insurance'=> $detail['insurance']==0 ? $detail['insuranceNote'] : $inquiry->getInsurance($detail['insurance']),
            'governed'=> $detail['governed'],
            'invoiceInfo'=> !empty($detail['invoiceInfo']) ? json_decode($detail['invoiceInfo'], true) : [],
            'consigneeInfo'=> !empty($detail['consigneeInfo']) ? json_decode($detail['consigneeInfo'], true) : [],
            'message'=> $detail['message'],
            'attachment'=> $detail['attachment'],
            'state'=> $detail['state'],
            'stateName'=> $order->getState($detail['state']),
            'paymentFile'=> $detail['paymentFile'],
            'shippedFile'=> $detail['shippedFile'],
            'receiptFile'=> $detail['receiptFile'],
            'bcontract'=> $detail['bcontract'],
            'scontract'=> $detail['scontract'],
            'bconfirm'=> $detail['bconfirm'],
            'sconfirm'=> $detail['sconfirm'],
            'addTime'=> date('Y-m-d H:i:s', $detail['addTime']),
            'confirmTime'=> $detail['confirmTime'] > 0 ? date('Y-m-d H:i:s', $detail['confirmTime']) : '',
            'payTime'=> $detail['payTime'] > 0 ? date('Y-m-d H:i:s', $detail['payTime']) : '',
            'shipTime'=> $detail['shipTime'] > 0 ? date('Y-m-d H:i:s', $detail['shipTime']) : '',
            'finishedTime'=> $detail['finishedTime'] > 0 ? date('Y-m-d H:i:s', $detail['finishedTime']) : '',
            'productList'=> [],
        ];

        $productList = $order->getProductList($orderId);
        foreach ($productList as $v) {
            $result['productList'][] = [
                'skuId'=> $v['skuId'],
                'productId'=> $v['productId'],
                'title'=> $v['title'],
                'image'=> picurl($v['image']),
                'orderPrice'=> sprintf('%.2f', $v['orderPrice']),
                'weightUnit'=> D('Home/Product')->getProductWeightUnit($v['weightUnit']),
                'skuNumber'=> $v['skuNumber'],
                'orderTotalPrice'=> sprintf('%.2f', $v['orderTotalPrice']),
            ];
        }
        $this->ajaxReturn($result);
    }

    /**
     * 资料下载
     */
    public function download(){
        $url = I('get.url');
        if (!$url) {
            return;
        }
        $distFile = realpath(dirname(APP_PATH).'/Site');
        $distFile = $distFile . $url;

        ob_end_clean();  //用于清除图片缓存
        if (file_exists($distFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($distFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($distFile));
            readfile($distFile);
            exit;
        }
    }

    /**
     * 上传合同
     */
    public function uploadContract(){
    	$orderId = I('json.orderId', 0, 'intval');
    	$scontract = I('json.scontract');
    	if (empty($scontract) || $orderId < 1) {
            throw new CsException("Parameter is error", 400);
        }
    	$order = D('Home/Order');
    	$detail = $order->detail($orderId);
        if (empty($detail) || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        if ($detail['state'] != $order::START_STATE) {
        	throw new CsException("It cannot be uploaded in this order state", 400);
        }
        $order->editOrder(['scontract'=> $scontract], $orderId);
        $this->ajaxReturn('Success');
    }

    /**
     * 平台确认订单
     */
    public function confirmOrder(){
    	$orderId = I('json.orderId', 0, 'intval');
    	$order = D('Home/Order');
    	$detail = $order->detail($orderId);
        if (empty($detail) || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        if ($detail['state'] != $order::START_STATE) {
        	throw new CsException("It cannot be uploaded in this order state", 400);
        }
        if ($detail['sconfirm'] == 1) {
        	throw new CsException("The order has been confirmed", 400);
        }
        if (empty($detail['scontract'])) {
        	throw new CsException("The seller contract has not been uploaded", 400);
        }
        $order->editOrder(['sconfirm'=> '1'], $orderId);
        $logData = [
        	'orderId'=> $orderId,
            'type'=> 2,
            'oid'=> session('userid'),
            'username'=> session('username'),
            'beforeState'=> $order::START_STATE,
            'afterState'=> $order::START_STATE,
            'remark'=> 'Seller confirmation',
        ];
        // 双方都已经确认，订单进入确认订单状态
        if ($detail['bconfirm'] == '1') {
        	$order->updateState($orderId, $order::CONFIRM_STATE);
        	$order->editOrder(['confirmTime'=> time()], $orderId);
        	$logData['afterState'] = $order::CONFIRM_STATE;
        	// 询盘订单更新询盘状态
        	if ($detail['inquiryId'] > 0) {
        		D('Home/Inquiry')->updateState($detail['inquiryId'], 1);
        	}
        }
        // 插入日志
        $order->addOrderStateLog($logData);
        $this->ajaxReturn('Success');
    }

    /**
     * 修改订单状态
     */
    public function changeOrderState(){
    	$orderId = I('json.orderId', 0, 'intval');
    	$state = I('json.state');
    	$order = D('Home/Order');
    	$detail = $order->detail($orderId);
        if (empty($detail) || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        $logData = [
        	'orderId'=> $orderId,
            'type'=> 2,
            'oid'=> session('userid'),
            'username'=> session('username'),
            'beforeState'=> $detail['state'],
            'afterState'=> $detail['state']+1,
            'remark'=> '',
        ];
        if ($state == $order::PAID_STATE) {
        	if ($detail['state'] != $order::CONFIRM_STATE) {
	        	throw new CsException("It cannot be updated state in this order state", 400);
	        }
	        $logData['remark'] = 'Buyer pays the order';
	        $order->editOrder(['payTime'=> time()], $orderId);
	        // 询盘订单更新询盘状态
        	if ($detail['inquiryId'] > 0) {
        		D('Home/Inquiry')->updateState($detail['inquiryId'], 2);
        	}
        } elseif ($state == $order::SHIPPED_STATE) {
        	if ($detail['state'] != $order::PAID_STATE) {
	        	throw new CsException("It cannot be updated state in this order state", 400);
	        }
	        $logData['remark'] = 'The order has been shipped';
	        $order->editOrder(['shipTime'=> time()], $orderId);
        } elseif ($state == $order::COMPLETE_STATE) {
        	if ($detail['state'] != $order::SHIPPED_STATE) {
	        	throw new CsException("It cannot be updated state in this order state", 400);
	        }
	        $logData['remark'] = 'The seller confirms the receipt information';
	        $order->editOrder(['finishedTime'=> time()], $orderId);
	        // 询盘订单更新询盘状态
        	if ($detail['inquiryId'] > 0) {
        		D('Home/Inquiry')->updateState($detail['inquiryId'], 3);
        	}
        } else {
        	throw new CsException("The state is error", 400);
        }
        $order->updateState($orderId, $state);
        // 插入日志
        $order->addOrderStateLog($logData);
        $this->ajaxReturn('Success');
    }

    /**
     * 上传订单凭证文件
     */
    public function uploadOrderFile(){
    	$orderId = I('json.orderId', 0, 'intval');
    	$file = I('json.file');
    	$field = I('json.field');
    	if (empty($file) || $orderId < 1 || !in_array($field, ['paymentFile', 'shippedFile', 'receiptFile'])) {
            throw new CsException("Parameter is error", 400);
        }
    	$order = D('Home/Order');
    	$detail = $order->detail($orderId);
        if (empty($detail) || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        if ($field == 'paymentFile') {
        	if ($detail['state'] != $order::CONFIRM_STATE) {
	        	throw new CsException("It cannot be uploaded in this order state", 400);
	        }
        } elseif ($field == 'shippedFile') {
        	if ($detail['state'] != $order::PAID_STATE) {
	        	throw new CsException("It cannot be uploaded in this order state", 400);
	        }
        } elseif ($field == 'receiptFile') {
        	if ($detail['state'] != $order::SHIPPED_STATE) {
	        	throw new CsException("It cannot be uploaded in this order state", 400);
	        }
        }
	        
        $order->editOrder([$field=> $file], $orderId);
        $this->ajaxReturn('Success');
    }
}