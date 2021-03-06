<?php
return array(
	"MENU" => array(
		array(
			'gname' => '快速操作',
			'gicon' => 'icon-search',
			'list' => array(
				array('url'=>'Pay/index', 'icon'=>'icon-users', 'name'=>'消费'),
				array('url'=>'Cus/add', 'icon'=>'icon-users', 'name'=>'添加会员'),
			),
		),
		array(
			'gname' => '顾客管理',
			'gicon' => 'icon-man',
			'list' => array(
				array('url'=>'Cus/index', 'icon'=>'icon-users', 'name'=>'会员列表'),
				array('url'=>'Level/index', 'icon'=>'icon-users', 'name'=>'会员等级'),
			    array('url'=>'Card/index', 'icon'=>'icon-group_go', 'name'=>'生成会员卡')
			),
		),
		array(
			'gname' => '商品管理',
			'gicon' => 'icon-tip',
			'list' => array(
				array('url'=>'Goods/index', 'icon'=>'icon-users', 'name'=>'商品列表'),
			    array('url'=>'Package/index', 'icon'=>'icon-group_go', 'name'=>'套餐管理'),
			    array('url'=>'Cate/index', 'icon'=>'icon-group_go', 'name'=>'商品类别管理')
			),
		),
		array(
			'gname' => '订单管理',
			'gicon' => 'icon-tip',
			'list' => array(
				array('url'=>'Order/index', 'icon'=>'icon-users', 'name'=>'订单列表'),
			),
		),
		array(
			'gname' => '运营管理',
			'gicon' => 'icon-tip',
			'list' => array(
				array('url'=>'Sale/index', 'icon'=>'icon-users', 'name'=>'促销活动'),
				array('url'=>'Recharge/index', 'icon'=>'icon-users', 'name'=>'充值活动'),
				array('url'=>'Erate/index', 'icon'=>'icon-users', 'name'=>'积分兑换设置'),
				//array('url'=>'Member/index', 'icon'=>'icon-users', 'name'=>'数据报表'),
			),
		),
		array(
			'gname' => '系统管理',
			'gicon' => 'icon-lock',
			'list' => array(
				array('url'=>'Auth/index', 'icon'=>'icon-users', 'name'=>'用户管理'),
				array('url'=>'Auth/group', 'icon'=>'icon-group_go', 'name'=>'用户组管理'),
				//array('url'=>'Auth/rule', 'icon'=>'icon-rule', 'name'=>'规则管理'),
				array('url'=>'Shop/index', 'icon'=>'icon-rule', 'name'=>'店铺管理'),
				array('url'=>'Admin/pw', 'icon'=>'icon-rule', 'name'=>'修改密码'),
				array('url'=>'Admin/log', 'icon'=>'icon-rule', 'name'=>'日志列表'),
			),
		),
	)
);
