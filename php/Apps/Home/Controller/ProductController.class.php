<?php
// +----------------------------------------------------------------------
// | Keywa Inc.
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.keywa.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: vii
// +----------------------------------------------------------------------
namespace Home\Controller;

use Think\Controller;
use Think\Page;
use Common\Basic\CsException;

class ProductController extends CommonController{
	
	/**
	 * 商品列表
	 */
	public function index() {
		$this->assign('get', I('get.'));
		$this->assign('url', C('KW_DOMAIN').'product/search');
		$this->assign('url_search_data', C('KW_DOMAIN').'product/getSearchData');
		
		$this->assign('page_title', '商品搜索');
		$this->display('goods_list');
	}
	
    /**
     * 商城首页
     */
	/* public function index(){

        $model = D( 'Home/Product' );
        $state = $model->getProductState();
        $currency = $model->getProductCurrency();
        $weightUnit = $model->getProductWeightUnit();

        //获取参数
        $page = empty( I( 'p' ) ) ? 1 : intval( I( 'p' ) );
        $pageSize = 20;
        $categoryFirst = intval( I( 'categoryFirst' ) );
        $categorySecond = intval( I( 'categorySecond' ) );
        $categoryThird = intval( I( 'categoryThird' ) );

        $brandIds = trim( urldecode( I( 'brandIds' ) ) );
        $countryIds = trim( urldecode( I( 'countryIds' ) ) );
        $companyModelIds = trim( urldecode( I( 'companyModels' ) ) );

        $sort = intval( I( 'by' ) );
        $minPrice = floatval( I( 'minPrice' ) );
        $maxPrice = floatval( I( 'maxPrice' ) );
        $keyword = trim( urldecode( I( 'keyword' ) ) );
        if(!empty($_GET['keyword'])){
            $_GET['keyword'] = trim( urldecode( $_GET['keyword'] ) );
        }



	    //获取热门商品
        if( empty( $keyword ) ){
            $param = array(
                'state' => $state['ACTIVE']['value'],
                'page_size' => 16,
            );
            $hotProducts = $model->lists( $param );
            
            $hotProducts = empty($hotProducts['lists'])?array():$hotProducts['lists'];
            if( !empty( $hotProducts ) ){
                foreach( $hotProducts as $k => $v9 ){
                    $hotProducts[$k]['weightUnitTip'] = $weightUnit[$v9['weightUnit']]['enTitle'];
                    $hotProducts[$k]['priceTip'] = (empty($currency[$v9['currency']]['character']) ? '' : $currency[$v9['currency']]['character']).sprintf( '%.2f', $v9['price'] );
                    $hotProducts[$k]['images'] = unserialize( $v9['images'] );
                }
            }
            $this->assign( 'hotProducts', $hotProducts );
        }

        //获取分类
        $categories = D( 'Home/Category' )->getCategory();
        $this->assign( 'categories', $categories );

        //获取品牌名
        $brands = D( 'Home/Brand' )->getHotLists( array( 'page_size' => 10000 ) );
        $this->assign( 'brands', $brands );

        //获取经营模式
        $companyModels = D( 'Admin/Companydata' )->getList( 'model' );
        $companyModels = $companyModels[0]['children'];
        $this->assign( 'companyModels', $companyModels );

        //获取国家
        $countries = D( 'Admin/Area' )->getArea();
        $this->assign( 'countries', $countries );

        //商品列表
        $param = array(
            'p' => $page,
            'page_size' => $pageSize,
            'state' => $state['ACTIVE']['value'],
        );
        if( !empty( $keyword ) ){
            $param['keyword'] = $keyword;
        }
        $param['category'] = 0;
        $categoryParam = array( 'categoryThird', 'categorySecond', 'categoryFirst' );
        foreach( $categoryParam as $v1 ){
            if( !empty( $$v1 ) ){
                $param['category'] = $$v1;
                break;
            }
        }
        if( !empty( $brandIds ) ){
            if( is_string( $brandIds ) ){
                $brandIds = explode( ',', $brandIds );
            }
            $param['brandIds'] = $brandIds;
            $this->assign( 'selectBrand', $param['brandIds'] );
        }
        
        if( !empty( $countryIds ) ){
            if( is_string( $countryIds ) ){
                $countryIds = explode( ',', $countryIds );
            }
            $param['countryIds'] = $countryIds;
            $this->assign( 'selectCountries', $param['countryIds'] );
        }
        
        if( !empty( $companyModelIds ) ){
            if( is_string( $companyModelIds ) ){
                $companyModelIds = explode( ',', $companyModelIds );
            }
            $param['models'] = $companyModelIds;
            $this->assign( 'selectModels', $param['models'] );
        }
        

        switch( $sort ){
            case 1:
                $param['by'] = 'hash:product:*->addTime';
                break;
            case 2:
                $param['by'] = 'hash:product:*->price';
                break;
            default:
                $param['by'] = 'hash:product:*->addTime';
                break;
        }
        $param['sort'] = 'desc';
        if( $minPrice > 0 ){
            $param['minPrice'] = $minPrice;
        }
        if( $maxPrice > 0 ){
            $param['maxPrice'] = $maxPrice;
        }
        $products = $model->lists( $param );
        $count = empty($products['count'])?0:$products['count'];
        $products = empty($products['lists'])?array():$products['lists'];
        if( !empty( $products ) ){
            foreach( $products as &$v ){
                $v['priceTip'] = (empty($currency[$v['currency']]['character']) ? '' : $currency[$v['currency']]['character'].sprintf( '%.2f', $v['price'] ));
                $v['weightUnitTip'] = $weightUnit[$v['weightUnit']]['enTitle'];
                $seatList = explode( ',', $v['seatList'] );
                $v['seat'] = '';
                if( !empty( $seatList ) ){
                    $seat = array();
                    $i = 0;
                    foreach( $seatList as $seatId ){
                        if( $i >= 2 ){
                            break;
                        }
                        $area = D( 'Home/Area' )->detail( array( 'id' => $seatId ) );
                        if( !empty( $area['title'] ) ){
                            $seat[] = $area['title'];
                        }
                        $i++;
                    }
                }
                $seat = array_reverse( $seat );
                $v['firstSeat'] = empty($seat[0])?'':$seat[0];
                $v['seat'] = implode( ' ', $seat );
                $v['images'] = unserialize( $v['images'] );
            }
        }

        if( !empty( $param['category'] ) ){
            $matchCategory = D( 'Home/Category' )->detail( array( 'id' => $param['category'] ) );
            $this->assign( 'category', $matchCategory );
        }
        if( !empty( $param['brandIds'] ) ){
            $matchBrands = array();
            foreach( $param['brandIds'] as $brandId ){
                $matchBrand = D( 'Home/Brand' )->detail( array( 'id' => $brandId ) );
                $matchBrands[] = $matchBrand['title'];
            }
            $this->assign( 'brand', $matchBrands );
        }
        if( !empty( $param['countryIds'] ) ){
            $matchCountries = array();
            foreach( $param['countryIds'] as $countryId ){
                $matchArea = D( 'Home/Area' )->detail( array( 'id' => $countryId ) );
                $matchCountries[] = $matchArea['title'];
            }
            $this->assign( 'area', $matchCountries );
        }
        if( !empty( $param['models'] ) ){
            $matchModels = array();
            foreach( $param['models'] as $modelId ){
                $matchModel = D( 'Home/Brand' )->getModelDetail( array( 'id' => $modelId ) );
                $matchModels[] = $matchModel['title'];
            }
            $this->assign( 'companyModel', $matchModels );
        }

        $priceBetween = '';
        if( $minPrice > 0 || $maxPrice > 0 ){
            if( $minPrice > 0 && $maxPrice > 0 ){
                $priceBetween = $minPrice.'-'.$maxPrice;
            }elseif( $minPrice > 0 ){
                $priceBetween = $minPrice.'-above';
            }elseif( $maxPrice > 0 ){
                $priceBetween = '0-'.$maxPrice;
            }
        }

        $this->assign( 'priceBetween', $priceBetween );
        $pageHtml = new Page( $count, $pageSize );
        $pageHtml->setConfig( 'prev', '<i class="icon-prev"></i>Previous Page' );
        $pageHtml->setConfig( 'next', 'Next Page<i class="icon-next"></i>' );
        $this->assign( 'products', $products );
        $this->assign( 'totalCount', $count );
        $this->assign( 'page', $page );
        $this->assign( 'totalPageCount', ceil( $count / $pageSize ) );
        $this->assign( 'page_html', $pageHtml->show() );
        $this->assign( 'cate', getcategory() );
        $this->display( 'goods-list' );
	} */

