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

class InquiryController extends CommonController{
	public function ajaxWriteInquiry(){
		$post = I('json.');
		$skuId = intval($post['skuId']);
		$productNumber = intval($post['productNumber']);
		if ($skuId < 1) {
			throw new CsException('Sku is required');
		}
		$skuInfo = D('Sku')->detail($skuId);
		if (empty($skuInfo)) {
			throw new CsException('Product does not exist');
		}
		$unitArr = D('Product')->getProductWeightUnit();
		$goodsInfo = [
			'skuId'=> $skuInfo['skuId'],
			'productId'=> $skuInfo['productId'],
			'price'=> sprintf('%.2f', $skuInfo['price']),
			'productNumber'=> $productNumber,
			'moq'=> $skuInfo['moq'],
			'weightUnit'=> $skuInfo['weightUnit'],
			'weightUnitTip'=> $unitArr[$skuInfo['weightUnit']]['name'],
			'url' => U('Product/detail', ['id'=> $skuInfo['productId']]),
		];
		$productInfo = D('Product')->detail(['id'=> $skuInfo['productId']]);
		if (empty($productInfo)) {
			throw new CsException('Product does not exist');
		}
		$goodsInfo['title'] = $productInfo['title'];
		$goodsInfo['image'] = picurl(unserialize($productInfo['images'])[0]);
		$data = [
			'goodsInfo'=>$goodsInfo,
			'payway'=>D('Inquiry')->getPayway(),
			'provision'=>D('Inquiry')->getProvision(),
			'transport'=>D('Inquiry')->getTransport(),
		];
		$this->ajaxReturn($data);
	}

    /**
     * 询盘 sku 264
     */
	public function writeInquiry(){
        // $this->checkLogin();
        $skuId = I('get.skuId', 0, 'intval');
        $productNumber = I('get.productNumber', 0, 'intval');
        if ($skuId < 1) {
            exit;
        }
        $skuInfo = D('Sku')->detail($skuId);
        if (empty($skuInfo)) {
            $this->error('Product does not exist');
        }
        $unitArr = D('Product')->getProductWeightUnit();
        $goodsInfo = [
            'skuId'=> $skuInfo['skuId'],
            'productId'=> $skuInfo['productId'],
            'price'=> sprintf('%.2f', $skuInfo['price']),
            'productNumber'=> $productNumber,
            'moq'=> $skuInfo['moq'],
            'weightUnit'=> $skuInfo['weightUnit'],
            'weightUnitTip'=> $unitArr[$skuInfo['weightUnit']]['name'],
            'url' => U('Product/detail', ['id'=> $skuInfo['productId']]),
        ];
        $productInfo = D('Product')->detail(['id'=> $skuInfo['productId']]);
        if (empty($productInfo)) {
            $this->error('Product does not exist');
        }
        $goodsInfo['title'] = $productInfo['title'];
        $goodsInfo['image'] = picurl(unserialize($productInfo['images'])[0]);
        // print_r($goodsInfo);exit;
        $this->assign('payway', D('Inquiry')->getPayway());
        $this->assign('provision', D('Inquiry')->getProvision());
        $this->assign('transport', D('Inquiry')->getTransport());
        $this->assign('goodsInfo', $goodsInfo);
        $this->display('write-inquiry');
	}

