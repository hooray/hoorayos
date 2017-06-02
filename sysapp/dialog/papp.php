<?php
	require('../../global.php');

	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}

	if(isset($_GET['id'])){
		$app = $db->get('tb_member_app', '*', array(
			'tbid' => $_GET['id']
		));
	}else{
		$app = array(
			'icon' => 'static/img/papp.png',
			'type' => 'pwindow',
			'isresize' => 1,
			'isopenmax' => 0,
			'isflash' => 0
		);
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>编辑私人应用</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<form action="papp.ajax.php" method="post" name="form" id="form" class="form-horizontal">
		<div class="creatbox" style="top:0;">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="ac" value="edit">
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
					<input type="hidden" name="desk" value="<?php echo $_GET['desk']; ?>">
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
									<a href="javascript:;"><img src="../../static/img/system-gear.png" valsrc="static/img/system-gear.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-users.png" valsrc="static/img/system-users.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-wrench.png" valsrc="static/img/system-wrench.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-star.png" valsrc="static/img/system-star.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-shapes.png" valsrc="static/img/system-shapes.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-chart-bar.png" valsrc="static/img/system-chart-bar.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-document-edit.png" valsrc="static/img/system-document-edit.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-documents.png" valsrc="static/img/system-documents.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-mail.png" valsrc="static/img/system-mail.png"></a>
									<a href="javascript:;"><img src="../../static/img/system-puzzle.png" valsrc="static/img/system-puzzle.png"></a>
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
								<input type="radio" name="val_type" value="pwindow" <?php if($app['type'] == 'pwindow'){echo 'checked';} ?> <?php if(isset($_GET['appid'])){echo 'disabled';} ?>>窗口
							</label>
							<label class="radio-inline">
								<input type="radio" name="val_type" value="pwidget" <?php if($app['type'] == 'pwidget'){echo 'checked';} ?> <?php if(isset($_GET['appid'])){echo 'disabled';} ?>>挂件
							</label>
						</div>
					</div>
					<div class="form-group form-group-isresize <?php if($app['type'] == 'widget'){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">窗口是否拉伸：</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="val_isresize" value="1" <?php if($app['isresize'] == 1){echo 'checked';} ?>>是
							</label>
							<label class="radio-inline">
								<input type="radio" name="val_isresize" value="0" <?php if($app['isresize'] == 0){echo 'checked';} ?>>否
							</label>
						</div>
					</div>
					<div class="form-group form-group-isopenmax <?php if($app['type'] == 'widget' || $app['isresize'] == 0){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">打开默认最大化：</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="val_isopenmax" value="1" <?php if($app['isopenmax'] == 1){echo 'checked';} ?>>是
							</label>
							<label class="radio-inline">
								<input type="radio" name="val_isopenmax" value="0" <?php if($app['isopenmax'] == 0){echo 'checked';} ?>>否
							</label>
						</div>
					</div>
					<div class="form-group form-group-isflash <?php if($app['type'] == 'widget'){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">是否为Flash：</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="val_isflash" value="1" <?php if($app['isflash'] == 1){echo 'checked';} ?>>是
							</label>
							<label class="radio-inline">
								<input type="radio" name="val_isflash" value="0" <?php if($app['isflash'] == 0){echo 'checked';} ?>>否
							</label>
							<span class="help-block">如果设置为Flash应用，当窗口处于非当前窗口时，会显示遮罩层</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom-bar">
			<a class="btn btn-primary pull-right" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
			<a class="btn btn-default pull-right" href="javascript:window.parent.dialog.get('editdialog').close().remove();" style="margin-right:10px"><i class="icon-remove"></i> 关闭</a>
		</div>
	</form>
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
			server: 'papp.ajax.php?ac=uploadImg',
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
				$('#val_icon').val('');
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
			$('#val_icon').val(cb.url);
			uploader.removeFile(file);
		});
		$('#form').Validform({
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
				if(data.status == 'y'){
					window.parent.HROS.app.get();
					window.parent.dialog.get('editdialog').close().remove();
				}
			}
		});
		$('input[name="val_type"]').change(function(){
			if($(this).val() == 'pwindow'){
				$('.form-group-isresize, .form-group-isopenmax, .form-group-isflash').removeClass('hide');
			}else{
				$('input[name="val_isresize"]').each(function(){
					if($(this).val() == '1'){
						$(this).prop('checked', true);
					}
				});
				$('input[name="val_isopenmax"]').each(function(){
					if($(this).val() == '0'){
						$(this).prop('checked', true);
					}
				});
				$('input[name="val_isflash"]').each(function(){
					if($(this).val() == '0'){
						$(this).prop('checked', true);
					}
				});
				$('.form-group-isresize, .form-group-isopenmax, .form-group-isflash').addClass('hide');
			}
		});
		$('input[name="val_isresize"]').change(function(){
			if($(this).val() == '1'){
				$('.form-group-isopenmax').removeClass('hide');
			}else{
				$('.form-group-isopenmax').addClass('hide');
			}
		});
		//选择应用图片
		$('.shortcut-selicon a').click(function(){
			$('.shortcutview img').remove();
			$('.shortcutview').append($(this).html());
			$('#val_icon').val($(this).children('img').attr('valsrc')).focusout();
		});
	});
	</script>
</body>
</html>