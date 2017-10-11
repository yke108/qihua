<?php
namespace Home\Controller;
use Think\Controller;


class ScriptController extends Controller{
    protected $redis;

    public function __construct(){
        $this->redis = \Think\Cache::getInstance('Redis');
    }

    /**
     * 同步商家信息
     */
	public function syncMember(){
        $tmpKey = 'tmp:zset:member:status:'.rand(0,9999);
        $this->redis->zunion($tmpKey, ['set:member:status:0','set:member:status:1','set:member:status:2']); // 
        $list = $this->redis->zRevRange($tmpKey ,0, -1);
        $this->redis->del($tmpKey);
        $num = 0;
        foreach ($list as $id) {
            $companyName = $this->redis->hGet('hash:member:info:'.$id, 'companyName');
            $companyName = $companyName ? $companyName : '';
            $other = $this->redis->hGet('hash:member:info:'.$id, 'other');
            $cert = $this->redis->hGet('hash:member:info:'.$id, 'cert');
            // 地区
            $other = !empty($other) ? unserialize($other) : [];
            $addressList = [];
            if (isset( $other['country']) && !empty( $other['country'])) $addressList[] = $other['country']; 
            if (isset( $other['area_s']) && !empty( $other['area_s'])) $addressList[] = $other['area_s']; 
            if (isset( $other['area_c']) && !empty( $other['area_c'])) $addressList[] = $other['area_c']; 
            $addressList = implode(',', $addressList);
            // 营业执照
            $cert = !empty($cert) ? unserialize($cert) : [];
            $businessCert = isset($cert['businessCert']) ? $cert['businessCert'] : '';
            
            $ret = $this->redis->hSet('hash:member:'.$id, 'firstName', '');
            $ret = $this->redis->hSet('hash:member:'.$id, 'secondName', '');
            $ret = $this->redis->hSet('hash:member:'.$id, 'foxedPhone', '');
            $ret = $this->redis->hSet('hash:member:'.$id, 'addressList', $addressList);
            $ret = $this->redis->hSet('hash:member:'.$id, 'addressDetail', !empty($other['address']) ? $other['address'] : '');
            $ret = $this->redis->hSet('hash:member:'.$id, 'companyName', $companyName);
            $ret = $this->redis->hSet('hash:member:'.$id, 'businessCert', $businessCert);
            if ($ret) $num++;
        }
        echo $num;
	}

    /**
     * 同步商品SKU信息
     */
    public function syncSku(){
        $tmpKey = 'tmp:zset:sku:status:'.rand(0,9999);
        $this->redis->zunion($tmpKey, ['set:product:status:1','set:product:status:2']); // 
        $list = $this->redis->zRevRange($tmpKey ,0, -1);
        $this->redis->del($tmpKey);
        $unitArr = D( 'Home/Product' )->getProductWeightUnit();
        $num = 0;
        foreach ($list as $id) {
            if ($this->redis->exists('set:sku:productId:'.$id)) {
                continue;
            }
            $skuId = $this->redis->incr('string:sku');
            $price = $this->redis->hGet('hash:product:'.$id, 'price');
            $inventoryType = $this->redis->hGet('hash:product:'.$id, 'inventoryType');
            $inventory = $this->redis->hGet('hash:product:'.$id, 'inventory');
            $inventoryNum = $this->redis->hGet('hash:product:'.$id, 'inventoryNum');
            $moq = $this->redis->hGet('hash:product:'.$id, 'moq');
            $weightUnit = $this->redis->hGet('hash:product:'.$id, 'weightUnit');
            $skuInfo = [
                'skuId'=> $skuId,
                'productId'=> $id,
                'specification'=> $price.'$/'.$unitArr[$weightUnit]['name'],
                'price'=> $price,
                'inventoryType'=> $inventoryType,
                'inventory'=> $inventory,
                'inventoryNum'=> $inventoryNum,
                'moq'=> $moq,
                'weightUnit'=> $weightUnit,
                'packWeight'=> $weightUnit == 2 ? 1 : 1000,
                'status'=> 1,
                'isDefault'=> 1,
                'addTime'=> time(),
            ];
            $this->redis->sAdd('set:sku:productId:'.$id, $skuId);
            $ret = $this->redis->hMset('hash:sku:'.$skuId, $skuInfo);
            if ($ret) $num++;
        }
        echo $num;
    }

    /**
     * 替换商品图片
     */
    // public function syncGoodsImg(){
    //     $tmpKey = 'tmp:zset:sku:status:'.rand(0,9999);
    //     $this->redis->zunion($tmpKey, ['set:product:status:1','set:product:status:2']); // 
    //     $list = $this->redis->zRevRange($tmpKey ,0, -1);
    //     $this->redis->del($tmpKey);
    //     $num = 0;
    //     foreach ($list as $id) {
    //         $ret = $this->redis->hSet('hash:product:'.$id, 'images', serialize(['/Uploads/Admin/20170925/1506306133.png']));
    //         if ($ret) $num++;
    //     }
    //     echo $num;
    // }

