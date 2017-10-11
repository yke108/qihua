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

class InquiryController extends CommonController{

    // 获取询盘列表
    public function getList() {
        $state = I('json.state', -1, 'intval');//-1:全部
        $keyword = I('json.keyword');
        $page = I('json.page', 1, 'intval');
        $pageSize = I('json.pageSize', 20, 'intval');
        $username = I('json.username');
        $startDate = I('json.startDate');
        $endDate = I('json.endDate');

        $inquiry = D('Home/Inquiry');
        $order = D('Home/Order');
        // 搜索参数
        $param = [
            'page'=> $page, 
            'pageSize'=> $pageSize, 
            'keyword'=> $keyword
        ];
        // 状态
        if ($state != -1) {
            $param['state'] = $state;
        }
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
        $ret = $inquiry->lists($param);
        $list = [];
        $user = D('Admin/User');
        foreach ($ret['list'] as $key => $value) {
            // 判断是否已经生成订单
            $orderSign = '0'; // 0-可以生成订单，1-不可以
            $tempId = $order->getOrderIdByInquiryId($value['inquiryId']);
            if (!empty($tempId)) {
                $orderInfo = $order->detail($tempId, ['isSave']);
                if ($orderInfo['isSave'] == 0) {
                    $orderSign = '1';
                }
            }
            // 报价单没生成时不能生成订单
            if ($orderSign == '0') {
                if (!D('Home/Quotes')->lists($value['inquiryId'], true)) {
                    $orderSign = '1';
                }
            }
            // 一条询盘
            $row = [
                'inquiryId'=> $value['inquiryId'],
                'inquirySn'=> $value['inquirySn'],
                'Uid'=> $value['Uid'],
                'addTime'=> date('Y-m-d H:i', $value['addTime']),
                'country'=> $value['country'],
                'skuPrice'=> sprintf('%.2f', $value['skuPrice']),
                'freight'=> sprintf('%.2f', $value['freight']),
                'totalPrice'=> sprintf('%.2f', $value['totalPrice']),
                'username'=> $redis->hGet('hash:member:'.$value['Uid'],'username'),
                'adminName'=> $value['adminId'] == 0 ? '' : $user->getUserName($value['adminId']),
                'state'=> $inquiry->getState($value['state']),
                'orderSign'=> $orderSign,
            ];
            $list[] = $row;
        }
        $result = ['list'=> $list, 'count'=> $ret['count']];
        $this->ajaxReturn($result);
    }

