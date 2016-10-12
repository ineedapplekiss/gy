<?php
return array(
	array(
		'gname' => '权限模块',
		'gicon' => 'icon-auth',
		'list' => array(
			//url=>权限 icon=>图标css name=>名称
			array('url'=>'Auth/index', 'icon'=>'icon-users', 'name'=>'用户管理'),
			array('url'=>'Auth/group', 'icon'=>'icon-group_go', 'name'=>'角色管理'),
			array('url'=>'Auth/rule', 'icon'=>'icon-rule', 'name'=>'规则管理'),
			array('url'=>'Auth/log', 'icon'=>'icon-rule', 'name'=>'日志列表'),
			),
	),
	array(
		'gname' => '研发工具',
		'gicon' => 'icon-cog',
		'list' => array(
			array('url'=>'Customer/fnotice', 'icon'=>'icon-users', 'name'=>'资金流水核对'),
		    array('url'=>'PzApi/index', 'icon'=>'icon-rule', 'name'=>'配资接口日志'),
		    array('url'=>'Changecard/index', 'icon'=>'icon-rule', 'name'=>'更换用户银行卡'),
            array('url'=>'Tool/clear', 'icon'=>'icon-key', 'name'=>'清除验证码限制'),
			array('url'=>'Customer/nameAuth', 'icon'=>'icon-users', 'name'=>'实名认证订单查询'),
			array('url'=>'DayBackInvest/index', 'icon'=>'icon-users', 'name'=>'复投率'),
			array('url'=>'Hq/backAmount', 'icon'=>'icon-rule', 'name'=>'活期用户可提现金额'),
		    array('url'=>'UserFeedback/index', 'icon'=>'icon-bell', 'name'=>'用户反馈信息'),
		    array('url'=>'Data11/index', 'icon'=>'icon-users', 'name'=>'双11数据'),
			array('url'=>'InvestPath/index', 'icon'=>'icon-users', 'name'=>'投资路径'),	
			array('url'=>'Test/redis', 'icon'=>'icon-users', 'name'=>'redis-cli'),	
		    array('url'=>'UserAgent/index', 'icon'=>'icon-users', 'name'=>'理财师用户列表'),
			array('url'=>'AppResource/index', 'icon'=>'icon-arrow_refresh', 'name'=>'APP资源包管理'),
		
			),
		)
	);