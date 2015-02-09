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
	
	$set = $db->get('tb_setting', '*');
	if($set['dock'] != ''){
		$set['dockinfo'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
			'tbid' => explode(',', $set['dock'])
		));
	}
	if($set['desk1'] != ''){
		$set['desk1info'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
			'tbid' => explode(',', $set['desk1'])
		));
	}
	if($set['desk2'] != ''){
		$set['desk2info'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
			'tbid' => explode(',', $set['desk2'])
		));
	}
	if($set['desk3'] != ''){
		$set['desk3info'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
			'tbid' => explode(',', $set['desk3'])
		));
	}
	if($set['desk4'] != ''){
		$set['desk4info'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
			'tbid' => explode(',', $set['desk4'])
		));
	}
	if($set['desk5'] != ''){
		$set['desk5info'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
			'tbid' => explode(',', $set['desk5'])
		));
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
	<strong>游客访问默认设置</strong>
</p>
<form action="defaultset.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<div class="input-label">
	<div class="label-text">是否开启游客访问：</div>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_isforcedlogin" value="0" <?php if($set['isforcedlogin'] == 0){echo 'checked';} ?>>是</label>
		<label class="radio"><input type="radio" name="val_isforcedlogin" value="1" <?php if($set['isforcedlogin'] == 1){echo 'checked';} ?>>否</label>
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
		<a class="btn" href="javascript:;" menu="addapps">添加应用</a>
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
		<a class="btn" href="javascript:;" menu="addapps">添加应用</a>
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
		<a class="btn" href="javascript:;" menu="addapps">添加应用</a>
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
		<a class="btn" href="javascript:;" menu="addapps">添加应用</a>
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
		<a class="btn" href="javascript:;" menu="addapps">添加应用</a>
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
		<a class="btn" href="javascript:;" menu="addapps">添加应用</a>
		<input type="hidden" name="val_desk5" id="val_desk5" value="<?php echo $set['desk5']; ?>">
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<label class="label-text">默认显示桌面：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_desk" value="1" <?php if($set['desk'] == 1){echo 'checked';} ?>>第1屏桌面</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_desk" value="2" <?php if($set['desk'] == 2){echo 'checked';} ?>>第2屏桌面</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_desk" value="3" <?php if($set['desk'] == 3){echo 'checked';} ?>>第3屏桌面</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_desk" value="4" <?php if($set['desk'] == 4){echo 'checked';} ?>>第4屏桌面</label>
		<label class="radio"><input type="radio" name="val_desk" value="5" <?php if($set['desk'] == 5){echo 'checked';} ?>>第5屏桌面</label>
	</div>
</div>
<div class="input-label">
	<label class="label-text">图标排列方式：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_appxy" value="x" <?php if($set['appxy'] == 'x'){echo 'checked';} ?>>横向排列</label>
		<label class="radio"><input type="radio" name="val_appxy" value="y" <?php if($set['appxy'] == 'y'){echo 'checked';} ?>>纵向排列</label>
	</div>
</div>
<div class="input-label">
	<label class="label-text">图标显示尺寸：</label>
	<div class="label-box form-inline control-group">
		<div class="input-prepend input-append">
			<button class="btn appsize-minus" type="button"><i class="icon-minus"></i></button>
			<input type="text" name="val_appsize" class="text-center span1" value="<?php echo $set['appsize']; ?>">
			<button class="btn appsize-plus" type="button"><i class="icon-plus"></i></button>
		</div>
	</div>
</div>
<div class="input-label">
	<label class="label-text">垂直间距：</label>
	<div class="label-box form-inline control-group">
		<div class="input-prepend input-append">
			<button class="btn appverticalspacing-minus" type="button"><i class="icon-minus"></i></button>
			<input type="text" name="val_appverticalspacing" class="text-center span1" value="<?php echo $set['appverticalspacing']; ?>">
			<button class="btn appverticalspacing-plus" type="button"><i class="icon-plus"></i></button>
		</div>
	</div>
</div>
<div class="input-label">
	<label class="label-text">水平间距：</label>
	<div class="label-box form-inline control-group">
		<div class="input-prepend input-append">
			<button class="btn apphorizontalspacing-minus" type="button"><i class="icon-minus"></i></button>
			<input type="text" name="val_apphorizontalspacing" class="text-center span1" value="<?php echo $set['apphorizontalspacing']; ?>">
			<button class="btn apphorizontalspacing-plus" type="button"><i class="icon-plus"></i></button>
		</div>
	</div>
