<?php
$redisKeyConfig=array(
		'REDIS_KEYS'=>array(
			// string类型
			'string'=>array(
					'/^member:[\w]+$/',
					'/^string:aboutUs:mediaReport$/',
					'/^string:aboutUs:notice$/',
					'/^string:area$/',
					'/^string:brand$/',
					'/^string:buyoffer$/',
					'/^string:buyoffer:history$/',
					'/^string:buyoffer:[\w]+$/',
					'/^string:category$/',
					'/^string:company:.+$/',
					'/^string:company:email:.+$/',
					'/^string:email:rate:[\w]+$/',
					'/^string:email:rate:lock:[\w]+$/',
					'/^string:employees$/',
					'/^string:fail:[\w]+$/',
					'/^string:ip:[\d]{8}:[\.\d]+$/',
					'/^string:limitRate.+$/',
					'/^string:member$/',
					'/^string:member:history$/',
					'/^string:message$/',
					'/^string:model$/',
					'/^string:partner$/',
					'/^string:phone:[\d]{11}$/',
					'/^string:phone:[\d]{8}:[\d]{11}$/',
					'/^string:producer$/',
					'/^string:product$/',
					'/^string:product:history$/',
					'/^string:productCode:[\w]+$/',
					'/^string:product:keyIndex:id$/',
					'/^string:product:keyIndex:[\w]+$/',
					'/^string:property$/',
					'/^string:supply$/',
					'/^string:supply:[\w]+$/',
					'/^string:supply:history$/',
					'/^string:system$/',
					'/^string:trade$/',
					'/^string:turnover$/',
					// 追加
					'/^string:sku$/',
					'/^string:product:unit$/',
					'/^string:verifyCode:email:[\w]+$/',
					
					),  
				
			// hash类型
			'hash'=>array(
					'aboutFS'	=>'/^hash:aboutUs:company:foshan$/',
					'aboutGZ'	=>'/^hash:aboutUs:company:guangzhou$/',
					'cateCache'	=>'/^hash:category:cache$/',
					'coopBrand'	=>'/^hash:aboutUs:cooperation:brand$/',
					'coopCustom'=>'/^hash:aboutUs:cooperation:custom$/',
					'coopInvest'=>'/^hash:aboutUs:cooperation:invest$/',
					'coopPurch'	=>'/^hash:aboutUs:cooperation:purchase$/',
					'coopSell'	=>'/^hash:aboutUs:cooperation:sell$/',
					'usDesc'	=>'/^hash:aboutUs:description$/',
					'usLegal'	=>'/^hash:aboutUs:legalStatement$/',
					'report'	=>'/^hash:aboutUs:mediaReport:[\d]+$/',
					'notice'	=>'/^hash:aboutUs:notice:[\d]+$/',
					'protocol'	=>'/^hash:aboutUs:protocol$/',
					'area'		=>'/^hash:area:[\d]+$/',
					'brand'		=>'/^hash:brand:[\d]+$/',
					'buyoffer'	=>'/^hash:buyoffer:[\d]+$/',
					'buyHis'	=>'/^hash:buyoffer:operation:history:[\d]+$/',
					'category'	=>'/^hash:category:[\d]+$/', 
					'comHis'	=>'/^hash:company:operation:history:[\d]+$/',
					'country'	=>'/^hash:country:name$/',
					'employees'	=>'/^hash:employees:[\d]+$/',
					'member'	=>'/^hash:member:[\d]+$/',
					'memInfo'	=>'/^hash:member:info:[\d]+$/',
					'memHis'	=>'/^hash:member:operation:history:[\d]+$/',
					'message'	=>'/^hash:message:[\d]+$/',
					'model'		=>'/^hash:model:[\d]+$/',
					'partner'	=>'/^hash:partner:[\d]+$/',
					'producer'	=>'/^hash:producer:[\d]+$/',
					'product'	=>'/^hash:product:[\d]+$/',
					'productKey'=>'/^hash:product:keyIndex:([\d]+)$/',
					'productHis'=>'/^hash:product:operation:history:[\d]+$/',
					'property'	=>'/^hash:property:[\d]+$/',
					'supply'	=>'/^hash:supply:[\d]+$/',
					'supplyOper'=>'/^hash:supply:operation:history:[\d]+$/',
					'supplyHis'=>'/^hash:purchase:operation:history:[\d]+$/',
					'system'	=>'/^hash:systemInfo:[\d]+$/',
					'trade'		=>'/^hash:trade:[\d]+$/',
					'turnover'	=>'/^hash:turnover:[\d]+$/',
					// 追加
					'sku'	=>'/^hash:sku:[\d]+$/',

					), 
				
			// hash类型
			'hashFields'=>array(
					'aboutFS'	=>array('address', 'companyName', 'tel', 'serviceTel', 'email', 'fax', 'editTime', 'addTime'),
					'aboutGZ'	=>array('address', 'companyName', 'tel', 'serviceTel', 'email', 'fax', 'editTime', 'addTime'),
					'cateCache'	=>array('cache'),
					'coopBrand'	=>array('phone', 'mail', 'name', 'qq', 'editTime', 'addTime'),
					'coopCustom'=>array('phone', 'mail', 'name', 'qq', 'editTime', 'addTime'),
					'coopInvest'=>array('phone', 'mail', 'name', 'qq', 'editTime', 'addTime'),
					'coopPurch'=>array('phone', 'mail', 'name', 'qq', 'editTime', 'addTime'),
					'coopSell'	=>array('phone', 'mail', 'name', 'qq', 'editTime', 'addTime'),
					'usDesc'	=>array('content', 'editTime', 'addTime'),
					'usLegal'	=>array('content', 'editTime', 'addTime'),
					'report'	=>array('id', 'title', 'content', 'userId', 'img', 'referer', 'reportDate', 'editTime', 'addTime'),
					'notice'	=>array('id', 'title', 'content', 'userId', 'editTime', 'addTime'),
					'protocol'	=>array('title', 'content', 'editTime', 'addTime'),
					'area'		=>array('id', 'title', 'short', 'parentId', 'parentList', 'depth', 'addTime', 'updateTime'),
					'brand'		=>array('id', 'title', 'parentId', 'parentList', 'depth', 'addTime', 'updateTime'),
					'buyoffer'	=>array('id', 'type', 'title', 'content', 'expire', 'state', 'number', 'Uid', 'times', 'createTime', 'updateTime',
										// 追加
										'image','location'
										),
					'buyHis'	=>'/^[\d]+$/',
					'category'	=>array('id', 'title', 'parentId', 'parentList', 'depth', 'addTime', 'updateTime'),
					'comHis'	=>'/^[\d]+$/',
					'country'	=>'/^[\w]{2}$/',
					'employees'	=>array('id', 'title', 'addTime', 'updateTime'),
					'member'	=>array('id', 'username', 'password', 'salt', 'phone', 'img', 'email', 'lastLoginIp', 'lastLoginTime', 'recentLoginTime', 
										'bind', 'is_new', 'close', 'isFirstLogin', 'status', 'addTime', 'country','source', 'type','guide'
										// 追加
										,'companyName','firstName','secondName','foxedPhone','addressList','addressDetail','businessCert'
										),
					'memInfo'	=>array('id', 'companyName', 'establishmentDate', 'cert', 'property', 'state', 'turnover', 'employee', 
										'model', 'trade', 'opera', 'companyIntroduction', 'contact', 'businessScope', 'other', 'businessTerm','intention'),
					'memHis'	=>'/^[\d]+$/',
					'message'	=>array('id', 'subject', 'content', 'from', 'to', 'status', 'sendTime', 'readTime','reply'),
					'model'		=>array('id', 'title', 'addTime', 'updateTime'),
					'partner'	=>array('id', 'text', 'img', 'status', 'addTime'),
					'producer'	=>array('id', 'title', 'shortTitle', 'parentId', 'parentList', 'depth', 'addTime', 'updateTime'),
					'product'	=>array('id', 'title', 'enName', 'enAlias', 'productCode', 'cas', 'brandId', 'producerId', 'placeList', 'seatList', 'categoryList', 
										'price', 'weightUnit', 'currency', 'moq', 'inventoryType','inventory','inventoryNum', 'paymentMethod',  'sales', 'tradingCapacity', 'attribute', 'images',
										'logisticsMethod', 'lastUpdateIp', 'Uid','keyIndex','state', 'addTime', 'updateTime','einecsNO'
										// 追加
										,'detail'
										),
					'productHis'=>'/^[\d]+$/',
					'productKey'  =>array('id', 'cid', 'name', 'addTime', 'editTime'),
					'property'	=>array('id', 'title', 'addTime', 'updateTime'),
					'supply'	=>array('id', 'type', 'title', 'expire', 'Uid', 'content', 'times', 'number', 'state', 'createTime', 'updateTime'),
					'supplyOper'=>'/^[\d]+$/',
					'supplyHis'=>'/^[\d]+$/',
					'system'	=>array('id', 'subject', 'content', 'from', 'to', 'status', 'sendTime', 'readTime'),
					'trade'		=>array('id', 'title', 'addTime', 'updateTime'),
					'turnover'	=>array('id', 'title', 'addTime', 'updateTime'),
					//追加
					'sku'	=>array('skuId', 'productId', 'specification', 'price', 'inventoryType', 'inventory', 'inventoryNum', 'moq', 'weightUnit', 'packWeight', 
										'status', 'isDefault', 'addTime'),
					
			),
				
			// set类型
			'set'=>array(
					'/^set:aboutUs:mediaReport:status:[\d]{1}$/',
					'/^set:aboutUs:notice:status:[\d]{1}$/',
					'/^set:active:country:[\d]{1}$/',
					'/^set:area:status:[\d]{1}$/',
                    '/^set:area:member:[\d]+$/',
					'/^set:areaAllChild:[\d]+$/',
					'/^set:areaChild:[\d]+$/',
					'/^set:active:country:[\d]+$/',
					'/^set:brand:name$/',
					'/^set:brandAllChild:[\d]+$/',
					'/^set:brand:status:[\d]+$/',
					'/^set:brandChild:[\d]+$/',
					'/^set:buyoffer:member:[\d]+$/',
					'/^set:buyoffer:state:[\d]{1}$/',
					'/^set:buyoffer:status:[\d]{1}$/',
					'/^set:buyoffer:type:[\d]{1}$/',
					'/^set:category:status:[\d]{1}$/',
					'/^set:categoryAllChild:[\d]+$/',
					'/^set:categoryChild:[\d]+$/',
					'/^set:company:complete:[\d]{1}$/',
					'/^set:company:state:[\d]{1}$/',
					'/^set:employees:status:[\d]{1}$/',
					'/^set:member:active:country$/',
					'/^set:member:company:certType:[\d]+$/',
					'/^set:member:sign:status:[\d]{1}$/',
					'/^set:member:status:[\d]{1}$/',
					'/^set:member:type:.+$/',
					'/^set:message:[\d]+$/',
					'/^set:message:read:[\d]+$/',
					'/^set:message:receive:[\d]+$/',
					'/^set:message:status:[\d]+$/',
					'/^set:message:status:[\d]+:[\d]{1}$/',
					'/^set:model:status:[\d]{1}$/',
					'/^set:partner:status:[\d]{1}$/',
					'/^set:producer:name$/',
					'/^set:producer:status:[\d]{1}$/',
					'/^set:producerChild:[\d]+$/',
					'/^set:producerAllChild:[\d]+$/',
					'/^set:product:brand:[\d]+$/',
					'/^set:product:cas:.+$/',
					'/^set:product:category:[\d]+$/',
					'/^set:product:member:[\d]+$/',
					'/^set:product:model:[\d]*$/',
					'/^set:product:order:[\d]+$/',
					'/^set:product:productDepot:[\d]+$/',
					'/^set:product:seat:[\d]+$/',
					'/^set:product:state:[\d]+$/',
					'/^set:product:status:[\d]+$/',
                    '/^set:product:stock:[\d]+$/',
					'/^set:product:unread:[\d]+:[\d]+$/',
					'/^set:product:keyIndex:status:[\d]+$/',
					'/^set:property:status:[\d]{1}$/',
					'/^set:supply:member:[\d]+$/',
					'/^set:supply:state:[\d]{1}$/',
					'/^set:supply:status:[\d]{1}$/',
					'/^set:supply:type:[\d]{1}$/',
					'/^set:system:[\d]+$/',
					'/^set:system:read:[\d]{1}$/',
					'/^set:system:status:[\d]{1}$/',
					'/^set:member:source:[\w]+$/',
					'/^set:member:type:[\w]+$/',
					'/^set:trade:status:[\d]{1}$/',
					'/^set:turnover:status:[\d]{1}$/',
					'/^set:white:phone$/',
					'/^tmp:.+$/',//tmp:set:.+
					'/^tmp:company:productDepot:list:[\w]+$/',
					//追加
					'/^set:sku:productId:[\d]+$/',

					
					),  
				
			// zset类型
			'zset'=>array(
					'/^zset:collect:buyoffer$/',
					'/^zset:collect:buyoffer:[\d]+$/',
					'/^zset:collect:goods$/',
					'/^zset:collect:goods:[\d]+$/',
					'/^zset:collect:supply$/',
					'/^zset:collect:supply:[\d]+$/',
					'/^zset:member:addTime$/',
					'/^zset:product:price$/',
					'/^tmp:.+$/',//tmp:zset:.+
					'/^tmp:company:productDepot:list:[\w]+$/',
					
					'/^RFSC:index:member:companyName:postid:.+$/',
					'/^RFSC:index:member:username:postid:.+$/',
					'/^RFSC:index:order:goods:goodsTitle:postid:.+$/',
					'/^RFSC:index:buyoffer:title:postid:.+$/',
					'/^RFSC:index:product:title:postid:.+$/',
					'/^RFSC:index:test:postid:.+$/',
					'/^RFSC:index:Mailbox:title:postid:.+$/',
					'/^RFSC:index:supply:title:postid:.+$/',
					'/^RFSC:tmp:.+$/',
					
					),
				
			// list类型
			'list'=>array(), 
				
			
			
		)
	);
?>