    // 提交询盘
    public function postInquiry() {
        $this->checkLogin();
        // 接收参数
        $message = I('json.message');
        $attachment = I('json.attachment');
        $payway = I('json.payway', 1, 'intval');
        $provision = I('json.provision', 1, 'intval');
        $transport = I('json.transport', 1, 'intval');
        $freight = I('json.freight', 0, 'floatval');
        $freight = $freight < 0 ? 0 : $freight;
        $skuList = I('json.skuList');

        // 判断参数是否合法
        $paywayArr = D('Inquiry')->getPayway();
        if (!isset($paywayArr[$payway])) {
            throw new CsException("Payway is error", 400);
        }
        $provisionArr = D('Inquiry')->getProvision();
        if (!isset($provisionArr[$provision])) {
            throw new CsException("Provision is error", 400);
        }
        $transportArr =D('Inquiry')->getTransport();
        if (!isset($transportArr[$transport])) {
            throw new CsException("Transport is error", 400);
        }
        if (!is_array($skuList) || empty($skuList)) {
            throw new CsException("Product is empty", 400);
        }

        // 询盘商品
        $inquiryProduct = [];
        $skuModel = D('Sku');
        $productModel = D('Product');
        $nowTime = time();
        $skuPrice = 0;
        foreach ($skuList as $value) {
            $skuNumber = (int)$value['skuNumber'];
            if ($skuNumber < 1) {
                throw new CsException("Product qty is error", 400);
            }
            $inquiryPrice = sprintf('%.2f', (float)$value['inquiryPrice']);
            $skuPrice = bcadd($skuPrice, bcmul($inquiryPrice, $skuNumber, 2), 2);
            if ($inquiryPrice <= 0) {
                throw new CsException("Product price is error", 400);
            }
            $skuInfo = $skuModel->detail($value['skuId']);
            if (empty($skuInfo)) {
                throw new CsException("Product does not exist", 400);
            }
            $productInfo = $productModel->detail(['id'=> $skuInfo['productId']]);
            $inquiryProduct[] = [
                'skuId'=> $value['skuId'],
                'productId'=> $skuInfo['productId'],
                'skuNumber'=> $skuNumber,
                'title'=> $productInfo['title'],
                'image'=> picurl(unserialize($productInfo['images'])[0]),
                'price'=> $skuInfo['price'],
                'inquiryPrice'=> $inquiryPrice,
                'inquiryTotalPrice'=> bcmul($inquiryPrice, $skuNumber, 2),
                'moq'=> $skuInfo['moq'],
                'packWeight'=> $skuInfo['packWeight'],
                'weightUnit'=> $skuInfo['weightUnit'],
                'addTime'=> $nowTime,
            ];
        }
        $userInfo = D('User/Member')->detail(['id'=> $this->uid]);
        $inquiryInfo = [
            'Uid'=> $this->uid,
            'adminId'=> 0,
            'message'=> $message,
            'attachment'=> $attachment,
            'skuPrice'=> $skuPrice,
            'freight'=> $freight,
            'totalPrice'=> bcadd($skuPrice, $freight),
            'provision'=> $provision,
            'payway'=> $payway,
            'transport'=> $transport,
            'country'=> $userInfo['country'],
            'read'=> 0,
            'state'=> 0,
            'status'=> 1,
            'addTime'=> $nowTime,
        ];
        $inquiryId = D('Inquiry')->insertInquiry($inquiryInfo);
        D('Inquiry')->insertProduct($inquiryProduct, $inquiryId);
        $this->ajaxReturn(['inquiryId'=> $inquiryId]);
    }

