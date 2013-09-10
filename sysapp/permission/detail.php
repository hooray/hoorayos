<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	//验证是否为管理员
	else if(!checkAdmin()){
		redirect('../error.php?code='.$errorcode['noAdmin']);
	}
	//验证是否有权限
	else if(!checkPermissions(1)){
		redirect('../error.php?code='.$errorcode['noPermissions']);
	}
	
	if(isset($permissionid)){
		$permission = $db->select(0, 1, 'tb_permission', '*', 'and tbid = '.$permissionid);
		if($permission['apps_id'] != ''){
			$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$permission['apps_id'].')');
			$permission['appsinfo'] = $appsrs;
		}
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>权限管理</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<form action="detail.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<input type="hidden" name="id" value="<?php echo $permissionid; ?>">
<div class="creatbox">
	<div class="middle">
		<p class="detile-title">
			<strong>编辑权限</strong>
		</p>
		<div class="input-label">
			<label class="label-text">权限名称：</label>
			<div class="label-box form-inline control-group">
				<input type="text" name="val_name" value="<?php echo $permission['name']; ?>" datatype="*" nullmsg="请输入权限名称">
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">专属应用：</label>
			<div class="label-box form-inline control-group">
				<div class="permissions_apps">
					<?php
						if($permission['appsinfo'] != NULL){
							foreach($permission['appsinfo'] as $v){
								echo '<div class="app" appid="'.$v['tbid'].'">';
									echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
									echo '<span class="del">删</span>';
								echo '</div>';
							}
						}
					?>
				</div>
				<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
				<input type="hidden" name="val_apps_id" id="val_apps_id" value="<?php echo $permission['apps_id']; ?>" datatype="*" nullmsg="请选择专属应用">
				<span class="help-inline"></span>
			</div>
		</div>
	</div>
</div>
<div class="bottom-bar">
	<div class="con">
		<a class="btn btn-primary fr" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
		<a class="btn fr" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="icon-chevron-up"></i> 返回权限列表</a>
	</div>
</div>
</form>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	$('#form').Validform({
		btnSubmit: '#btn-submit',
		postonce: false,
		showAllError: true,
		//msg：提示信息;
		//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
		//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
		tiptype: function(msg, o){
			if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
				var B = o.obj.parents('.control-group');
				var T = B.children('.help-inline');
				if(o.type == 2){
					B.removeClass('error');
					T.text('');
				}else{
					B.addClass('error');
					T.text(msg);
				}
			}
		},
		ajaxPost: true,
		callback: function(data){
			if($('input[name="id"]').val() != ''){
				if(data.status == 'y'){
					$.dialog({
						id : 'ajaxedit',
						content : '修改成功，是否继续修改？',
						okVal: '是',
						ok : function(){
							$.dialog.list['ajaxedit'].close();
						},
						cancel : function(){
							window.parent.closeDetailIframe(function(){
								window.parent.$('#pagination').trigger('currentPage');
							});
						}
					});
				}
			}else{
				if(data.status == 'y'){
					$.dialog({
						id : 'ajaxedit',
						content : '添加成功，是否继续添加？',
						okVal: '是',
						ok : function(){
							location.reload();
							return false;
						},
						cancel : function(){
							window.parent.closeDetailIframe(function(){
								window.parent.$('#pagination').trigger('currentPage');
							});
						}
					});
				}
			}
		}
	});
	//添加应用
	$('a[menu=addapps]').click(function(){
		$.dialog.data('appsid', $('#val_apps_id').val());
		$.dialog.open('sysapp/permission/alert_addapps.php', {
			id : 'alert_addapps',
			title : '添加应用',
			resize: false,
			width : 360,
			height : 300,
			ok : function(){
				$('#val_apps_id').val($.dialog.data('appsid')).focusout();
				$.ajax({
					type : 'POST',
					url : 'detail.ajax.php',
					data : 'ac=updateApps&appsid=' + $.dialog.data('appsid'),
					success : function(msg){
						$('.permissions_apps').html(msg);
					}
				});
			},
			cancel : true
		});
	});
	//删除应用
	$('.permissions_apps').on('click','.app .del',function(){
		var appid = $(this).parent().attr('appid');
		var appsid = $('#val_apps_id').val().split(',');
		var newappsid = [];
		for(var i=0, j=0; i<appsid.length; i++){
			if(appsid[i] != appid){
				newappsid[j] = appsid[i];
				j++;
			}
		}
		$('#val_apps_id').val(newappsid.join(',')).focusout();
		$(this).parent().remove();
	});
});
</script>
</body>
</html>