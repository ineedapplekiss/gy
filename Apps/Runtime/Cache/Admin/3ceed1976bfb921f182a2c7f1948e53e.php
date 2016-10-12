<?php if (!defined('THINK_PATH')) exit();?><body>
	<table id="dgGroup" title="会员组列表" class="easyui-datagrid" style="width:100%;" url="<?php echo U('Member/groupList');?>"  toolbar="#toolbarGroup" pagination="true" pageSize='10' pageNumber='1' multiSort='true' sortOrder="desc" pageList="[2,5,10,15,20,25,30,40,50]" rownumbers="true" fitColumns="true" onClickRow='onClickRow' singleSelect="true">
        <thead>
            <tr>
                <th data-options="field:'sort',sortable:true,editor:{type:'numberbox'}">排序</th>
                <th field="name">用户组名</th>
                <th field="issystem" formatter='checkYesNo'>系统组</th>
                <th field="nums" width="50">会员数</th>
                <th data-options="field:'starnum',sortable:true,editor:{type:'numberbox'}" width="50">星星数</th>
                <th field="point" sortable="true" width="50">积分小于</th>
                <th field="allowattachment" formatter='checkYesNo'>允许上传附件</th>
                <th field="allowpost" formatter='checkYesNo'>投稿权限</th>
                <th field="allowpostverify" formatter='checkYesNo'>投稿不需审核</th>
                <th field="allowsearch" formatter='checkYesNo'>搜索权限</th>
                <th field="allowupgrade" formatter='checkYesNo'>自助升级</th>                
                <th field="allowsendmessage" formatter='checkYesNo'>发短消息</th>
                <th field="price_d" sortable="true" width="50">升级价格(包日)</th>
                <th field="price_m" sortable="true" width="50">升级价格(包月)</th>
                <th field="price_y" sortable="true" width="50">升级价格(包年)</th>
            </tr>
        </thead>
    </table>

    <div id="toolbarGroup">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newGroup()">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editGroup()">编辑</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyGroup()">删除</a>
    </div>
	
	<!-- 会员组添加 -->
	<div id="dlgGroup" class="easyui-dialog" style="width:500px;padding:10px 20px"
            closed="true" buttons="#dlgGroup-buttons"  modal="true">
        <form id="fmGroup" method="post" novalidate>
            <div class="easyui-panel" title="基本信息" style="padding:5px;">
                <div class="fitem">
                    <label>会员组名称</label>
                    <input name="name" class="easyui-textbox" data-options="required:true,height:30,delay:1000,validType:['length[2,10]','exists[\'会员组\',\'<?php echo U('Member/checkGroup','','');?>\']']"/>
                </div>
                <div class="fitem">
                    <label>积分小于</label>
                    <input name="groupPoint" id="groupPoint" data-options="height:30,min:0,max:65535,prompt:'只能填数字'" class="easyui-numberbox" />
                </div>
                <div class="fitem">
                    <label>星星数</label>
                    <input name="starnum" class="easyui-numberbox" data-options="height:30,min:0,max:65535,prompt:'只能填数字'" />
                </div>
            </div>
            <div style="height:10px;"></div>
            <div class="easyui-panel" title="详细信息" style="padding:5px;">
                 <div class="fitem">
                    <label>&nbsp;</label>
                   <input name="allowpost" id="allowpost" type="checkbox" value="1" style="width:10px;"><label for="allowpost" style="padding:0 3px 0 3px;cursor:pointer">允许投稿&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                   <input name="allowpostverify" id="allowpostverify" type="checkbox" value="1" style="width:10px;"><label for="allowpostverify" style="padding:0 3px 0 3px;cursor:pointer">投稿不需审核</label>
                </div> 
                <div class="fitem">
                    <label>用户权限</label>
                   <input name="allowupgrade" id="allowupgrade" type="checkbox" value="1" style="width:10px;"><label for="allowupgrade" style="padding:0 3px 0 3px;cursor:pointer">允许自助升级</label>
                   <input name="allowsendmessage" id="allowsendmessage" type="checkbox" value="1" style="width:10px;"><label for="allowsendmessage" style="padding:0 3px 0 3px;cursor:pointer">允许发短消息</label>
                </div> 
                <div class="fitem">
                    <label>&nbsp;</label>
                   <input name="allowattachment" id="allowattachment" type="checkbox" value="1" style="width:10px;"><label for="allowattachment" style="padding:0 3px 0 3px;cursor:pointer">允许上传附件</label>
                   <input name="allowsearch"  id="allowsearch" type="checkbox" value="1" style="width:10px;"><label for="allowsearch" style="padding:0 3px 0 3px;cursor:pointer">搜索权限</label>
                </div> 
                <div class="fitem">
                    <label>升级价格</label>
                    <span style="padding:0 3px 0 3px;">包日:</span><input name="price_d" class="easyui-numberbox" data-options="height:30,precision:2" style="width:55px;">
                    <span style="padding:0 3px 0 3px;">包月:</span><input name="price_m" class="easyui-numberbox" data-options="height:30,precision:2" style="width:55px;">
                    <span style="padding:0 3px 0 3px;">包年:</span><input name="price_y" class="easyui-numberbox" data-options="height:30,precision:2" style="width:55px;">
                </div> 
                <div class="fitem">
                    <label>最大短消息数</label>
                    <input name="allowmessage" class="easyui-numberbox" data-options="height:30,prompt:'只能填数字'" />
                </div>
                <div class="fitem">
                    <label>日最大投稿数</label>
                    <input name="allowpostnum" class="easyui-numberbox" data-options="height:30,prompt:'只能填数字'" />
                </div> 
                <div class="fitem">
                    <label>用户名颜色</label>
                    <input name="usernamecolor" id="usernamecolor" class="easyui-textbox" data-options="height:30" />
                </div> 
                <div class="fitem">
                    <label>用户组图标</label>
                    <input name="icon" class="easyui-textbox" id="icon" data-options="height:30" />
                </div> 
                <div class="fitem">
                    <label>简洁描述</label>
                    <input name="description" class="easyui-textbox" data-options="height:30" />
                </div>
            </div>
        </form>
    </div>
	<!-- /会员组添加 -->

	<!-- 会员组编辑 -->
	<div id="dlgGroupEditGroup" class="easyui-dialog" style="width:500px;padding:10px 20px"
            closed="true" buttons="#dlgGroupEditGroup-buttons" modal="true">
        <form id="fmEditGroup" method="post" novalidate>
	      <div class="easyui-panel" title="基本信息" style="padding:5px;">
                <div class="fitem">
                    <label>会员组名称</label>
                    <input type="hidden" name="id" />
                    <input name="name" class="easyui-textbox" data-options="disabled:true,height:30" />
                </div>
                <div class="fitem">
                    <label>排序</label>
                    <input name="sort" class="easyui-numberbox" data-options="height:30,required:true,min:0,max:250,prompt:'只能填数字'" />
                </div>
                <div class="fitem">
                    <label>积分小于</label>
                    <input name="groupPoint" id="groupPoint" data-options="height:30,min:0,max:65535,prompt:'只能填数字'" class="easyui-numberbox" />
                </div>
                <div class="fitem">
                    <label>星星数</label>
                    <input name="starnum" class="easyui-numberbox" data-options="height:30,min:0,max:65535,prompt:'只能填数字'" />
                </div>
            </div>
            <div style="height:10px;"></div>
             <div class="easyui-panel" title="详细信息" style="padding:5px;">
                 <div class="fitem">
                    <label>&nbsp;</label>
                   <input name="allowpost" id="allowpost1" type="checkbox" value="1" style="width:10px;"><label for="allowpost1" style="padding:0 3px 0 3px;cursor:pointer">允许投稿&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                   <input name="allowpostverify" id="allowpostverify1" type="checkbox" value="1" style="width:10px;"><label for="allowpostverify1" style="padding:0 3px 0 3px;cursor:pointer">投稿不需审核</label>
                </div> 
                <div class="fitem">
                    <label>用户权限</label>
                   <input name="allowupgrade" id="allowupgrade1" type="checkbox" value="1" style="width:10px;"><label for="allowupgrade1" style="padding:0 3px 0 3px;cursor:pointer">允许自助升级</label>
                   <input name="allowsendmessage" id="allowsendmessage1" type="checkbox" value="1" style="width:10px;"><label for="allowsendmessage1" style="padding:0 3px 0 3px;cursor:pointer">允许发短消息</label>
                </div> 
                <div class="fitem">
                    <label>&nbsp;</label>
                   <input name="allowattachment" id="allowattachment1" type="checkbox" value="1" style="width:10px;"><label for="allowattachment1" style="padding:0 3px 0 3px;cursor:pointer">允许上传附件</label>
                   <input name="allowsearch"  id="allowsearch1" type="checkbox" value="1" style="width:10px;"><label for="allowsearch1" style="padding:0 3px 0 3px;cursor:pointer">搜索权限</label>
                </div> 
                <div class="fitem">
                    <label>升级价格</label>
                    <span style="padding:0 3px 0 3px;">包日:</span><input name="price_d" class="easyui-numberbox" data-options="height:30,precision:2" style="width:55px;">
                    <span style="padding:0 3px 0 3px;">包月:</span><input name="price_m" class="easyui-numberbox" data-options="height:30,precision:2" style="width:55px;">
                    <span style="padding:0 3px 0 3px;">包年:</span><input name="price_y" class="easyui-numberbox" data-options="height:30,precision:2" style="width:55px;">
                </div> 
                <div class="fitem">
                    <label>最大短消息数</label>
                    <input name="allowmessage" class="easyui-numberbox" data-options="height:30,prompt:'只能填数字'" />
                </div>
                <div class="fitem">
                    <label>日最大投稿数</label>
                    <input name="allowpostnum" class="easyui-numberbox" data-options="height:30,prompt:'只能填数字'" />
                </div> 
                <div class="fitem">
                    <label>用户名颜色</label>
                    <input name="usernamecolor" class="easyui-textbox" data-options="height:30" />
                </div> 
                <div class="fitem">
                    <label>用户组图标</label>
                    <input name="icon" class="easyui-textbox" data-options="height:30" />
                </div> 
                <div class="fitem">
                    <label>简洁描述</label>
                    <input name="description" class="easyui-textbox" data-options="height:30" />
                </div>
            </div>
        </form>
    </div>

	<!-- 添加会员组 -->
    <div id="dlgGroup-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="addGroup()" style="width:90px">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgGroup').dialog('close')" style="width:90px">取消</a>
    </div>
	<!-- /添加会员组 -->	

	<!-- 会员编辑 -->
    <div id="dlgGroupEditGroup-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveGroup()" style="width:90px">保存</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgGroupEditGroup').dialog('close')" style="width:90px">取消</a>
    </div>
	<!-- /会员编辑 -->

   <script type="text/javascript">
        var url;
        //添加会员组对话窗
        function newGroup(){
            $('#dlgGroup').dialog('open').dialog('setTitle','添加会员组');
            $('#fmGroup').form('clear');
            $('#usernamecolor').textbox('setValue','#000000');
            $('#icon').textbox('setValue','images/group/vip.jpg');
            url ="<?php echo U('Member/groupAdd');?>";
        }

        //编辑会员对话窗
        function editGroup(){
            var row = $('#dgGroup').datagrid('getSelected');
            if (row){
                $('#dlgGroupEditGroup').dialog('open').dialog('setTitle','编辑会员');
                $('#fmEditGroup').form('load',row);
                
                url ="<?php echo U('Member/groupAdd','','');?>"+'?id='+row.id;
            }
        }

        //添加会员组
        function addGroup(){
            $('#fmGroup').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
                	 var result=eval('('+result+')');
                    if (!result.status){
                        $.messager.alert('错误提示',result.message,'error');
                    } else {
                        $('#dlgGroup').dialog('close');        // close the dialog
                        $('#dgGroup').datagrid('reload');    // reload the user data
                    }
                }
            });
        }

        //编辑会员组
        function saveGroup(){
            $('#fmEditGroup').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
                	 var result=eval('('+result+')');
                    if (!result.status){
                        $.messager.alert('错误提示',result.message,'error');
                    }else{
                        $('#dlgGroupEditGroup').dialog('close');        // close the dialog
                        $('#dgGroup').datagrid('reload');    // reload the user data
                    }
                }
            });
        }

        //删除会员
        function destroyGroup(){
            var row =$('#dgGroup').datagrid('getSelected');
            if (row){
                if(row.issystem==1){
                    $.messager.alert('错误提示','该会员组属于系统组,不能删除','error');
                    return false;
                }
                $.messager.confirm('删除提示','真的要删除此会员组吗?删除将不能再恢复!',function(r){
                    if (r){
                    	var durl='<?php echo U("Member/delHandle");?>';
                        $.post(durl,{id:row.id,issystem:row.issystem,M:'g'},function(result){
                            if (result.status){
                                $('#dgGroup').datagrid('reload');    // reload the user data
                            } else {
                                $.messager.alert('错误提示',result.message,'error');
                            }
                        },'json');
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
        			case 'name':
        				$('#dgGroup').datagrid({ queryParams:{name:searchValue}});
        				break;
        			case 'email':
        				$('#dgGroup').datagrid({ queryParams:{email:searchValue}});
        				break;
        			case 'mobile':
        				$('#dgGroup').datagrid({ queryParams:{mobile:searchValue}});
        				break;
        		}
        	}
        }

      /** 
        *ajax更新单个字段记录
        * 通用
        *$param string  url 请求路径
        *$param string  model 模型
        *$param string  whereType 条件字段
        *$param string  whereValue 条件值
        *$param string  dataType 更新字段
        *$param string  dataValue 更新值
        *@author 黄药师 <46914685@qq.com> 20141214
        */
       function ajaxUpdateSingle(url,model,whereType,whereValue,dataType,dataValue){
            var data={};
            data.model=model;
            data.whereType=whereType;
            data.whereValue=whereValue;
            data.dataType=dataType;
            data.dataValue=dataValue;
            data.model=model;
            $.ajax({
                async:false,
                type:"POST",
                url:url,
                data:data,
                success: function(result){
                    if(result.status) return true;
                    else return true;
                }
            });
       }    

    </script>
    <style type="text/css">
        #fmGroup{
            margin:0;
            padding:10px;
        }
        
    </style>
	
</body>