    // 获取询盘列表
    public function getInquiryList() {
        $this->checkLogin();
        $unread = I('json.unread', 0, 'intval');//1全部，1-未读
        $status = I('json.status', 0, 'intval');//0-全部，1-new inquiry 2- orderer 3- closed
        $keyword = I('json.keyword');
        $page = I('json.page', 1, 'intval');
        $pageSize = I('json.pageSize', 4, 'intval');

        $inquiry = D('Inquiry');
        $param = ['Uid'=> $this->uid, 'page'=> $page, 'pageSize'=> $pageSize, 'keyword'=> $keyword];
        // 未读
        if ($unread == 1) {
            $param['read'] = 0;
        }
        // 状态
        if ($status > 0) {
            switch ($status) {
                case 1: $param['state'] = '0';break;
                case 2: $param['state'] = '1,2,3';break;
                case 3: $param['state'] = '4';break;
            }
        }
        $ret = $inquiry->lists($param);
        $list = [];
        foreach ($ret['list'] as $key => $value) {
            // 一条询盘
            $row = [
                'inquiryId'=> $value['inquiryId'],
                'inquirySn'=> $value['inquirySn'],
                'addTime'=> date('Y-m-d H:i', $value['addTime']),
                'country'=> $value['country'],
                'username'=> session('memberName'),
                'seller'=> C('SELF_SUPPORT_INFO')['NAME'],
                'state'=> $inquiry->getState($value['state']),
                'productList'=> [],
            ];
            // 一条询盘下的商品列表
            $productList = $inquiry->getProductList($value['inquiryId']);
            $tempList = [];
            foreach ($productList as $k => $v) {
                $tempList[] = [
                    'skuId'=> $v['skuId'],
                    'productId'=> $v['productId'],
                    'skuNumber'=> $v['skuNumber'],
                    'title'=> $v['title'],
                    'inquiryPrice'=> sprintf('%.2f', $v['inquiryPrice']),
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

    // 关闭询盘
    public function closeInquiry() {
        $this->checkLogin();
        $inquiryId = I('json.inquiryId', 0, 'intval');
        if ($inquiryId < 1) {
            throw new CsException("Parameter error", 400);
        }
        $inquiry = D('Inquiry');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail) || $detail['Uid'] != $this->uid) {
            throw new CsException("Inquiry does not exist", 400);
        }
        if ($detail['state'] == '4') {
            throw new CsException("The inquiry is closed", 400);
        }
        $ret = $inquiry->updateState($inquiryId, 4);
        if (!$ret) {
            throw new CsException("Failed", 400);
        }
        $this->ajaxReturn('success');
    }

    // 批量删除询盘
    public function deleteInquiry() {
        $this->checkLogin();
        $inquiryId = I('json.inquiryId');
        if (empty($inquiryId)) {
            throw new CsException("Parameter error", 400);
        }
        $inquiry = D('Inquiry');
        $inquiryId = explode(',', $inquiryId);
        $ids = [];
        foreach ($inquiryId as $v) {
            $v = (int)$v;
            $detail = $inquiry->detail($v);
            if (empty($detail) || $detail['Uid'] != $this->uid) {
                continue;
            }
            $ids[] = $v;
        }
        if (empty($ids)) {
            throw new CsException("Inquiry does not exist", 400);
        }
        $ret = $inquiry->deleteInquiry($ids);
        if (!$ret) {
            throw new CsException("Failed", 400);
        }
        $this->ajaxReturn('success');
    }

    // 获取询盘详情
    public function getInquiryDetail() {
        $this->checkLogin();
        $inquiryId = I('json.inquiryId', 0, 'intval');
        if (empty($inquiryId)) exit;

        $result = [];

        $inquiry = D('Inquiry');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail) || $detail['Uid'] != $this->uid) {
            throw new CsException("Inquiry does not exist", 400);
        }
        // 修改未读状态
        $inquiry->updateRead($detail);
        // 底盘信息
        $inquiryInfo = [
            'inquiryId'=> $detail['inquiryId'],
            'inquirySn'=> $detail['inquirySn'],
            'addTime'=> date('Y-m-d H:i', $detail['addTime']),
            'transport'=> $inquiry->getTransport($detail['transport']),
            'payway'=> $inquiry->getPayway($detail['payway']),
            'provision'=> $inquiry->getProvision($detail['provision']),
            'skuPrice'=> sprintf('%.2f', $detail['skuPrice']),
            'freight'=> sprintf('%.2f', $detail['freight']),
            'totalPrice'=> sprintf('%.2f', $detail['totalPrice']),
            'message'=> $detail['message'],
            'attachment'=> $detail['attachment'],
            'state'=> $detail['state'],
            'productList'=> [],
        ];
        $productList = $inquiry->getProductList($inquiryId);
        foreach ($productList as $v) {
            $inquiryInfo['productList'][] = [
                'skuId'=> $v['skuId'],
                'productId'=> $v['productId'],
                'title'=> $v['title'],
                'image'=> picurl($v['image']),
                'inquiryPrice'=> sprintf('%.2f', $v['inquiryPrice']),
                'weightUnit'=> D('Product')->getProductWeightUnit($v['weightUnit']),
                'skuNumber'=> $v['skuNumber'],
                'inquiryTotalPrice'=> sprintf('%.2f', $v['inquiryTotalPrice']),
            ];
        }
        $result['inquiryInfo'] = $inquiryInfo;
        // 卖家信息
        $result['sellerInformation'] = C('SELF_SUPPORT_INFO');
        // 报价单信息
        $quotesInfo = D('Home/quotes')->lists($inquiryId, true);
        $result['quotesInfo'] = ['quotesId'=>0];
        if (!empty($quotesInfo)) {
            $result['quotesInfo'] = [
                'quotesId'=> $quotesInfo['quotesId'],
                'quotesSn'=> $quotesInfo['quotesSn'],
                'inquiryId'=> $quotesInfo['inquiryId'],
                'addTime'=> date('Y-m-d H:i', $quotesInfo['addTime']),
                'transport'=> $inquiry->getTransport($quotesInfo['transport']),
                'payway'=> $inquiry->getPayway($quotesInfo['payway']),
                'provision'=> $inquiry->getProvision($quotesInfo['provision']),
                'skuPrice'=> sprintf('%.2f', $quotesInfo['skuPrice']),
                'freight'=> sprintf('%.2f', $quotesInfo['freight']),
                'totalPrice'=> sprintf('%.2f', $quotesInfo['totalPrice']),
                'productList'=> [],
            ];
            foreach ($quotesInfo['productList'] as $k => $v) {
                $result['quotesInfo']['productList'][] = [
                    'skuId'=> $v['skuId'],
                    'productId'=> $v['productId'],
                    'title'=> $v['title'],
                    'image'=> picurl($v['image']),
                    'inquiryPrice'=> sprintf('%.2f', $v['inquiryPrice']),
                    'weightUnit'=> D('Home/Product')->getProductWeightUnit($v['weightUnit']),
                    'skuNumber'=> $v['skuNumber'],
                    'inquiryTotalPrice'=> sprintf('%.2f', $v['inquiryTotalPrice']),
                ];
            }
        }
        $this->ajaxReturn($result);
    }

