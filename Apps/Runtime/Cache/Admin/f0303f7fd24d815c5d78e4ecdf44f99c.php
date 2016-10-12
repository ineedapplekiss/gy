<?php if (!defined('THINK_PATH')) exit();?><body>
	<script type="text/javascript">
		var authGroupUrl="<?php echo U('Auth/groupList');?>";
        var authGroupDelUrl="<?php echo U('Auth/groupDel');?>";
        var authGroupShowUrl="<?php echo U('Auth/groupUserList');?>";
		var authAccessListUrl="<?php echo U('Auth/accessList');?>";
		using('../../../default/js/authGroup.js');
	</script>
	<table id="authGroupDatagrid"></table>
	<!-- 角色添加 -->
	<div id="authGroupAddDialog" class="easyui-dialog" title="添加角色" style="width:500px;padding:10px 20px"
        closed="true" buttons="#authGroupAddButtons"  modal="true">
         <form id="authGroupAddForm" method="post" novalidate>
         	<div class="fitem">
                <label>角色名称</label>
                <input name="title" class="easyui-textbox" data-options="height:30,required:true,validType:['length[2,20]','exists[\'角色\',\'<?php echo U('Auth/checkGroup','','');?>\']']" />
            </div>
            <div class="fitem">
                <label>角色描述</label>
                <input name="describe" class="easyui-textbox" data-options="height:30"/>
            </div><div class="fitem">
                <label>状态</label>
               <select class="easyui-combobox" name="status" id='authGroupStatus' data-options="required:true,height:30,editable:false">
               		<option value="0">禁用</option>   
                    <option value="1">启用</option>  
                </select>
            </div>
         </form>
    </div>
    <div id="authGroupAddButtons">
    	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="authGroupObj.add('<?php echo U('Auth/groupAdd');?>')" style="width:90px">添加</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#authGroupAddDialog').dialog('close')" style="width:90px">取消</a>
    </div>
	<!-- /角色添加 -->

	<!-- 角色编辑 -->
    <div id="authGroupEditDialog" class="easyui-dialog" style="padding:10px 20px;width:500px;"
            closed="true" buttons="#authGroupEditButtons" modal="true">
        <form id="authGroupEditForm" method="post" novalidate>
            <div class="fitem">
                <label>角色名称</label>
                <input name='id' id="authGroupId" type="hidden" />
                <input name="title" class="easyui-textbox" data-options="height:30,required:true,validType:['length[2,20]','exists[\'角色\',\'#authGroupId\',\'<?php echo U('Auth/checkGroup','','');?>\']']" />
            </div>
            <div class="fitem">
                <label>角色描述</label>
                <input name="describe" class="easyui-textbox" data-options="height:30"/>
            </div>
            <div class="fitem">
                <label>状态</label>
               <select class="easyui-combobox" name="status" id='authGroupStatus' data-options="required:true,height:30,editable:false">
                    <option value="0">禁用</option>   
                    <option value="1">启用</option>  
                </select>
            </div>
        </form>
    </div>
    <div id="authGroupEditButtons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="authGroupObj.save('<?php echo U('Auth/groupSave');?>'+'?id='+$('#authGroupId').val())" style="width:90px">保存</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#authGroupEditDialog').dialog('close')" style="width:90px">取消</a>
    </div>
    <!-- /角色编辑 -->
    <!-- 权限设置 -->
    <div id="authAccessSetDialog" class="easyui-dialog" style="padding:10px 20px;width:500px;"
            closed="true" buttons="#authAccessSetButtons" modal="true">
        <!-- <div class="easyui-panel" id="authAccessSetTreePanel">
            
        </div> -->
        <ul id="authAccessSetTree"></ul>
    </div>
    <!-- /权限设置 -->
    <div id="authAccessSetButtons">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="authGroupObj.set('<?php echo U('Auth/AccessSet');?>')" style="width:90px">保存</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#authAccessSetDialog').dialog('close')" style="width:90px">取消</a>
    </div>    
</body>