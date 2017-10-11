<?php
// +----------------------------------------------------------------------
// | Keywa Inc.
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.keywa.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: vii
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Page;
use Common\Basic\CsException;


class OrderController extends CommonController{

    public function writeOrder() {
        $this->display('write-order');
    }

    // 填写订单表单的数据，优先级：orderId->inquiryId->skuId,productNumber
    public function ajaxWriteOrder() {
    	// 接参
    	$skuId = I('json.skuId', 0, 'intval');
        $productNumber = I('json.productNumber', 0, 'intval');
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $orderId = I('json.orderId', 0, 'intval');
        $order = D('Home/Order');

        // 询盘下单已经有保存订单的情况，再求保存的orderId
        if ($orderId == 0 && $inquiryId > 0) {
        	$orderId = $order->getOrderIdByInquiryId($inquiryId);
        	$orderId = $orderId ? $orderId : 0;
        }

        // 预定义输出结果
        $result = [];
		$productList = [];
		if ($orderId > 0) {
			// 保存的订单
			$orderInfo = $order->detail($orderId);
			if (empty($orderInfo) || $orderInfo['Uid'] != $this->uid) {
				throw new CsException("The order does not exist");
			}
			if ($orderInfo['isSave'] == '0') {
				throw new CsException("The order is generated");
			}
			$quotesInfo = D('Home/quotes')->lists($orderInfo['inquiryId'], true);
			$result['orderInfo'] = [
            	'message' => $orderInfo['message'],
				'attachment' => $orderInfo['attachment'],
				'quotesSn' => !empty($quotesInfo) ? $quotesInfo['quotesSn'] : '',
				'quotesTime' => !empty($quotesInfo) ? date('Y-m-d H:i', $quotesInfo['addTime']) : '',
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
				'orderId' => $orderId,
            ];
            $productList = $order->getProductList($orderId);

            foreach ($productList as $k => $v) {
                $result['productList'][] = [
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
		} elseif ($inquiryId > 0) {
			// 询盘下单
			$inquiryInfo = D('Home/Inquiry')->detail($inquiryId);
			if (empty($inquiryInfo) || $inquiryInfo['Uid'] != $this->uid) {
				throw new CsException("Inquiry does not exist");
			}
			$quotesInfo = D('Home/quotes')->lists($inquiryId, true);
			if (empty($quotesInfo)) {
				throw new CsException("Quotes does not exist");
			}
            $result['orderInfo'] = [
            	'message' => '',
				'attachment' => '',
				'quotesSn' => $quotesInfo['quotesSn'],
				'quotesTime' => date('Y-m-d H:i', $quotesInfo['addTime']),
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
				'freight' => $quotesInfo['freight'],
				'inquiryId' => $inquiryId,
				'orderId' => '0',
            ];
            foreach ($quotesInfo['productList'] as $k => $v) {
                $result['productList'][] = [
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
		} else {
			// 普通下单
			if ($skuId < 1) {
				throw new CsException('Sku is required');
			}
			if ($productNumber < 1) {
				throw new CsException('Number is required');
			}
			$skuInfo = D('Sku')->detail($skuId);
			if (empty($skuInfo)) {
				throw new CsException('Sku does not exist');
			}
			$productInfo = D('Product')->detail(['id'=> $skuInfo['productId']]);
			if (empty($productInfo)) {
				throw new CsException('Product does not exist');
			}
			$result['orderInfo'] = [
				'message' => '',
				'attachment' => '',
				'quotesSn' => '',
				'quotesTime' => '',
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
				'orderId' => '0',
			];
			$result['productList'][] = [
				'skuId'=> $skuInfo['skuId'],
				'productId'=> $skuInfo['productId'],
				'title'=> $productInfo['title'],
				'price'=> sprintf('%.2f', $skuInfo['price']),
				'skuNumber'=> $productNumber,
				'moq'=> $skuInfo['moq'],
				'image'=> picurl(unserialize($productInfo['images'])[0]),
				'weightUnit'=> $skuInfo['weightUnit'],
				'weightUnitTip'=> D('Home/Product')->getProductWeightUnit($skuInfo['weightUnit']),
			];
		}

		$result['paywayArr'] = D('Inquiry')->getPayway();
		$result['provisionArr'] = D('Inquiry')->getProvision();
		$result['transportArr'] = D('Inquiry')->getTransport();
		$result['freightwayArr'] = D('Inquiry')->getFreightway();
		$result['insuranceArr'] = D('Inquiry')->getInsurance();
        $result['sellerInformation'] = C('SELF_SUPPORT_INFO');

		$this->ajaxReturn($result);
    }

    // 提交订单
    public function postOrder() {
        $this->checkLogin();
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
        $orderId = I('json.orderId', 0, 'intval');
        $isSave = I('json.isSave', 0, 'intval');
        $skuList = I('json.skuList');

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
        	if (empty($inquiryInfo) || $inquiryInfo['Uid'] != $this->uid) {
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

        // 验证是不是保存的订单
        if ($orderId > 0) {
        	$orderInfo = $orderModel->detail($orderId);
			if (empty($orderInfo) || $orderInfo['Uid'] != $this->uid) {
				throw new CsException("The order does not exist", 400);
			}
			if ($orderInfo['isSave'] == '0') {
				throw new CsException("The order is generated", 400);
			}
			if ($orderInfo['state'] == $orderModel::CLOSED_STATE) {
				throw new CsException("The order is closed", 400);
			}
			$inquiryId = $orderInfo['inquiryId'];
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
            $orderPrice = sprintf('%.2f', (float)$value['orderPrice']);
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
        $userInfo = D('User/Member')->detail(['id'=> $this->uid]);
        $orderInfo = [
            'orderId'=> $orderId,
            'inquiryId'=> $inquiryId,
            'Uid'=> $this->uid,
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
            'initiator'=> 1,
            'state'=> 0,
            'isSave'=> in_array($isSave, [0,1]) ? $isSave : 0,
            'bconfirm'=> 0,
            'sconfirm'=> 0,
            'status'=> 1,
            'addTime'=> $nowTime,
        ];
        $orderId = $orderModel->insertOrder($orderInfo);
        $orderModel->insertProduct($orderProduct, $orderId);
        $this->ajaxReturn(['orderId'=> $orderId]);
    }

    // 获取订单列表
    public function getOrderList() {
        $this->checkLogin();
        $state = I('json.state', -1, 'intval');
        $isSave = I('json.isSave', -1, 'intval');
        $keyword = I('json.keyword');
        $page = I('json.page', 1, 'intval');
        $pageSize = I('json.pageSize', 4, 'intval');

        $order = D('Home/Order');
        $param = [
        	'Uid'=> $this->uid, 
        	'page'=> $page, 
        	'pageSize'=> $pageSize, 
        	'keyword'=> $keyword
        ];
        // 状态
        if ($state != -1) $param['state'] = $state;
        // 是否保存订单
        if ($isSave != -1) $param['isSave'] = $isSave;

        // 查询 列表
        $ret = $order->lists($param);
        $list = [];
        foreach ($ret['list'] as $key => $value) {
            $row = [
                'orderId'=> $value['orderId'],
                'orderSn'=> $value['orderSn'],
                'addTime'=> date('Y-m-d H:i', $value['addTime']),
                'country'=> $value['country'],
                'username'=> session('memberName'),
                'seller'=> C('SELF_SUPPORT_INFO')['NAME'],
                'isSave'=> $value['isSave'],
                'state'=> $value['state'],
                'isReceipt'=> $value['isReceipt'],
                'stateName'=> $order->getState($value['state']),
                'productList'=> [],
            ];
            // 商品列表
            $productList = $order->getProductList($value['orderId']);
            $tempList = [];
            foreach ($productList as $k => $v) {
                $tempList[] = [
                    'skuId'=> $v['skuId'],
                    'productId'=> $v['productId'],
                    'skuNumber'=> $v['skuNumber'],
                    'title'=> $v['title'],
                    'orderPrice'=> sprintf('%.2f', $v['orderPrice']),
                    'weightUnit'=> D('Product')->getProductWeightUnit($v['weightUnit']),
                    'image'=> picurl($v['image']),
                ];
            }
            $row['productList'] = $tempList;
            $list[] = $row;
        }
        $result = ['list'=> $list, 'count'=> $ret['count']];
        $this->ajaxReturn($result);
    }

    // 关闭订单
    public function closeOrder() {
        $this->checkLogin();
        $orderId = I('json.orderId', 0, 'intval');
        if ($orderId < 1) {
            throw new CsException("Parameter error", 400);
        }
        $order = D('Home/Order');
        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['Uid'] != $this->uid) {
            throw new CsException("The order does not exist", 400);
        }
        if ($detail['state'] != $order::START_STATE) {
            throw new CsException("The current state cannot be closed", 400);
        }
        $ret = $order->updateState($orderId, $order::CLOSED_STATE);
        if (!$ret) {
            throw new CsException("Failed", 400);
        }
        $this->ajaxReturn('Success');
    }

    // 删除订单
    public function deleteOrder() {
        $this->checkLogin();
        $orderId = I('json.orderId');
        if (empty($orderId)) {
            throw new CsException("Parameter error", 400);
        }
        $order = D('Home/Order');
        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['Uid'] != $this->uid) {
            throw new CsException("The order does not exist", 400);
        }
        if ($detail['state'] != $order::CLOSED_STATE) {
            throw new CsException("The current state cannot be deleted", 400);
        }
        $ret = $order->deleteInquiry($orderId);
        if (!$ret) {
            throw new CsException("Failed", 400);
        }
        $this->ajaxReturn('Success');
    }

    // 获取订单详情
    public function getOrderDetail() {
        $this->checkLogin();
        $orderId = I('json.orderId', 0, 'intval');
        if (empty($orderId)) {
        	exit;
        }

        $result = [];
        $order = D('Home/Order');
        $inquiry = D('Home/Inquiry');

        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['Uid'] != $this->uid || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        // 订单信息
        $result['orderInfo'] = [
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
            'bcontract'=> $detail['bcontract'],
            'scontract'=> $detail['scontract'],
            'bconfirm'=> $detail['bconfirm'],
            'sconfirm'=> $detail['sconfirm'],
            'isReceipt'=> $detail['isReceipt'],
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
                'weightUnit'=> D('Product')->getProductWeightUnit($v['weightUnit']),
                'skuNumber'=> $v['skuNumber'],
                'orderTotalPrice'=> sprintf('%.2f', $v['orderTotalPrice']),
            ];
        }

        // 操作日志
        $result['operationRecord'] = [];
        $operationRecord = $order->getOrderStateLogList($orderId);
        foreach ($operationRecord as $v) {
            $result['operationRecord'][] = [
                'id'=> $v['id'],
                'operator'=> $v['type'] == 1 ? 'Buyer' : 'Seller',
                'remark'=> $v['remark'],
                'addTime'=> date('Y-m-d H:i:s', $v['addTime']),
            ];
        }

        $this->ajaxReturn($result);
    }

    /**
     * 上传合同
     */
    public function uploadContract(){
        $this->checkLogin();
        $orderId = I('json.orderId', 0, 'intval');
        $bcontract = I('json.bcontract');
        if (empty($bcontract) || $orderId < 1) {
            throw new CsException("Parameter is error", 400);
        }
        $order = D('Home/Order');
        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['Uid'] != $this->uid || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        if ($detail['state'] != $order::START_STATE) {
            throw new CsException("It cannot be uploaded in this order state", 400);
        }
        $order->editOrder(['bcontract'=> $bcontract], $orderId);
        $this->ajaxReturn('Success');
    }

    // 确认订单
    public function confirmOrder() {
        $this->checkLogin();
        $orderId = I('json.orderId', 0, 'intval');
        $order = D('Home/Order');
        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['Uid'] != $this->uid || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        if ($detail['state'] != $order::START_STATE) {
            throw new CsException("It cannot be uploaded in this order state", 400);
        }
        if ($detail['bconfirm'] == 1) {
            throw new CsException("The order has been confirmed", 400);
        }
        if (empty($detail['scontract'])) {
            throw new CsException("The seller contract has not been uploaded", 400);
        }
        $order->editOrder(['bconfirm'=> '1'], $orderId);
        $logData = [
            'orderId'=> $orderId,
            'type'=> 1,
            'oid'=> $this->uid,
            'username'=> session('memberName'),
            'beforeState'=> $order::START_STATE,
            'afterState'=> $order::START_STATE,
            'remark'=> 'buyer confirmation',
        ];
        // 双方都已经确认，订单进入确认订单状态
        if ($detail['sconfirm'] == '1') {
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

    // 买家确认收货
    public function confirmReceipt() {
        $this->checkLogin();
        $orderId = I('json.orderId', 0, 'intval');
        $order = D('Home/Order');
        $detail = $order->detail($orderId);
        if (empty($detail) || $detail['Uid'] != $this->uid || $detail['isSave'] == 1) {
            throw new CsException("Order does not exist", 400);
        }
        if ($detail['state'] != $order::SHIPPED_STATE || $detail['state'] != $order::COMPLETE_STATE) {
            throw new CsException("It cannot be Receipted in this order state", 400);
        }
        $order->editOrder(['isReceipt'=> '1'], $orderId);
        $logData = [
            'orderId'=> $orderId,
            'type'=> 1,
            'oid'=> $this->uid,
            'username'=> session('memberName'),
            'beforeState'=> $detail['state'],
            'afterState'=> $detail['state'],
            'remark'=> 'The buyer confirms the receipt information',
        ];
        // 插入日志
        $order->addOrderStateLog($logData);
        $this->ajaxReturn('Success');
    }

    public function test() {
    	$order = D('Home/order');
    	$redis = \Think\Cache::getInstance('Redis');

    	print_r($_SERVER);
    }
}