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
	<link rel="stylesheet" href="../../static/plugins/bootstrap-slider-9.8.0/dist/css/bootstrap-slider.min.css">
	<style media="screen">
		.slider.slider-horizontal{
			margin-top: 8px;
		}
	</style>
</head>
<body>
	<?php include('global_title.php'); ?>
	<form action="defaultset.ajax.php" method="post" id="form" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-body">
				<input type="hidden" name="ac" value="edit">
				<div class="form-group">
					<label class="col-sm-2 control-label">是否开启游客访问：</label>
					<div class="col-sm-10">
						<input type="checkbox" name="val_isforcedlogin" <?php if($set['isforcedlogin'] == 0){echo 'checked';} ?> data-plugin="bootstrapSwitch" data-on-color="info" data-on-text="开启" data-off-text="关闭">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">应用码头默认应用：</label>
					<div class="col-sm-10">
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
						<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
						<input type="hidden" name="val_dock" id="val_dock" value="<?php echo $set['dock']; ?>">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">桌面 1 默认应用：</label>
					<div class="col-sm-10">
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
						<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
						<input type="hidden" name="val_desk1" id="val_desk1" value="<?php echo $set['desk1']; ?>">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">桌面 2 默认应用：</label>
					<div class="col-sm-10">
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
						<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
						<input type="hidden" name="val_desk2" id="val_desk2" value="<?php echo $set['desk2']; ?>">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">桌面 3 默认应用：</label>
					<div class="col-sm-10">
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
						<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
						<input type="hidden" name="val_desk3" id="val_desk3" value="<?php echo $set['desk3']; ?>">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">桌面 4 默认应用：</label>
					<div class="col-sm-10">
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
						<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
						<input type="hidden" name="val_desk4" id="val_desk4" value="<?php echo $set['desk4']; ?>">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">桌面 5 默认应用：</label>
					<div class="col-sm-10">
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
						<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
						<input type="hidden" name="val_desk5" id="val_desk5" value="<?php echo $set['desk5']; ?>">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">默认显示桌面：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="val_desk" value="1" <?php if($set['desk'] == 1){echo 'checked';} ?>>第 1 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_desk" value="2" <?php if($set['desk'] == 2){echo 'checked';} ?>>第 2 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_desk" value="3" <?php if($set['desk'] == 3){echo 'checked';} ?>>第 3 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_desk" value="4" <?php if($set['desk'] == 4){echo 'checked';} ?>>第 4 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_desk" value="5" <?php if($set['desk'] == 5){echo 'checked';} ?>>第 5 屏桌面
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标排列方式：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="val_appxy" value="x" <?php if($set['appxy'] == 'x'){echo 'checked';} ?>>横向排列
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_appxy" value="y" <?php if($set['appxy'] == 'y'){echo 'checked';} ?>>纵向排列
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标显示尺寸：</label>
					<div class="col-sm-10">
						<input id="inputAppsize" name="val_appsize" data-slider-id='inputAppsizeSlider' type="text" data-slider-min="32" data-slider-max="128" data-slider-step="1" data-slider-ticks="[32, 48, 64, 128]" data-slider-ticks-labels='["小", "中", "大", "超大"]' data-slider-ticks-positions="[0, 25, 50, 100]" data-slider-ticks-snap-bounds="1" data-slider-value="<?php echo $set['appsize']; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标垂直间距：</label>
					<div class="col-sm-10">
						<input id="inputAppverticalspacing" name="val_appverticalspacing" data-slider-id='inputAppverticalspacingSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-ticks="[0, 25, 50, 75, 100]" data-slider-ticks-labels='["超紧缩", "紧缩", "标准", "加宽", "超加宽"]' data-slider-ticks-snap-bounds="1" data-slider-value="<?php echo $set['appverticalspacing']; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标水平间距：</label>
					<div class="col-sm-10">
						<input id="inputApphorizontalspacing" name="val_apphorizontalspacing" data-slider-id='inputApphorizontalspacingSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-ticks="[0, 25, 50, 75, 100]" data-slider-ticks-labels='["超紧缩", "紧缩", "标准", "加宽", "超加宽"]' data-slider-ticks-snap-bounds="1" data-slider-value="<?php echo $set['apphorizontalspacing']; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">应用码头位置：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="val_dockpos" value="top" <?php if($set['dockpos'] == 'top'){echo 'checked';} ?>>顶部
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_dockpos" value="left" <?php if($set['dockpos'] == 'left'){echo 'checked';} ?>>左侧
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_dockpos" value="right" <?php if($set['dockpos'] == 'right'){echo 'checked';} ?>>右侧
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_dockpos" value="none" <?php if($set['dockpos'] == 'none'){echo 'checked';} ?>>隐藏
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">默认窗口皮肤：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="val_skin" value="default" <?php if($set['skin'] == 'default'){echo 'checked';} ?>>默认皮肤
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_skin" value="chrome" <?php if($set['skin'] == 'chrome'){echo 'checked';} ?>>chrome皮肤
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_skin" value="ext" <?php if($set['skin'] == 'ext'){echo 'checked';} ?>>ext皮肤
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_skin" value="mac" <?php if($set['skin'] == 'mac'){echo 'checked';} ?>>mac皮肤
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_skin" value="none" <?php if($set['skin'] == 'qq'){echo 'checked';} ?>>qq皮肤
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">默认壁纸：</label>
					<div class="col-sm-10">
						<select name="val_wallpaper_id" class="form-control" data-plugin="bootstrapSelect">
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
				<div class="form-group">
					<label class="col-sm-2 control-label">壁纸显示方式：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="val_wallpapertype" value="tianchong" <?php if($set['wallpapertype'] == 'tianchong'){echo 'checked';} ?>>填充
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_wallpapertype" value="shiying" <?php if($set['wallpapertype'] == 'shiying'){echo 'checked';} ?>>适应
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_wallpapertype" value="pingpu" <?php if($set['wallpapertype'] == 'pingpu'){echo 'checked';} ?>>平铺
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_wallpapertype" value="lashen" <?php if($set['wallpapertype'] == 'lashen'){echo 'checked';} ?>>拉伸
						</label>
						<label class="radio-inline">
							<input type="radio" name="val_wallpapertype" value="juzhong" <?php if($set['wallpapertype'] == 'juzhong'){echo 'checked';} ?>>居中
						</label>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<a class="btn btn-primary btn-block" id="form-submit" href="javascript:;">保存设置</a>
					</div>
				</div>
			</div>
		</div>
	</form>
	<?php include('sysapp/global_js.php'); ?>
	<script src="../../static/plugins/bootstrap-slider-9.8.0/dist/bootstrap-slider.min.js"></script>
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
					swal({
						type: 'success',
						title: '操作成功',
						timer: 2000,
						showConfirmButton: false
					});
					//ZENG.msgbox.show('设置已保存！', 4, 2000);
				}
			}
		});
		//添加应用
		$('a[menu=addapps]').click(function(){
			var appsBox = $(this).siblings('.permissions_apps');
			var appsidInput = $(this).next('input');
			dialog({
				id : 'alert_addapps',
				title : '选择应用',
				url : 'alert_addapps.php',
				data : {
					appsid : appsidInput.val()
				},
				padding: 0,
				width : 360,
				height : 363,
				ok : function(){
					appsidInput.val(this.data.appsid).focusout();
					$.ajax({
						type : 'POST',
						url : 'defaultset.ajax.php',
						data : 'ac=updateApps&appsid=' + this.data.appsid
					}).done(function(msg){
						appsBox.html(msg);
					});
				},
				okValue : '确认',
				cancel : true,
				cancelValue : '取消'
			}).showModal();
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
		$('#inputAppsize').slider({
			formatter: function(value){
				return '图标尺寸：' + value;
			}
		});
		$('#inputAppverticalspacing').slider({
			formatter: function(value){
				return '图标垂直间距：' + value;
			}
		});
		$('#inputApphorizontalspacing').slider({
			formatter: function(value){
				return '图标水平间距：' + value;
			}
		});
	});
	</script>
</body>
</html>