    // 获取询盘详情
    public function getDetail() {
        $inquiryId = I('json.inquiryId', 0, 'intval');
        if (empty($inquiryId)) exit;

        $result = [];

        $inquiry = D('Home/Inquiry');
        $quotes = D('Home/quotes');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail)) {
            throw new CsException("Inquiry does not exist", 400);
        }
        $inquiryInfo = [
            'inquiryId'=> $detail['inquiryId'],
            'inquirySn'=> $detail['inquirySn'],
            'addTime'=> date('Y-m-d H:i', $detail['addTime']),
            'transport'=> $detail['transport'],
            'payway'=> $detail['payway'],
            'provision'=> $detail['provision'],
            'transportTip'=> $inquiry->getTransport($detail['transport']),
            'paywayTip'=> $inquiry->getPayway($detail['payway']),
            'provisionTip'=> $inquiry->getProvision($detail['provision']),
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
                'weightUnit'=> D('Home/Product')->getProductWeightUnit($v['weightUnit']),
                'skuNumber'=> $v['skuNumber'],
                'inquiryTotalPrice'=> sprintf('%.2f', $v['inquiryTotalPrice']),
            ];
        }
        $result['inquiryInfo'] = $inquiryInfo;
        // 报价单
        $list = $quotes->lists($inquiryId);
        $quotesList = [];
        foreach ($list as $key => $value) {
            $info = [
                'quotesId'=> $value['quotesId'],
                'quotesSn'=> $value['quotesSn'],
                'inquiryId'=> $value['inquiryId'],
                'addTime'=> date('Y-m-d H:i', $value['addTime']),
                'transport'=> $value['transport'],
                'payway'=> $value['payway'],
                'provision'=> $value['provision'],
                'transportTip'=> $inquiry->getTransport($value['transport']),
                'paywayTip'=> $inquiry->getPayway($value['payway']),
                'provisionTip'=> $inquiry->getProvision($value['provision']),
                'skuPrice'=> sprintf('%.2f', $value['skuPrice']),
                'freight'=> sprintf('%.2f', $value['freight']),
                'totalPrice'=> sprintf('%.2f', $value['totalPrice']),
                'productList'=> [],
            ];
            foreach ($value['productList'] as $k => $v) {
                $info['productList'][] = [
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
            $quotesList[] = $info;
        }
        $result['quotesList'] = $quotesList;
        // 最新的一条报价单
        $quotesInfo = $inquiryInfo;
        if (!empty($quotesList)) {
            $quotesInfo = $quotesList[0];
        }
        $result['quotesInfo'] = $quotesInfo;

        // 配置
        $result['transports'] = $inquiry->getTransport();
        $result['payways'] = $inquiry->getPayway();
        $result['provisions'] = $inquiry->getProvision();
        $this->ajaxReturn($result);
    }

    // 提交询盘
    public function postQuotes() {
        // 接收参数
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $payway = I('json.payway', 1, 'intval');
        $provision = I('json.provision', 1, 'intval');
        $transport = I('json.transport', 1, 'intval');
        $freight = I('json.freight', 0, 'floatval');
        $freight = $freight < 0 ? 0 : $freight;
        $skuList = I('json.productList');

        $inquiry = D('Home/Inquiry');
        // 判断参数是否合法
        $paywayArr = $inquiry->getPayway();
        if (!isset($paywayArr[$payway])) {
            throw new CsException("Payway is error", 400);
        }
        $provisionArr = $inquiry->getProvision();
        if (!isset($provisionArr[$provision])) {
            throw new CsException("Provision is error", 400);
        }
        $transportArr =$inquiry->getTransport();
        if (!isset($transportArr[$transport])) {
            throw new CsException("Transport is error", 400);
        }
        if (!is_array($skuList) || empty($skuList)) {
            throw new CsException("Product is empty", 400);
        }
        $redis = new Redis();
        if ($inquiryId < 1 || !$redis->exists('hash:inquiry:'.$inquiryId)) {
            throw new CsException("Inquiry is error", 400);
        }

        // 询盘商品
        $inquiryProduct = [];
        $skuModel = D('Home/Sku');
        $productModel = D('Home/Product');
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
                'image'=> unserialize($productInfo['images'])[0],
                'price'=> $skuInfo['price'],
                'inquiryPrice'=> $inquiryPrice,
                'inquiryTotalPrice'=> bcmul($inquiryPrice, $skuNumber, 2),
                'moq'=> $skuInfo['moq'],
                'packWeight'=> $skuInfo['packWeight'],
                'weightUnit'=> $skuInfo['weightUnit'],
                'addTime'=> $nowTime,
            ];
        }
        $inquiryInfo = [
            'inquiryId'=> $inquiryId,
            'skuPrice'=> $skuPrice,
            'freight'=> $freight,
            'totalPrice'=> bcadd($skuPrice, $freight),
            'provision'=> $provision,
            'payway'=> $payway,
            'transport'=> $transport,
            'addTime'=> $nowTime,
        ];
        $quotesModel = D('Home/Quotes');
        $quotesId = $quotesModel->insertQuotes($inquiryInfo);
        $quotesModel->insertProduct($inquiryProduct, $quotesId);
        // 插入系统信息
        $ret = $inquiry->insertMessage([
            'inquiryId'=> $inquiryId,
            'type'=> 3,
            'Uid'=> session('userid'),
            'username'=> session('username'),
            'content'=> 'The quotes has been updated, please check',
            'addTime'=> time(),
        ]);
        $this->ajaxReturn('success');
    }

    // 获取聊天记录
    public function getMessageList() {
        $inquiryId = I('json.inquiryId', 0, 'intval');
        if ($inquiryId < 1) {
            throw new CsException("Parameter error", 400);
        }
        $inquiry = D('Home/Inquiry');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail)) {
            throw new CsException("Inquiry does not exist", 400);
        }
        $data = $inquiry->getMessageList($inquiryId);
        foreach ($data as $key => $value) {
            $data[$key]['addTime'] = date('Y-m-d H:i', $value['addTime']);
        }
        $this->ajaxReturn(['data'=> $data]);
    }

    // 发送询盘信息
    public function sendMessage() {
        $inquiryId = I('json.inquiryId', 0, 'intval');
        $content = I('json.content');
        if ($inquiryId < 1 || empty($content)) {
            throw new CsException("Parameter error", 400);
        }
        $inquiry = D('Home/Inquiry');
        $detail = $inquiry->detail($inquiryId);
        if (empty($detail)) {
            throw new CsException("Inquiry does not exist", 400);
        }
        $param = [
            'inquiryId'=> $inquiryId,
            'type'=> 2,
            'Uid'=> session('userid'),
            'username'=> session('username'),
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