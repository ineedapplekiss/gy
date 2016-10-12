<?php if (!defined('THINK_PATH')) exit();?><body>
	<table id="dg" title="会员列表" class="easyui-datagrid" style="width:100%;" url="<?php echo U('Member/memberList');?>"  toolbar="#toolbar" pagination="true" pageSize='15' pageNumber='1' multiSort='true' sortOrder="desc" pageList="[2,5,10,15,20,25,30,40,50]" rownumbers="true" fitColumns="true" singleSelect="true">
        <thead>
            <tr>
                <th field="username" width="50" sortable="true">用户名</th>
                <th field="name" sortable="true">用户组</th>
                <th field="nickname" width="50">昵称</th>
                <th field="mobile" width="50">手机</th>
                <th field="email">邮箱</th>
                <th field="vip" formatter="checkYesNo">VIP</th>
                <th field="overduedate" formatter="timestampToDate">VIP过期时间</th>
                <th field="amt" width="50" sortable="true">金额</th>
                <th field="pt" width="50" sortable="true">积分</th>
                <th field="loginnum" width="50" sortable="true">登陆次数</th>
                <th field="regdate" width="50" formatter="timestampToDateTime" sortable="true">注册时间</th>                
                <th field="regip" width="50">注册ip</th>
                <th field="lastdate" width="50" formatter="timestampToDateTime">最后登陆时间</th>
                <th field="lastip" width="50">上次登陆ip</th>
                <th field="lk" formatter="checkNoYes">启用</th>
            </tr>
        </thead>
    </table>

    <div id="toolbar">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newUser()">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editUser()">编辑</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyUser()">删除</a>
       
        <form id="fmSearch" method="post" novalidate style="display:inline-block;margin-left:50px;">
             <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon icon-all" plain="true" onclick="showAll()">全部</a>  	
       		<input id='searchBox' class="easyui-searchbox" data-options="height:30,menu:'#searchboxSelect',searcher:doSearch"  style="width:300px"/>
			    <div id="searchboxSelect">
			        <div data-options="name:'username'">用户名</div>
			        <div data-options="name:'email'">邮箱</div>
			        <div data-options="name:'mobile'">手机</div>
		    	</div>
	    
	    </form>
    </div>
	
	<!-- 会员添加 -->
	<div id="dlg" class="easyui-dialog" title="添加会员" style="width:400px;padding:10px 20px"
            closed="true" buttons="#dlg-buttons"  modal="true">
        <form id="fm" method="post" novalidate>
            <div class="fitem">
                <label>用&nbsp;&nbsp;户&nbsp;名</label>
                <input name="username" class="easyui-textbox" data-options="delay:1000,required:true,height:30,validType:['length[4,20]','exists[\'用户名\',\'<?php echo U('Member/checkUser','','');?>\']']" />
            </div>
            <div class="fitem">
                <label>会&nbsp;&nbsp;员&nbsp;组</label>
                <select class="easyui-combobox" name="groupid" id='groupid' data-options="required:true,height:30">
                    <?php if(is_array($groupInfo)): $i = 0; $__LIST__ = $groupInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>                        
                </select>
            </div>
            <div class="fitem">
                <label>密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码</label>
                <input name="pwd" id="pwd" type="password" class="easyui-textbox" data-options="required:true,height:30,delay:1000,validType:['length[6,16]','equals[\'#reppwd\']']">
            </div>
            <div class="fitem">
                <label>确认密码</label>
                <input name="reppwd" id="reppwd" type="password" class="easyui-textbox" data-options="required:true,height:30,delay:1000,validType:['length[6,16]','equals[\'#pwd\']']">
            </div>
            <div class="fitem">
                <label>邮&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;箱</label>
                <input name="email" class="easyui-textbox" data-options="
                required:true,height:30,delay:1000,validType:['email','exists[\'邮箱\',\'<?php echo U('Member/checkEmail','','');?>\']']">
            </div>
        </form>
    </div>
	
	<!-- 会员编辑 -->
	<div id="dlgEdit" class="easyui-dialog" style="padding:10px 20px;width:500px;"
            closed="true" buttons="#dlgEdit-buttons" modal="true">
        <form id="fmEdit" method="post" novalidate>
	        <div class="easyui-panel" title="基本信息" style="padding:5px;">
	            <div class="fitem">
	                <label>用&nbsp;&nbsp;户&nbsp;名:</label>
	                <input name='id' id="memberId" type="hidden" />
	                <input name="username" class="easyui-textbox" data-options="height:30" disabled="disabled" required="true">
	            </div>
	            <div class="fitem">
	                <label>密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码:</label>
	                <input id="pwdd" name="pwd" type="password" class="easyui-textbox" data-options="height:30" validType="equals['#reppwdd']">
	            </div>
	            <div class="fitem">
	                <label>确认密码:</label>
	                <input name="reppwd" id="reppwdd" type="password" class="easyui-textbox" data-options="height:30" validType="equals['#pwdd']">
	            </div>
	            <div class="fitem">
	                <label>邮&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;箱:</label>
	                <input id='emailEdit' name="email" class="easyui-textbox" data-options="height:30,
	                                required:true,
	                				delay:1000,
	                                validType:['email','exists[\'邮箱\',\'#memberId\',\'<?php echo U('Member/checkEmail','','');?>\']']">
	            </div>
	            <div class="fitem">
	                <label>昵&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;称:</label>
	                <input name="nickname" class="easyui-textbox" data-options="height:30" />
	            </div> 
	            <div class="fitem">
	                <label>手机号码:</label>
	                <input name="mobile" class="easyui-textbox" data-options="height:30" />
	            </div>
	            <div class="fitem">
	                <label>会&nbsp;&nbsp;员&nbsp;组:</label>
	                 <select class="easyui-combobox" name="gid" data-options="height:30">
						<?php if(is_array($groupInfo)): $i = 0; $__LIST__ = $groupInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?> 
					</select>	
	            </div>
				<div class="fitem">
	                <label>积分点数:</label>
	                <input name="pt" class="easyui-textbox" data-options="height:30" />
	            </div>
	            <div class="fitem">
	                <label>VIP会员:</label><input name="vip" type="checkbox" value="1" style="width:10px;"><span style="padding:0 3px 0 3px;">是否为VIP会员 过期时间 </span><input id="overduedate" name="overduedate" editable="false" class="easyui-datebox" data-options="height:30" />
	            </div>

             	<div class="fitem">
	                <label>是否启用:</label>
	                 <select class="easyui-combobox" name="lk" data-options="height:30">
						<option value="0">启用</option>
						<option value="1">禁用</option>
					</select>	
	            </div>
			</div>
            <div style="height:10px;"></div>
			<div class="easyui-panel" title="详细信息" style="padding:5px;">
			</div>
        </form>
    </div>

	<!-- 添加会员 -->
    <div id="dlg-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="addUser()" style="width:90px">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">取消</a>
    </div>
	<!-- /添加会员 -->	

	<!-- 会员编辑 -->
    <div id="dlgEdit-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveUser()" style="width:90px">保存</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgEdit').dialog('close')" style="width:90px">取消</a>
    </div>
	<!-- /会员编辑 -->

   <script type="text/javascript">
        var url;
        //添加会员对话窗
        function newUser(){
            $('#dlg').dialog('open').dialog('setTitle','添加会员');
            $('#fm').form('clear');
            $('#groupid').combobox('select',2);
            url ="<?php echo U('Member/addHandle');?>";
        }

        //编辑会员对话窗
        function editUser(){
            var row = $('#dg').datagrid('getSelected');
            if (row){
                $('#dlgEdit').dialog('open').dialog('setTitle','编辑会员');
                $('#fmEdit').form('load',row);
                var date=timestampToDate(row.overduedate);
                $('#overduedate').datebox('setValue',date);
                $('#pwdd').textbox('clear');
                $('#reppwdd').textbox('clear');
                url ="<?php echo U('Member/editHandle','','');?>"+'?id='+row.id;
            }
        }

        //添加会员
        function addUser(){
            $('#fm').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
                	 var result=eval('('+result+')');
                    if (!result.status){
                        $.messager.confirm('错误提示',result.message,function(r){
                            $('#dlg').dialog('close');  
                        });
                    } else {
                        $('#dlg').dialog('close');        // close the dialog
                        $('#dg').datagrid('reload');    // reload the user data
                    }
                }
            });
        }

        //编辑会员
        function saveUser(){
            $('#fmEdit').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
                	 var result=eval('('+result+')');
                    if (!result.status){
                        $.messager.confirm('错误提示',result.message,function(r){
                            $('#dlgEdit').dialog('close');  
                        });
                    }else{
                        $('#dlgEdit').dialog('close');        // close the dialog
                        $('#dg').datagrid('reload');    // reload the user data
                    }
                }
            });
        }

        //删除会员
        function destroyUser(){
            var row = $('#dg').datagrid('getSelected');
            if (row){
                $.messager.confirm('删除提示','真的要删除此会员吗?删除将不能再恢复!',function(r){
                    if (r){
                    	var durl='<?php echo U("Member/delHandle");?>';
                        $.post(durl,{id:row.id,M:'m'},function(result){
                            if (result.status){
                                $('#dg').datagrid('reload');    // reload the user data
                            } else {
                                $.messager.alert('错误提示',result.message,'error');
                            }
                        },'json').error(function(data){
                            var info=eval('('+data.responseText+')');
                            $.messager.confirm('错误提示',info.message,function(r){
                                
                            });
                        });
                    }
                });
            }
        }

        //搜索
        function doSearch(value,name){
        	if(value==''){
        		$.messager.alert('搜索提示','搜索内容不能为空!','error');
        	}else{
        		var searchName=$('#searchBox').searchbox('getName');
        		var searchValue=$('#searchBox').searchbox('getValue');
        		switch(searchName){
        			case 'username':
        				$('#dg').datagrid({ queryParams:{username:searchValue}});
        				break;
        			case 'email':
        				$('#dg').datagrid({ queryParams:{email:searchValue}});
        				break;
        			case 'mobile':
        				$('#dg').datagrid({ queryParams:{mobile:searchValue}});
        				break;
        		}
        	}
        }

        //显示全部数据
        function showAll(){
        	$('#dg').datagrid({ queryParams:''});
        }

       

    </script>
    <style type="text/css">
        #fm{
            margin:0;
            padding:10px;
        }
    </style>
	
</body>