    /**
     * 商品搜索
     */
    public function search(){
        $model = D( 'Home/Product' );
        $state = $model->getProductState();
        $currency = $model->getProductCurrency();
        $weightUnit = $model->getProductWeightUnit();

        //获取参数
        $page = empty( I('json.page') ) ? 1 : intval( I('json.page') );
        $p = I('get.p');
        if (isset($p)) {
        	$page = empty( $p ) ? 1 : intval( $p );
        }
        $pageSize = empty( I('json.pageSize') ) ? 12 : intval( I('json.pageSize') );
        $categoryId = intval( I( 'json.categoryId' ) );

        $brandIds = trim( urldecode( I( 'json.brandIds' ) ) );
        $countryIds = trim( urldecode( I( 'json.countryIds' ) ) );
        $companyModelIds = trim( urldecode( I( 'json.companyModelIds' ) ) );

        $sort = intval( I( 'json.by' ) );
        $minPrice = floatval( I( 'json.minPrice' ) );
        $maxPrice = floatval( I( 'json.maxPrice' ) );
        $keyword = trim( urldecode( I( 'json.keyword' ) ) );

        // 搜索条件展示
        //$categoryList = [];
        //$countryList = [];
        //$companyModelList = [];

        //商品列表
        $param = array(
            'p' => $page,
            'page_size' => $pageSize,
            'state' => $state['ACTIVE']['value'],
        );
        if( !empty( $keyword ) ){
            $param['keyword'] = $keyword;
        }
        if ( !empty($categoryId) ) {
            $param['category'] = $categoryId;
            // 分类面包屑
            $ret = D('Category')->detail(['id'=> $categoryId]);
            if (!empty($ret)) {
                $arr = explode(',', $ret['parentList']);
                for ($i=0; $i<(count($arr)-1); $i++) {
                    $ret = D('Category')->detail(['id'=> $arr[$i]]);
                    $categoryList[] = ['id'=> $arr[$i], 'name'=> $ret['title']];
                }
                $categoryList[] = ['id'=> $categoryId, 'name'=> $ret['title']];
            }
        }

        if( !empty( $brandIds ) ){
            if( is_string( $brandIds ) ){
                $brandIds = explode( ',', $brandIds );
            }
            $param['brandIds'] = $brandIds;
        }
        
        if( !empty( $countryIds ) ){
            if( is_string( $countryIds ) ){
                $countryIds = explode( ',', $countryIds );
            }
            $param['countryIds'] = $countryIds;
            // 国家
            foreach ($countryIds as $id) {
                $name = getAreaName($id);
                if (!empty($name)) $countryList[] = ['id'=> $id, 'name'=> $name];
            }
        }

        if( !empty( $companyModelIds ) ){
            if( is_string( $companyModelIds ) ){
                $companyModelIds = explode( ',', $companyModelIds );
            }
            $param['models'] = $companyModelIds;
            // 经常模式
            $ret = D( 'Admin/Companydata' )->getList( 'model' );
            foreach ($ret[0]['children'] as $v) {
                if (in_array($v['id'], $companyModelIds)) $companyModelList[] = ['id'=> $v['id'], 'name'=> $v['text']];
            }
        }

        switch( $sort ){
            case 1:
                $param['by'] = 'hash:product:*->addTime';
                break;
            case 2:
                $param['by'] = 'hash:product:*->price';
                break;
            default:
                $param['by'] = 'hash:product:*->addTime';
                break;
        }
        $param['sort'] = 'desc';
        if( $minPrice > 0 ){
            $param['minPrice'] = $minPrice;
        }
        if( $maxPrice > 0 ){
            $param['maxPrice'] = $maxPrice;
        }
        $res = $model->lists( $param );
        $count = empty($res['count']) ? 0 : $res['count'];
        $products = empty($res['lists'])?array():$res['lists'];
        $list = [];
        if( !empty( $products ) ){
            foreach( $products as $value ){
                $list[] = [
                    'id' => $value['id'],
                    'skuId' => $value['skuId'],
                    'title' => subtext($value['title'], 40),
                    'currency' => empty($currency[$value['currency']]['character']) ? '' : $currency[$value['currency']]['character'],
                    'price' => sprintf('%.2f', $value['price']),
                    'weightUnit' => $weightUnit[$value['weightUnit']]['name'],
                    'thumb' => $value['images'] ? C('KW_DOMAIN').unserialize($value['images'])[0] : __ROOT__.'Site/Public/Front/images/placeholder.jpg',
                    'url' => U('detail', array('id'=>$value['id'])),
                ];
            }
        }
        
        //分页
        $pageHtml = new Page( $count, $pageSize );
        $pageHtml->setConfig( 'prev', '<i class="icon-prev"></i>Previous Page' );
        $pageHtml->setConfig( 'next', 'Next Page<i class="icon-next"></i>' );
        $page_html = $pageHtml->show();
        
        $this->ajaxReturn(['list'=> $list, 'count'=> $count, 'page_html'=> $page_html, 'categoryList'=> $categoryList, 'countryList'=> $countryList, 'companyModelList'=> $companyModelList]);
    }

