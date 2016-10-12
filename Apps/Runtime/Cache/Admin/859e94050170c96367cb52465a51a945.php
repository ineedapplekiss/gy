<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo ($webName); ?>--后台首页</title>
	<link rel="stylesheet" type="text/css" href="/Public/Common/easyui/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="/Public/Common/easyui/css/admin.css">
	<link rel="stylesheet" type="text/css" href="/Public/Common/easyui/themes/icon.css">
	<script type="text/javascript" src="/Public/Common/easyui/js/jquery.min.js"></script>
	<script type="text/javascript" src="/Public/Common/easyui/js/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="/Public/Common/easyui/js/easyloader.js"></script>
	<script type="text/javascript" src="/Public/Common/easyui/js/easyui-lang-zh_CN.js"></script>
	<script type="text/javascript" src="/Public/Common/easyui/js/common.js"></script>
	<script type="text/javascript" src="/Public/Common/easyui/js/extends.js"></script>

	<script>
		function showTab(url,title){
			if ($('#黄药师Tabs').tabs('exists',title)){
				$('#黄药师Tabs').tabs('select',title);
			} else {
				$('#黄药师Tabs').tabs('add',{
					title:title,
					href:url,
					closable:true,
					onLoadError:function(data){
						var info=eval('('+data.responseText+')');
						$.messager.confirm('错误提示',info.message,function(r){
							$('#黄药师Tabs').tabs('close',title);
						});
						return false;
					}	
				});
			}
		}
	</script>
</head>
<body>
	<div class="easyui-layout" id="layout" data-options="fit:true">
		<div  data-options="region:'north'" border="false" >
			<div class="topLine">
							<p class="fl"><span class="icon icon-users">&nbsp;</span>用户：管理员(<?php echo (session('username')); ?>)&nbsp;&nbsp;&nbsp;<span class="icon icon-role">&nbsp;</span>角色：<?php echo (session('gname')); ?>&nbsp;&nbsp;&nbsp;<span class="icon icon-clock">&nbsp;</span>登陆时间：<?php echo (date('Y-m-d H:i:s',session('logintime'))); ?></p>
							<p class="fr">
								<span class="icon icon-arrow_refresh">&nbsp;</span><a  title="更新缓存" href="javascript:void(0)" onclick="clearCache()">更新缓存</a>
								<span class="icon icon-door_out">&nbsp;</span><a title="退出" href="javascript:void(0)" onclick="logout()">退出</a>
							</p>
			</div>
			<!-- <div style="height:50px;"></div> -->
		</div>
		<!-- <div data-options="region:'south',split:true" style="height:50px;">底部</div> -->

		<div data-options="region:'west',split:true," title="导航菜单" style="width:220px;">
		 	<div class="easyui-accordion">
		        <!-- <div title="系统模块" data-options="iconCls:'icon-ok'" style="overflow:auto;padding:10px;">
		            
		        </div> -->
		        <!-- <div title="文章模块" data-options="iconCls:'icon-help'" style="padding:10px;">
		           	<ul class="navlist">
		        						<li>
		        							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Auth/index');?>','用户管理')"><span class="icon icon-users">&nbsp;</span><span class="nav">用户管理</span></a>
		        						</li>
		        						<li>
		        							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Auth/group');?>','角色管理')"><span class="icon icon-group_go">&nbsp;</span><span class="nav">角色管理</span></a>	
		        						</li>
		        						<li>
		        							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Auth/rule');?>','规则管理')"><span class="icon icon-rule">&nbsp;</span><span class="nav">规则管理</span></a>
		        							
		        						</li>
		        					</ul>
		        
		        </div> -->
		        <div title="权限模块" data-options="iconCls:'icon-help',selected:true" style="padding:10px;">
		           	<ul class="navlist">
						<li>
							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Auth/index');?>','用户管理')"><span class="icon icon-users">&nbsp;</span><span class="nav">用户管理</span></a>
						</li>
						<li>
							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Auth/group');?>','角色管理')"><span class="icon icon-group_go">&nbsp;</span><span class="nav">角色管理</span></a>
						</li>
						<li>
							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Auth/rule');?>','规则管理')"><span class="icon icon-rule">&nbsp;</span><span class="nav">规则管理</span></a>
						</li>
					</ul>
		        </div>
		        <div title="会员模块" data-options="iconCls:'icon-search'" style="padding:10px;">
					<ul class="navlist">
						<li>
							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Member/index');?>','会员管理')"><span class="icon icon-users">&nbsp;</span><span class="nav">会员管理</span></a>
						</li>
						<li>
							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Member/group');?>','管理会员组')"><span class="icon icon-group_go">&nbsp;</span><span class="nav">会员组管理</span></a>
						</li>
						<!-- <li>
							<a rel="http://hxling.cnblogs.com" href="#" ref="12"><span class="icon icon-role">&nbsp;</span><span class="nav">资金账户</span></a>
						</li>
						
						<li>
							<a rel="http://hxling.cnblogs.com" href="#" ref="12"><span class="icon icon-set">&nbsp;</span><span class="nav">资金记录</span></a>
						</li> -->
					</ul>
		        </div>
		        <div title="积分模块" data-options="iconCls:'icon-search'" style="padding:10px;">
		        					<ul class="navlist">
		        						<li>
		        							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Member/index');?>','会员管理')"><span class="icon icon-users">&nbsp;</span><span class="nav">会员管理</span></a>
		        						</li>
		        						<li>
		        							<a href="javascript:void(0)" onclick="showTab('<?php echo U('Member/group');?>','管理会员组')"><span class="icon icon-group_go">&nbsp;</span><span class="nav">会员组管理</span></a>
		        						</li>
		        						<li>
		        							<a rel="http://hxling.cnblogs.com" href="#" ref="12"><span class="icon icon-role">&nbsp;</span><span class="nav">积分管理</span></a>
		        						</li>
		        
		        						<li>
		        							<a rel="http://hxling.cnblogs.com" href="#" ref="12"><span class="icon icon-set">&nbsp;</span><span class="nav">积分等级</span></a>
		        						</li>
		        					</ul>
		        </div>
		    </div>

		</div>

	 
		<div id="黄药师Tabs" class="easyui-tabs" data-options="region:'center',border:'false'">
		    <div title="首页" style="padding:10px">
	            <p style="font-size:14px">jQuery EasyUI framework helps you build your web pages easily.</p>
	            <ul>
	                <li>easyui is a collection of user-interface plugin based on jQuery.</li>
	                <li>easyui provides essential functionality for building modem, interactive, javascript applications.</li>
	                <li>using easyui you don't need to write many javascript code, you usually defines user-interface by writing some HTML markup.</li>
	                <li>complete framework for HTML5 web page.</li>
	                <li>easyui save your time and scales while developing your products.</li>
	                <li>easyui is very easy but powerful.</li>
	            </ul>	            
	       	</div>
		</div>
	<script type="text/javascript">
		//退出
		function logout(){
			var url="<?php echo U('Index/logout');?>";
			$.ajax({
                async:false,
                type:"get",
                url:url,
                success: function(result){
                	if(result.status) location.href="<?php echo U('Index/index');?>";
                }
            });
		}

		//清除缓存
		function clearCache(){
			var url="<?php echo U('system/clearCache');?>";
			$.ajax({
                async:false,
                type:"get",
                url:url,
                success: function(result){
                	if(result.status) $.messager.confirm('提示消息','缓存更新成功!',function(r){location.href="<?php echo U('Index/index');?>";}); 
                	
                }
            });
		}
	</script>

</body>
</html>