</div>
<div class="input-label">
	<label class="label-text">应用码头位置：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_dockpos" value="top" <?php if($set['dockpos'] == 'top'){echo 'checked';} ?>>顶部</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_dockpos" value="left" <?php if($set['dockpos'] == 'left'){echo 'checked';} ?>>左侧</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_dockpos" value="right" <?php if($set['dockpos'] == 'right'){echo 'checked';} ?>>右侧</label>
		<label class="radio"><input type="radio" name="val_dockpos" value="none" <?php if($set['dockpos'] == 'none'){echo 'checked';} ?>>隐藏</label>
	</div>
</div>
<div class="input-label">
	<label class="label-text">默认窗口皮肤：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_skin" value="default" <?php if($set['skin'] == 'default'){echo 'checked';} ?>>默认皮肤</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_skin" value="chrome" <?php if($set['skin'] == 'chrome'){echo 'checked';} ?>>chrome皮肤</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_skin" value="ext" <?php if($set['skin'] == 'ext'){echo 'checked';} ?>>ext皮肤</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_skin" value="mac" <?php if($set['skin'] == 'mac'){echo 'checked';} ?>>mac皮肤</label>
		<label class="radio"><input type="radio" name="val_skin" value="none" <?php if($set['skin'] == 'qq'){echo 'checked';} ?>>qq皮肤</label>
	</div>
</div>
<div class="input-label">
	<label class="label-text">默认壁纸：</label>
	<div class="label-box form-inline control-group">
		<select name="val_wallpaper_id">
		<?php
			foreach($db->select('tb_wallpaper', '*') as $v){
				if($v['tbid'] == $set['wallpaper_id']){
					echo '<option value="'.$v['tbid'].'" selected>'.$v['title'].'</option>';
				}else{
					echo '<option value="'.$v['tbid'].'">'.$v['title'].'</option>';
				}
			}
		?>
		</select>
	</div>
</div>
<div class="input-label">
	<label class="label-text">壁纸显示方式：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_wallpapertype" value="tianchong" <?php if($set['wallpapertype'] == 'tianchong'){echo 'checked';} ?>>填充</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_wallpapertype" value="shiying" <?php if($set['wallpapertype'] == 'shiying'){echo 'checked';} ?>>适应</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_wallpapertype" value="pingpu" <?php if($set['wallpapertype'] == 'pingpu'){echo 'checked';} ?>>平铺</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="val_wallpapertype" value="lashen" <?php if($set['wallpapertype'] == 'lashen'){echo 'checked';} ?>>拉伸</label>
		<label class="radio"><input type="radio" name="val_wallpapertype" value="juzhong" <?php if($set['wallpapertype'] == 'juzhong'){echo 'checked';} ?>>居中</label>
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
	var updateSize = function(size){
		if(size < 32){
			size = 32;
		}else if(size > 64){
			size = 64
		}
		$('input[name="val_appsize"]').val(size);
	};
	$('.appsize-minus, .appsize-plus').click(function(){
		var size = parseInt($('input[name="val_appsize"]').val());
		if($(this).hasClass('appsize-minus')){
			size = size - 1;
		}else{
			size = size + 1;
		}
		updateSize(size);
	});
	$('input[name="val_appsize"]').keyup(function(){
		var size = parseInt($('input[name="val_appsize"]').val());
		updateSize(size);
	});
	var updateVertical = function(vertical){
		if(vertical < 0){
			vertical = 0;
		}else if(vertical > 100){
			vertical = 100
		}
		$('input[name="val_appverticalspacing"]').val(vertical);
	};
	$('.appverticalspacing-minus, .appverticalspacing-plus').click(function(){
		var vertical = parseInt($('input[name="val_appverticalspacing"]').val());
		if($(this).hasClass('appverticalspacing-minus')){
			vertical = vertical - 1;
		}else{
			vertical = vertical + 1;
		}
		updateVertical(vertical);
	});
	$('input[name="val_appverticalspacing"]').keyup(function(){
		var vertical = parseInt($('input[name="val_appverticalspacing"]').val());
		updateVertical(vertical);
	});
	var updateHorizontal = function(horizontal){
		if(horizontal < 0){
			horizontal = 0;
		}else if(horizontal > 100){
			horizontal = 100
		}
		$('input[name="val_apphorizontalspacing"]').val(horizontal);
	};
	$('.apphorizontalspacing-minus, .apphorizontalspacing-plus').click(function(){
		var horizontal = parseInt($('input[name="val_apphorizontalspacing"]').val());
		if($(this).hasClass('apphorizontalspacing-minus')){
			horizontal = horizontal - 1;
		}else{
			horizontal = horizontal + 1;
		}
		updateHorizontal(horizontal);
	});
	$('input[name="val_apphorizontalspacing"]').keyup(function(){
		var horizontal = parseInt($('input[name="val_apphorizontalspacing"]').val());
		updateHorizontal(horizontal);
	});
});
</script>
</body>
</html>