    /**
     * 获取商品搜索的参数数据
     */
    public function getSearchData(){
        $result = [];
        $model = D( 'Home/Product' );
        $state = $model->getProductState();
        $currency = $model->getProductCurrency();
        $weightUnit = $model->getProductWeightUnit();

        //获取分类
        $cateList = D( 'Home/Category' )->getCategory();
        $categories = [];
        foreach ($cateList as $value){
            $cate = ['id'=> $value['id'], 'text'=> $value['text']];
            foreach ($value['children'] as $v1) {
                $cate1 = ['id'=> $v1['id'], 'text'=> $v1['text']];
                foreach ($v1['children'] as $v2) {
                	$url = U('search', array('categoryId'=>$v2['id']));
                    $cate1['children'][] = ['id'=> $v2['id'], 'text'=> $v2['text'], 'url'=> $url];
                }
                $cate['children'][] = $cate1;
            }
            $categories[] = $cate;
        }
        $result['categories'] = $categories;
        unset($cateList, $categories);

        //获取品牌名
        $brands = D( 'Home/Brand' )->getHotLists( array( 'page_size' => 10000 ) );
        foreach ($brands as $key => $value) {
            unset($brands[$key]['productSales'], $brands[$key]['parentId']);
        }
        $result['brands'] = $brands;
        unset($brands);

        //获取经营模式
        $companyModels = D( 'Admin/Companydata' )->getList( 'model' );
        $companyModels = $companyModels[0]['children'];
        foreach ($companyModels as $key => $value) {
            unset($companyModels[$key]['attributes']);
        }
        $result['companyModels'] = $companyModels;
        unset($companyModels);

        //获取国家
        $countries = D( 'Admin/Area' )->getArea();
        foreach ($countries as $key => $value) {
            unset($countries[$key]['state'], $countries[$key]['attributes'], $countries[$key]['short']);
        }
        $result['countries'] = $countries;
        unset($countries);

        $this->ajaxReturn($result);
    }

