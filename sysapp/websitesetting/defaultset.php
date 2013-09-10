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
	
	$set = $db->select(0, 1, 'tb_setting');
	if($set['dock'] != ''){
		$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$set['dock'].')');
		$set['dockinfo'] = $appsrs;
	}
	if($set['desk1'] != ''){
		$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$set['desk1'].')');
		$set['desk1info'] = $appsrs;
	}
	if($set['desk2'] != ''){
		$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$set['desk2'].')');
		$set['desk2info'] = $appsrs;
	}
	if($set['desk3'] != ''){
		$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$set['desk3'].')');
		$set['desk3info'] = $appsrs;
	}
	if($set['desk4'] != ''){
		$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$set['desk4'].')');
		$set['desk4info'] = $appsrs;
	}
	if($set['desk5'] != ''){
		$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$set['desk5'].')');
		$set['desk5info'] = $appsrs;
	}
	$global_title = 'defaultset';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>默认设置</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<?php include('global_title.php'); ?>
<p class="detile-title">
	<strong>用户未登录情况下的默认设置</strong>
</p>
<form action="defaultset.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<div class="input-label">
	<div class="label-text">是否开启强制登录：</div>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_isforcedlogin" value="1" <?php if($set['isforcedlogin'] == 1){echo 'checked';} ?>>是</label>
		<label class="radio"><input type="radio" name="val_isforcedlogin" value="0" <?php if($set['isforcedlogin'] == 0){echo 'checked';} ?>>否</label>
	</div>
</div>
<div class="input-label">
	<div class="label-text">应用码头默认应用：</div>
	<div class="label-box form-inline control-group">
		<div class="permissions_apps">
			<?php
				if($set['dockinfo'] != NULL){
					foreach($set['dockinfo'] as $v){
						echo '<div class="app" appid="'.$v['tbid'].'">';
							echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
							echo '<span class="del">删</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_dock" id="val_dock" value="<?php echo $set['dock']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<div class="label-text">桌面 1 默认应用：</div>
	<div class="label-box form-inline control-group">
		<div class="permissions_apps">
			<?php
				if($set['desk1info'] != NULL){
					foreach($set['desk1info'] as $v){
						echo '<div class="app" appid="'.$v['tbid'].'">';
							echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
							echo '<span class="del">删</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_desk1" id="val_desk1" value="<?php echo $set['desk1']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<div class="label-text">桌面 2 默认应用：</div>
	<div class="label-box form-inline control-group">
		<div class="permissions_apps">
			<?php
				if($set['desk2info'] != NULL){
					foreach($set['desk2info'] as $v){
						echo '<div class="app" appid="'.$v['tbid'].'">';
							echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
							echo '<span class="del">删</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_desk2" id="val_desk2" value="<?php echo $set['desk2']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<div class="label-text">桌面 3 默认应用：</div>
	<div class="label-box form-inline control-group">
		<div class="permissions_apps">
			<?php
				if($set['desk3info'] != NULL){
					foreach($set['desk3info'] as $v){
						echo '<div class="app" appid="'.$v['tbid'].'">';
							echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
							echo '<span class="del">删</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_desk3" id="val_desk3" value="<?php echo $set['desk3']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<div class="label-text">桌面 4 默认应用：</div>
	<div class="label-box form-inline control-group">
		<div class="permissions_apps">
			<?php
				if($set['desk4info'] != NULL){
					foreach($set['desk4info'] as $v){
						echo '<div class="app" appid="'.$v['tbid'].'">';
							echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
							echo '<span class="del">删</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_desk4" id="val_desk4" value="<?php echo $set['desk4']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<div class="label-text">桌面 5 默认应用：</div>
	<div class="label-box form-inline control-group">
		<div class="permissions_apps">
			<?php
				if($set['desk5info'] != NULL){
					foreach($set['desk5info'] as $v){
						echo '<div class="app" appid="'.$v['tbid'].'">';
							echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
							echo '<span class="del">删</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		<a class="btn btn-mini" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_desk5" id="val_desk5" value="<?php echo $set['desk5']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label" style="background:none;padding-left:0;text-align:center">
	<a class="btn" id="form-submit" href="javascript:;">应用</a>
</div>
</form>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	$('#form').Validform({
		btnSubmit: '#form-submit',
		postonce: false,
		showAllError: true,
		//msg：提示信息;
		//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
		//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
		tiptype: function(msg, o){
			if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
				var B = o.obj.parents('.control-group');
				var T = B.children('.errormsg');
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
			if(data.status == 'y'){
				ZENG.msgbox.show('设置已保存！', 4, 2000);
			}
		}
	});
	//添加应用
	$('a[menu=addapps]').click(function(){
		var appsBox = $(this).siblings('.permissions_apps');
		var appsidInput = $(this).next('input');
		$.dialog.data('appsid', appsidInput.val());
		$.dialog.open('sysapp/websitesetting/alert_addapps.php', {
			id : 'alert_addapps',
			title : '选择应用',
			resize: false,
			width : 360,
			height : 300,
			ok : function(){
				appsidInput.val($.dialog.data('appsid')).focusout();
				$.ajax({
					type : 'POST',
					url : 'defaultset.ajax.php',
					data : 'ac=updateApps&appsid=' + $.dialog.data('appsid'),
					success : function(msg){
						appsBox.html(msg);
					}
				});
			},
			cancel : true
		});
	});
	//删除应用
	$('.permissions_apps').on('click','.app .del',function(){
		var appid = $(this).parent().attr('appid');
		var appsid = $(this).parents('.permissions_apps').siblings('input[type="hidden"]').val().split(',');
		var newappsid = [];
		for(var i=0, j=0; i<appsid.length; i++){
			if(appsid[i] != appid){
				newappsid[j] = appsid[i];
				j++;
			}
		}
		$(this).parents('.permissions_apps').siblings('input[type="hidden"]').val(newappsid.join(',')).focusout();
		$(this).parent().remove();
	});
});
</script>
</body>
</html>