    // 获取聊天记录
    public function getMessageList() {
        $this->checkLogin();
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $id = I('json.id', 0, 'intval');
        if ($inquiryId < 1) {
            throw new CsException("Parameter error", 400);
        }
        $inquiry = D('Inquiry');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail) || $detail['Uid'] != $this->uid) {
            throw new CsException("Inquiry does not exist", 400);
        }
        $ret = $inquiry->getMessageList($inquiryId, $id);
        $maxId = 0;
        $data = [];
        $tempList = [];
        foreach ($ret as $key => $value) {
            if ($id > 0 && $value['type'] == '1') {
                continue;
            }
            $date = date('Y-m-d', $value['addTime']);
            $value['addTime'] = date('Y-m-d H:i', $value['addTime']);
            unset($value['inquiryId'], $value['Uid']);
            $tempList[$date][] = $value;
            $maxId = $value['id'] > $maxId ? $value['id'] : $maxId;
        }
        foreach ($tempList as $key => $value) {
            $data[] = ['dateName'=> $key, 'list'=>$value];
        }
        $this->ajaxReturn(['data'=> $data, 'maxId'=> $maxId]);
    }

    // 发送询盘信息
    public function sendMessage() {
        $this->checkLogin();
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $content = I('json.content');
        if ($inquiryId < 1 || empty($content)) {
            throw new CsException("Parameter error", 400);
        }
        $inquiry = D('Inquiry');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail) || $detail['Uid'] != $this->uid) {
            throw new CsException("Inquiry does not exist", 400);
        }
        $param = [
            'inquiryId'=> $inquiryId,
            'type'=> 1,
            'Uid'=> $this->uid,
            'username'=> session('memberName'),
            'content'=> $content,
            'addTime'=> time(),
        ];
        $ret = $inquiry->insertMessage($param);
        if (!$ret) {
            throw new CsException("Failed", 400);
        }
        $this->ajaxReturn('success');
    }
}