    /**
     * 商城详情
     */
    /* public function detail(){
        $model = D( 'Home/Product' );
        $id = I( 'id' );
        $product = $model->detail( array( 'id' => $id ) );
        if( empty( $product ) ){
            $this->display( 'Public/goods404' );
            exit();
        }
        $productStates = $model->getProductState();
        $currency = $model->getProductCurrency();
        $weightUnit = $model->getProductWeightUnit();

        if( $product['state'] != $productStates['ACTIVE']['value'] ){
            $this->display( 'Public/goods404' );
            exit();
        }
        
        $product['currencyTip'] =(empty($currency[$product['currency']]['character']) ? '' : $currency[$product['currency']]['character']);// $currency[$product['currency']]['character'];
        $product['priceTip'] = sprintf( '%.2f', $product['price'] );
        $product['weightUnitTip'] = $weightUnit[$product['weightUnit']]['enTitle'];

        $categoryList =  empty($product['categoryList'])? '' : $product['categoryList'];
        $categoryList = explode( ',', $categoryList );
        $seatList =empty($product['seatList'])?'': $product['seatList'];
        $placeList=empty($product['placeList'])? '' : $product['placeList'];
        $seatList = explode( ',', $seatList );
        $placeList=explode( ',', $placeList );;
        $productDepotCategory = $productDepotSeat = $categoryIds = array();
        foreach( $categoryList as $categoryId ){
            $category = D( 'Home/Category' )->detail( array( 'id' => $categoryId ) );
            if( !empty( $category['title'] ) ){
                $productDepotCategory[] = $category;
                $categoryIds[] = $category['id'];
            }
        }
        $i = 0;
        foreach( $seatList as $seatId ){
            if( $i >= 2 ){
                break;
            }
            $area = D( 'Home/Area' )->detail( array( 'id' => $seatId ) );
            if( !empty( $area['title'] ) ){
                $productDepotSeat[] = $area;
            }
            $i++;
        }
        $i = 0;
        foreach( $placeList as $seatId ){
            if( $i >= 2 ){
                break;
            }
            $area = D( 'Home/Area' )->detail( array( 'id' => $seatId ) );
            if( !empty( $area['title'] ) ){
                $productDepotPlace[] = $area;
            }
            $i++;
        }

        if( !empty( $product['brandId'] ) ){
            $brand = D( 'Home/Brand' )->detail( array( 'id' => $product['brandId'] ) );
            if( !empty( $brand['title'] ) ){
                $product['brand'] = $brand['title'];
            }
        }
        if( !empty( $product['producerId'] ) ){
            $producer = D( 'Home/Producer' )->detail( array( 'id' => $product['producerId'] ) );
            if( !empty( $producer['title'] ) ){
                $product['producer'] = $producer['title'];
            }
        }

        //供应商
        if( !empty( $product['Uid'] ) ){
            $product['sellerInfo'] = D( 'Home/Member' )->getMemberInfo( array( 'id' => $product['Uid'] ) );
        }


        //同类商品
        $relateProducts = array();
        if( !empty( $categoryIds ) ){
            $param = array(
                'state' => $productStates['ACTIVE']['value'],
                'page_size' => 5,
                'categoryIds' => $categoryIds,
            );
            $relateProductLists = $model->lists( $param );
            if( !empty( $relateProductLists['lists'] ) ){
                foreach( $relateProductLists['lists'] as &$v ){
                    $v['priceTip'] = (empty($currency[$v['currency']]['character']) ? '' : $currency[$v['currency']]['character'].sprintf( '%.2f', $v['price'] ));
                    $v['weightUnitTip'] = $weightUnit[$v['weightUnit']]['enTitle'];
                    $v['images'] = unserialize( $v['images'] );
                }
                $relateProducts = $relateProductLists['lists'];
            }
        }

        //获取热门商品
        $param = array(
            'state' => $productStates['ACTIVE']['value'],
            'page_size' => 4,
        );

        $hotProducts = $model->lists( $param );
        $hotProducts = $hotProducts['lists'];
        if( !empty( $hotProducts ) ){
            foreach( $hotProducts as &$v9 ){
                $v9['priceTip'] = (empty($currency[$v9['currency']]['character']) ? '' : $currency[$v9['currency']]['character'].sprintf( '%.2f', $v9['price'] ));
                $v9['weightUnitTip'] = $weightUnit[$v9['weightUnit']]['enTitle'];
                $v9['images'] = unserialize( $v9['images'] );
            }
        }
        $this->assign( 'hotProducts', $hotProducts );


        //收藏数
        $collectType = 1;
        $collectCount = intval( D( 'User/Collect' )->getCount( array( 'id' => $id, 'type' => $collectType ) ) );
        $isCollect = false;

        if( !empty( $this->uid ) ){
            $isCollect = D( 'User/Collect' )->getIsCollect( array( 'uid' => $this->uid, 'type' => $collectType , 'id' => $id ) );
        }



        $product['images'] = empty($product['images'] )? '':unserialize( $product['images'] );
        $product['attribute'] = empty($product['attribute'] )? '':unserialize( $product['attribute'] );

        if( empty( $product['attribute']['qualityGradeID'] ) ){
            $product['attribute']['qualityGradeTip'] = '';
        }else{
            $qualityGrade = $model->getProductQualityGrade();
            if( $product['attribute']['qualityGradeID'] == 4 ){
                if( empty( $product['attribute']['qualityGrade'] ) ){
                    $product['attribute']['qualityGradeTip'] = $qualityGrade[$product['attribute']['qualityGradeID']]['enTitle'];
                }else{
                    $product['attribute']['qualityGradeTip'] = $product['attribute']['qualityGrade'];
                }
            }else{
                $product['attribute']['qualityGradeTip'] = $qualityGrade[$product['attribute']['qualityGradeID']]['enTitle'];
            }
        }

        //得到商品关键指标, 并检测传入的参数是否合理
        $indicatorModel = D('Home/Indicator');
        $keyIndexList = $indicatorModel->search(array('get' => array('hash:product:keyIndex:*->cid', 'hash:product:keyIndex:*->name')));
        if (!empty($keyIndexList['rows']) && !empty($product['keyIndex'])) {
            foreach ($keyIndexList['rows'] as $row) {
                $tempKeyIndexList[$row['cid']] = $row['name'];
            }
            $ks = array();
            foreach (json_decode($product['keyIndex'], true) as $k => $v2) {
                if (array_key_exists($k, $tempKeyIndexList)) {
                    $ks[$tempKeyIndexList[$k]] = $v2;
                }
            }

            $product['keyIndex'] = $ks;
        }

        $this->assign( 'loginUid', $this->uid  );
        $this->assign( 'product', $product );
        $companyName=D('User/Account')->SelectAccountInfo($this->uid,array('companyName'))['companyName'];
        
        if($product['Uid']==$this->uid){
        	$companyName=-1;
        }

        $this->assign('companyName',$companyName);
        $this->assign( 'productDepotCategory', $productDepotCategory );
        $this->assign( 'productDepotSeat', empty($productDepotSeat)? array() : array_reverse( $productDepotSeat ) );
        $this->assign( 'productDepotPlace',empty($productDepotPlace) ? array() : array_reverse( $productDepotPlace ) );
        $this->assign( 'relateProducts', $relateProducts );
        $this->assign( 'collectCount', $collectCount );
        $this->assign( 'isCollect', $isCollect );
        $this->assign( 'cate', getcategory() );
        $this->display( 'goods-item' );
    } */
    
