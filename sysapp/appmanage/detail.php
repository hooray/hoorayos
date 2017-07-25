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

	if(isset($_GET['appid'])){
		$app = $db->get('tb_app', '*', array(
			'tbid' => $_GET['appid']
		));
	}else{
		//给个初始值
		$app = array(
			'type' => 'window',
			'isresize' => 1,
			'isopenmax' => 0,
			'isflash' => 0
		);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>应用管理</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<form action="detail.ajax.php" method="post" name="form" id="form" class="form-horizontal">
		<div class="title-bar">编辑应用</div>
		<div class="creatbox">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="ac" value="edit">
					<input type="hidden" name="id" value="<?php echo $_GET['appid']; ?>">
					<div class="form-group">
						<label for="inputIcon" class="col-sm-2 control-label">应用图片：</label>
						<div class="col-sm-10">
							<div class="shortcutview">
								<?php if($app['icon'] != ''){ ?>
									<img src="../../<?php echo $app['icon']; ?>">
								<?php } ?>
							</div>
							<a href="javascript:;" id="upload" class="btn btn-default" style="position:relative">选择图片</a>
							<div class="panel panel-default" style="margin-top:10px;margin-bottom:0;">
								<div class="panel-heading">系统推荐的图标：</div>
								<div class="panel-body shortcut-selicon">
									<?php for($i = 1; $i <= 40; $i++){ ?>
										<a href="javascript:;"><img src="../../static/img/icon/system/<?php echo $i; ?>.png" valsrc="static/img/icon/system/<?php echo $i; ?>.png"></a>
									<?php } ?>
								</div>
							</div>
							<input type="hidden" name="val_icon" id="inputIcon" value="<?php echo $app['icon']; ?>" datatype="*" nullmsg="请选择或上传应用图片">
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="inputName" class="col-sm-2 control-label">应用名称：</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="val_name" id="inputName" value="<?php echo $app['name']; ?>" datatype="*" nullmsg="请输入应用名称">
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="selectAppCategoryId" class="col-sm-2 control-label">应用分类：</label>
						<div class="col-sm-10">
							<select class="form-control" name="val_app_category_id" id="selectAppCategoryId" data-plugin="bootstrapSelect" datatype="*" nullmsg="请选择应用分类" title="请选择应用分类">
								<?php
								$appcategory = $db->select('tb_app_category', '*');
								foreach($appcategory as $ac){
									if($ac['tbid'] == $app['app_category_id']){
										echo '<option value="'.$ac['tbid'].'" selected>'.$ac['name'].'</option>';
									}else{
										echo '<option value="'.$ac['tbid'].'">'.$ac['name'].'</option>';
									}
								}
								?>
							</select>
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="inputUrl" class="col-sm-2 control-label">应用地址：</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="val_url" id="inputUrl" value="<?php echo $app['url']; ?>" datatype="*" nullmsg="请输入应用地址">
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">窗口大小：</label>
						<div class="col-sm-10">
							<div class="row">
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">宽</span>
										<input type="text" class="form-control" name="val_width" id="inputWidth" value="<?php echo $app['width']; ?>" datatype="n" nullmsg="请输入应用宽高" errormsg="宽高数值不规范">
										<span class="input-group-addon">px</span>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">高</span>
										<input type="text" class="form-control" name="val_height" id="inputHeight" value="<?php echo $app['height']; ?>" datatype="n" nullmsg="请输入应用宽高" errormsg="宽高数值不规范">
										<span class="input-group-addon">px</span>
									</div>
								</div>
							</div>
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">应用类型：</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="val_type" value="window" <?php if($app['type'] == 'window'){echo 'checked';} ?> <?php if(isset($_GET['appid'])){echo 'disabled';} ?>>窗口
							</label>
							<label class="radio-inline">
								<input type="radio" name="val_type" value="widget" <?php if($app['type'] == 'widget'){echo 'checked';} ?> <?php if(isset($_GET['appid'])){echo 'disabled';} ?>>挂件
							</label>
						</div>
					</div>
					<div class="form-group form-group-isresize <?php if($app['type'] == 'widget'){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">窗口是否拉伸：</label>
						<div class="col-sm-10">
							<input type="checkbox" name="val_isresize" <?php if($app['isresize'] == 1){echo 'checked';} ?> data-plugin="bootstrapSwitch" data-on-color="info" data-size="small" data-on-text="是" data-off-text="否">
						</div>
					</div>
					<div class="form-group form-group-isopenmax <?php if($app['type'] == 'widget' || $app['isresize'] == 0){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">打开默认最大化：</label>
						<div class="col-sm-10">
							<input type="checkbox" name="val_isopenmax" <?php if($app['isopenmax'] == 1){echo 'checked';} ?> data-plugin="bootstrapSwitch" data-on-color="info" data-size="small" data-on-text="是" data-off-text="否">
						</div>
					</div>
					<div class="form-group form-group-isflash <?php if($app['type'] == 'widget'){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">是否为Flash：</label>
						<div class="col-sm-10">
							<input type="checkbox" name="val_isflash" <?php if($app['isflash'] == 1){echo 'checked';} ?> data-plugin="bootstrapSwitch" data-on-color="info" data-size="small" data-on-text="是" data-off-text="否">
							<span class="help-block">如果设置为Flash应用，当窗口处于非当前窗口时，会显示遮罩层</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">应用介绍：</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="val_remark" id="textareaRremark" rows="3"><?php echo $app['remark']; ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom-bar">
			<a class="btn btn-default" id="btn-preview" href="javascript:;"><i class="glyphicon glyphicon-eye-open"></i> 预览应用</a>
			<a class="btn btn-primary pull-right" id="btn-submit" href="javascript:;"><i class="glyphicon glyphicon-ok"></i> 确定</a>
			<a class="btn btn-default pull-right" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="glyphicon glyphicon-chevron-up"></i> 返回应用列表</a>
		</div>
	</form>
	<div id="unpassinfo" class="form-inline hide">
		<label class="radio-inline">
			<input type="radio" name="unpassinfo" value="信息不完整" checked>信息不完整
		</label>
		<label class="radio-inline">
			<input type="radio" name="unpassinfo" value="应用已存在">应用已存在
		</label>
		<label class="radio-inline">
			<input type="radio" name="unpassinfo" value="内容低俗">内容低俗
		</label>
	</div>
	<?php include('sysapp/global_js.php'); ?>
	<script src="../../static/plugins/webuploader-0.1.5/webuploader.min.js"></script>
	<script>
	$(function(){
		var uploader = WebUploader.create({
			// 选完文件后，是否自动上传。
			auto: true,
			// swf文件路径
			swf: '../../static/plugins/webuploader-0.1.5/Uploader.swf',
			// 文件接收服务端。
			server: 'detail.ajax.php?ac=uploadImg',
			// 选择文件的按钮。可选。
			// 内部根据当前运行是创建，可能是input元素，也可能是flash.
			pick: {
				id: '#upload',
				multiple: false
			},
			// 只允许选择图片文件。
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			}
		});
		uploader.on('beforeFileQueued', function(file){
			if(file.size > 300 * 1024){
				alert('文件大于300Kb，请压缩后再上传');
				return false;
			}else{
				$('.shortcutview img').remove();
				$('#inputIcon').val('');
			}
		});
		uploader.on('fileQueued', function(file){
			var $img = $('<img>');
			$('.shortcutview').append($img);
			// 创建缩略图
			uploader.makeThumb(file, function(error, src){
				if(error){
					$img.replaceWith('');
					return;
				}
				$img.attr('src', src);
			}, 48, 48);
		});
		uploader.on('uploadSuccess', function(file, cb){
			$('.shortcutview img').attr('src', '../../' + cb.url);
			$('#inputIcon').val(cb.url);
			uploader.removeFile(file);
		});
		var form = $('#form').Validform({
			btnSubmit: '#btn-submit',
			postonce: false,
			showAllError: true,
			//msg：提示信息;
			//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
			//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
			tiptype: function(msg, o){
				if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
					var B = o.obj.parents('.form-group');
					var T = B.find('.help-block');
					if(o.type == 2){
						B.removeClass('has-error');
						T.text('');
					}else{
						B.addClass('has-error');
						T.text(msg);
					}
				}
			},
			ajaxPost: true,
			callback: function(data){
				if($('input[name="id"]').val() != ''){
					if(data.status == 'y'){
						window.parent.closeDetailIframe(function(){
							window.parent.$('#table').bootstrapTable('refresh');
						});
						window.parent.swal({
							type : 'success',
							title : '编辑成功'
						});
					}
				}else{
					if(data.status == 'y'){
						swal({
							type : 'success',
							title : '添加成功',
							text : '是否继续添加？',
							showCancelButton : true,
							confirmButtonText : '继续添加',
							cancelButtonText : '返回',
							closeOnConfirm : false,
							closeOnCancel : false
						}, function(isConfirm){
							if(isConfirm){
								location.reload();
							}else{
								window.parent.closeDetailIframe(function(){
									window.parent.$('#table').bootstrapTable('refresh');
								});
							}
						});
					}
				}
			}
		});
		$('input[name="val_type"]').change(function(){
			if($(this).val() == 'window'){
				$('.form-group-isresize, .form-group-isopenmax, .form-group-isflash').removeClass('hide');
			}else{
				$('input[name="val_isresize"]').bootstrapSwitch('state', true);
				$('input[name="val_isopenmax"]').bootstrapSwitch('state', false);
				$('input[name="val_isflash"]').bootstrapSwitch('state', false);
				$('.form-group-isresize, .form-group-isopenmax, .form-group-isflash').addClass('hide');
			}
		});
		$('input[name="val_isresize"]').on('switchChange.bootstrapSwitch', function(){
			if($(this).bootstrapSwitch('state')){
				$('.form-group-isopenmax').removeClass('hide');
			}else{
				$('.form-group-isopenmax').addClass('hide');
			}
		});
		//选择应用图片
		$('.shortcut-selicon a').click(function(){
			$('.shortcutview img').remove();
			$('.shortcutview').append($(this).html());
			$('#inputIcon').val($(this).children('img').attr('valsrc'));
		});
		$('#btn-preview').on('click', function(){
			if(form.check()){
				if($('input[name="val_type"]:checked').val() == 'window'){
					window.top.HROS.window.createTemp({
						title : $('input[name="val_name"]').val(),
						url : $('input[name="val_url"]').val(),
						width : $('input[name="val_width"]').val(),
						height : $('input[name="val_height"]').val(),
						isresize : $('input[name="val_isresize"]:checked').val() == 1 ? true : false,
						isopenmax : $('input[name="val_isopenmax"]:checked').val() == 1 ? true : false,
						isflash : $('input[name="val_isflash"]:checked').val() == 1 ? true : false
					});
				}else{
					window.top.HROS.widget.createTemp({
						url : $('input[name="val_url"]').val(),
						width : $('input[name="val_width"]').val(),
						height : $('input[name="val_height"]').val(),
					});
				}
			}else{
				swal({
					type : 'error',
					title : '应用无法预览',
					text : '请将内容填写完整后再尝试预览'
				});
			}
		});
	});
	</script>
</body>
</html>