    /**
     * 商品品插入商品SKUID
     */
    public function syncSkuId(){
        $tmpKey = 'tmp:zset:sku:status:'.rand(0,9999);
        $this->redis->zunion($tmpKey, ['set:product:status:1','set:product:status:2']); // 
        $list = $this->redis->zRevRange($tmpKey ,0, -1);
        $this->redis->del($tmpKey);
        $num = 0;
        foreach ($list as $id) {
            $ret = $this->redis->hSet('hash:product:'.$id, 'faq', "&lt;p style=&quot;box-sizing: content-box; margin-top: 0px; margin-bottom: 0px; font-size: 12px; line-height: 18px; padding: 0px; border: 0px; font-stretch: inherit; font-family: Arial, Helvetica, sans-senif; vertical-align: baseline; color: rgb(51, 51, 51); background-color: rgb(255, 255, 255);&quot;&gt;&lt;strong&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;1.What&amp;nbsp;is&amp;nbsp;your&amp;nbsp;main&amp;nbsp;item?&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: content-box; margin-top: 0px; margin-bottom: 0px; font-size: 12px; line-height: 18px; padding: 0px; border: 0px; font-stretch: inherit; font-family: Arial, Helvetica, sans-senif; vertical-align: baseline; color: rgb(51, 51, 51); background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;&lt;span style=&quot;box-sizing: content-box; font-weight: 700;&quot;&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; font-family: inherit; vertical-align: baseline; color: rgb(255, 102, 0);&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;/span&gt;We&amp;nbsp;are&amp;nbsp;professional&amp;nbsp;supplier&amp;nbsp;for&amp;nbsp;chemical .&lt;/span&gt;&lt;/p&gt;&lt;div style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-stretch: inherit; font-size: 12px; line-height: 18px; font-family: Arial, Helvetica; vertical-align: baseline; color: rgb(51, 51, 51); word-wrap: break-word; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br style=&quot;box-sizing: content-box;&quot;/&gt;&lt;strong&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;2.What&amp;#39;s&amp;nbsp;your&amp;nbsp;delivery&amp;nbsp;time?&lt;/span&gt;&lt;/strong&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-stretch: inherit; font-size: 12px; line-height: 18px; font-family: Arial, Helvetica; vertical-align: baseline; color: rgb(51, 51, 51); word-wrap: break-word; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;Normally,&amp;nbsp;our&amp;nbsp;delivery&amp;nbsp;time&amp;nbsp;is&amp;nbsp;about&amp;nbsp;10 - 25&amp;nbsp;days&amp;nbsp;after&amp;nbsp;payment, &amp;nbsp;if&amp;nbsp;you&amp;nbsp;are&amp;nbsp;urgent, &amp;nbsp;we&amp;nbsp;can &amp;nbsp;push for you the&amp;nbsp;best&amp;nbsp;lead&amp;nbsp;time&amp;nbsp;we&amp;nbsp;can&amp;nbsp;do&amp;nbsp;is&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;3&amp;nbsp;days&amp;nbsp;after&amp;nbsp;confirmed&amp;nbsp;the&amp;nbsp;sample&amp;nbsp;and&amp;nbsp;payment.&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-stretch: inherit; font-size: 12px; line-height: 18px; font-family: Arial, Helvetica; vertical-align: baseline; color: rgb(51, 51, 51); word-wrap: break-word; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; font-family: inherit; vertical-align: baseline; color: rgb(255, 102, 0);&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-stretch: inherit; font-size: 12px; line-height: 18px; font-family: Arial, Helvetica; vertical-align: baseline; color: rgb(51, 51, 51); word-wrap: break-word; background-color: rgb(255, 255, 255);&quot;&gt;&lt;strong&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;3.&amp;nbsp;&amp;nbsp;How&amp;nbsp;to&amp;nbsp;place&amp;nbsp;order?&lt;/span&gt;&lt;/strong&gt;&lt;br style=&quot;box-sizing: content-box;&quot;/&gt;&lt;span style=&quot;box-sizing: content-box; margin: 0px; padding: 0px; border: 0px none; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: 16px; line-height: 24px; font-family: inherit; vertical-align: baseline;&quot;&gt;Choose →&amp;nbsp;Confirm &amp;nbsp;→ Ship&amp;nbsp;goods.&lt;/span&gt;&lt;/div&gt;");
            if ($ret) $num++;
        }
        echo $num;
    }
}