    /**
     * 商品详情
     */
    public function detail($id = 0) {
    	$this->assign('id', $id);
    	$this->assign('url', C('KW_DOMAIN').'product/getDetail');
    	
    	$this->assign('page_title', '商品详情');
    	$this->display('goods_dateild');
    }

    /**
     * 获取商城详情
     */
    public function getDetail(){
        $result = [];
        $model = D( 'Home/Product' );
        $id = I( 'json.id' );
        $product = $model->detail( array( 'id' => $id ) );
        // 验证商品存在
        if( empty( $product ) ){
            throw new CsException("Product does not exist", 400);
        }
        $product['attribute'] = unserialize($product['attribute']);
        $product['images'] = unserialize($product['images']);
        $product['keyIndex'] = empty($product['keyIndex']) ? [] : json_decode($product['keyIndex'], true);
        $product['detail'] = empty($product['detail']) ? '' : htmlspecialchars_decode($product['detail']);
        // 验证商品有效
        $productStates = $model->getProductState();
        if( $product['state'] != $productStates['ACTIVE']['value'] ){
            throw new CsException("Product does not exist", 400);
        }
        // 分类
        $categoryList =  empty($product['categoryList'])? '' : $product['categoryList'];
        $categoryList = explode( ',', $categoryList );
        $result['categoryList'] = [];
        foreach( $categoryList as $categoryId ){
            $category = D( 'Home/Category' )->detail( array( 'id' => $categoryId ) );
            if( !empty( $category['title'] ) ){
                $result['categoryList'][] = ['id'=> $category['id'], 'title'=> $category['title']];
            }
        }
        // 商品属性
        $result['title'] = $product['title'];
        $result['cas'] = $product['cas'];
        $result['productCode'] = $product['productCode'];
        $result['sales'] = $product['sales'] . ' Kg';
        $result['images'] = $product['images'];
        // 货币
        $currency = $model->getProductCurrency();
        $result['currency'] = empty($currency[$product['currency']]['character']) ? '' : $currency[$product['currency']]['character'];
        
        //供应商
        if( !empty( $product['Uid'] ) ){
            $sellerInfo = D( 'Home/Member' )->detail( array( 'id' => $product['Uid'] ) );
            $result['companyName'] = $sellerInfo['companyName'];
        } else {
            $result['companyName'] = C('SELF_SUPPORT_INFO')['COMPANY_NAME'];
        }
        // location
        $seatList = empty($product['seatList']) ? '' : $product['seatList'];
        $seatList = explode( ',', $seatList );
        $productDepotSeat = [];
        foreach( $seatList as $key => $seatId ){
            if ( $key >= 2 ) break;
            $area = D( 'Home/Area' )->detail( array( 'id' => $seatId ) );
            if( !empty( $area['title'] ) ){
                $productDepotSeat[] = $area['title'];
            }
        }
        $result['location'] = implode(' ', array_reverse($productDepotSeat));
        // skuList
        $list = D('Home/Sku')->getProductSku($id);
        $unitArr = $model->getProductWeightUnit();
        $skuList = [];
        foreach ($list as $key => $value) {
            $weightUnit = $unitArr[$value['weightUnit']]['name'];
            $stock = '';
            if ($value['inventoryType'] == 2) {
                $stock = 'Out Of Stock';
            } elseif ($value['inventoryNum'] == 0 || $value['inventory'] == 1) {
                $stock = 'In Stock';
            } else {
                $stock = $value['inventoryNum'].$weightUnit;
            }
            $skuList[] = [
                'skuId' => $value['skuId'],
                'specification' => $value['specification'],
                'price' => sprintf('%.2f', $value['price']),
                'weightUnit' => $weightUnit,
                'stock' => $stock,
                'skuId' => $value['skuId'],
                'moq' => $value['moq'],
                'packWeight' => $value['packWeight'],
                'isDefault' => $value['isDefault'],
                'inquiryUrl' => C('KW_DOMAIN').U('Inquiry/writeInquiry', array('skuId'=>'sskuId', 'productNumber'=>'sproductNumber')),
                'orderUrl' => C('KW_DOMAIN').U('order/writeOrder', array('skuId'=>'sskuId', 'productNumber'=>'sproductNumber')),
            ];
        }
        $result['skuList'] = $skuList;
        $result['payway'] = array_column(D('Admin/Data')->getPayway(), 'name');
        // 是否已收藏
        $result['isCollect'] = 0;
        if( !empty( $this->uid ) ){
            $isCollect = D( 'User/Collect' )->getIsCollect( array( 'uid' => $this->uid, 'type' => 1 , 'id' => $id ) );
            if ($isCollect) $result['isCollect'] = 1;
        }
        $result['isLogin'] = $this->uid ? 1 : 0;

        /*********************** Description begin ***********************/
        // 生产商
        $description = [];
        $description['enName'] = $product['enName'];
        $description['enAlias'] = $product['enAlias'];
        $description['producer'] = '';
        if( !empty( $product['producerId'] ) ){
            $producer = D( 'Home/Producer' )->detail( array( 'id' => $product['producerId'] ) );
            if( !empty( $producer['title'] ) ){
                $description['producer'] = $producer['title'];
            }
        }
        // 品牌
        $description['brand'] = '';
        if( !empty( $product['brandId'] ) ){
            $brand = D( 'Home/Brand' )->detail( array( 'id' => $product['brandId'] ) );
            if( !empty( $brand['title'] ) ){
                $description['brand'] = $brand['title'];
            }
        }
        // 原产地
        $description['origin'] = '';
        $placeList = empty($product['placeList'])? '' : $product['placeList'];
        $placeList = explode( ',', $placeList );
        foreach( $placeList as $key => $seatId ){
            if ( $key >= 2 ) break;
            $area = D( 'Home/Area' )->detail( array( 'id' => $seatId ) );
            if( !empty( $area['title'] ) ){
                $productDepotPlace[] = $area['title'];
            }
        }
        $description['origin'] = implode(' ', array_reverse($productDepotPlace));
        $description['format'] = $product['attribute']['format'];
        $description['character'] = $product['attribute']['character'];
        if( empty( $product['attribute']['qualityGradeID'] ) ){
            $description['qualityGrade'] = '';
        }else{
            if( $product['attribute']['qualityGradeID'] == 4 && !empty( $product['attribute']['qualityGrade']) ){
                $description['qualityGrade'] = $product['attribute']['qualityGrade'];
            }else{
                $qualityGrade = $model->getProductQualityGrade();
                $description['qualityGrade'] = $qualityGrade[$product['attribute']['qualityGradeID']]['enTitle'];
            }
        }
        $description['pack'] = $product['attribute']['pack'];
        // 下载
        $description['download'] = [];
        $msdsUrl = empty($product['attribute']['msds']) ? '' : U('Product/download', ['id'=> $id, 'attr'=> 'msds']);
        $description['download'][] = ['url'=> $msdsUrl, 'name'=> 'MSDS'];
        $tdsUrl = empty($product['attribute']['tds']) ? '' : U('Product/download', ['id'=> $id, 'attr'=> 'tds']);
        $description['download'][] = ['url'=> $tdsUrl, 'name'=> 'TDS'];
        $coaUrl = empty($product['attribute']['coa']) ? '' : U('Product/download', ['id'=> $id, 'attr'=> 'coa']);
        $description['download'][] = ['url'=> $coaUrl, 'name'=> 'COA'];
        $result['description'] = $description;
        /************************* Description end *************************/

        $result['summary'] = $product['attribute']['summary'];
        $result['purpose'] = $product['attribute']['purpose'];
        $result['condition'] = $product['attribute']['condition'];
        $result['detail'] = htmlspecialchars_decode($product['detail']);
        $result['faq'] = htmlspecialchars_decode($product['faq']);

        /************************* Table begin *************************/
        //得到商品关键指标, 并检测传入的参数是否合理
        $keyIndex = [];
        $keyIndexList = D('Home/Indicator')->search(array('get' => array('hash:product:keyIndex:*->cid', 'hash:product:keyIndex:*->name')));
        if (!empty($keyIndexList['rows']) && !empty($product['keyIndex'])) {
            foreach ($keyIndexList['rows'] as $value) {
                $tempKeyIndexList[$value['cid']] = $value['name'];
            }
            $row = [];
            foreach ($product['keyIndex'] as $key => $value) {
                if (array_key_exists($key, $tempKeyIndexList)) {
                    $row[] = $tempKeyIndexList[$key];
                    $row[] = $value;
                }
                if (count($row) >= 4) {
                    $keyIndex[] = $row;
                    $row = [];
                }
            }
            if (!empty($row)) {
                $row[] = $row[] = '';
                $keyIndex[] = $row;
            }
        }
        $result['keyIndex'] = $keyIndex;
        $attr = [];
        $attr[] = ['CAS NO.', empty($product['cas']) ? '' : $product['cas'], 
            'Relative Density', empty($product['attribute']['density']) ? '' : $product['attribute']['density']
        ];
        $attr[] = ['Odor', empty($product['attribute']['smell']) ? '' : $product['attribute']['smell'], 
            'Solubility', empty($product['attribute']['solubility']) ? '' : $product['attribute']['solubility']
        ];
        $attr[] = ['Melting Point', empty($product['attribute']['melting']) ? '' : $product['attribute']['melting'],
            'EINECS NO.', empty($product['einecsNO']) ? '' : $product['einecsNO']
        ];
        $attr[] = ['Flash Point', empty($product['attribute']['flash']) ? '' : $product['attribute']['flash'],
            'Molecular Weight', empty($product['attribute']['formula']) ? '' : $product['attribute']['formula']
        ];
        $attr[] = ['pH', empty($product['attribute']['ph']) ? '' : $product['attribute']['ph'],
            'Boiling Point', empty($product['attribute']['boiling']) ? '' : $product['attribute']['boiling']
        ];
        $result['attr'] = $attr;
        /************************* Table end *************************/

        // 热门商品，根据销量sales的倒序
        $param = array(
            'by' => 'hash:product:*->sales',
            'state' => $productStates['ACTIVE']['value'],
            'page_size' => 4,
        );
        $list = $model->lists( $param );
        $list = empty($list['lists'])?array():$list['lists'];
        $hotProducts = [];
        if( !empty( $list ) ){
            foreach( $list as $value ){
                $hotProducts[] = [
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'currency' => empty($currency[$value['currency']]['character']) ? '' : $currency[$value['currency']]['character'],
                    'price' => sprintf('%.2f', $value['price']),
                    'weightUnit' => $weightUnit[$value['weightUnit']]['name'],
                    'thumb' => C('KW_DOMAIN').unserialize($value['images'])[0],
                    'url' => U('detail', array('id'=>$value['id'])),
                ];
            }
        }
        $result['hotProducts'] = $hotProducts;

        $this->ajaxReturn($result);
    }

    /**
     * 商品详情资料下载
     */
    public function download(){
        $id = I('get.id');
        if (!$id) {
            return;
        }
        $attr = I('get.attr');
        $distFile = realpath(dirname(APP_PATH));
        $model = D('Home/Product');
        $product = $model->detail(array('id' => $id));
        $productDepot1 = unserialize($product['attribute']);
        if ($attr == 'msds') {
            $distFile = $distFile . $productDepot1['msds'];
        } else if ($attr == 'tds') {
            $distFile = $distFile . $productDepot1['tds'];

        } else if ($attr == 'coa') {
            $distFile = $distFile . $productDepot1['coa'];
        } else {
            return;
        }
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

}