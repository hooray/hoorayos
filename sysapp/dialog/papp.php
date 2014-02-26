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
			'icon' => 'img/ui/papp.png',
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
<link rel="stylesheet" href="../../img/ui/sys.css">
<style type="text/css">
.creatbox .middle{bottom:47px}
.bottom-bar{height:48px}
.bottom-bar .con{height:28px;background:#fff}
</style>
</head>

<body>
<form action="papp.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="desk" value="<?php echo $_GET['desk']; ?>">
<div class="creatbox">
	<div class="middle">
		<div class="input-label">
			<label class="label-text">应用图片：</label>
			<div class="label-box form-inline control-group">
				<div class="shortcutview">
					<?php if($app['icon'] != ''){ ?>
						<img src="../../<?php echo $app['icon']; ?>">
					<?php } ?>
				</div>
				<a href="javascript:;" id="upload" class="btn fl" style="position:relative">选择图片</a>
				<div class="shortcut-selicon">
					<div class="title">系统推荐的图标：</div>
					<a href="javascript:;"><img src="../../img/ui/system-gear.png" valsrc="img/ui/system-gear.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-users.png" valsrc="img/ui/system-users.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-wrench.png" valsrc="img/ui/system-wrench.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-star.png" valsrc="img/ui/system-star.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-shapes.png" valsrc="img/ui/system-shapes.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-chart-bar.png" valsrc="img/ui/system-chart-bar.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-document-edit.png" valsrc="img/ui/system-document-edit.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-documents.png" valsrc="img/ui/system-documents.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-mail.png" valsrc="img/ui/system-mail.png"></a>
					<a href="javascript:;"><img src="../../img/ui/system-puzzle.png" valsrc="img/ui/system-puzzle.png"></a>
				</div>
				<input type="hidden" name="val_icon" id="val_icon" value="<?php echo $app['icon']; ?>" datatype="*" nullmsg="请选择或上传应用图片">
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">应用名称：</label>
			<div class="label-box form-inline control-group">
				<input type="text" class="text" name="val_name" value="<?php echo $app['name']; ?>" datatype="*" nullmsg="请输入应用名称">
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">应用地址：</label>
			<div class="label-box form-inline control-group">
				<input type="text" name="val_url" value="<?php echo $app['url']; ?>" style="width:300px" datatype="*" nullmsg="请输入应用地址">
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">窗口大小：</label>
			<div class="label-box form-inline control-group">
				<div class="input-prepend input-append">
					<span class="add-on">宽</span><input type="text" name="val_width" value="<?php echo $app['width']; ?>" style="width:40px" datatype="n" nullmsg="请输入应用宽高" errormsg="宽高数值不规范"><span class="add-on">px</span>
				</div>
				<div class="input-prepend input-append" style="margin-left:10px">
					<span class="add-on">高</span><input type="text" name="val_height" value="<?php echo $app['height']; ?>" style="width:40px" datatype="n" nullmsg="请输入应用宽高" errormsg="宽高数值不规范"><span class="add-on">px</span>
				</div>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">应用类型：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_type" value="pwindow" <?php if($app['type'] == 'pwindow'){echo 'checked';} ?>>窗口</label>
				<label class="radio"><input type="radio" name="val_type" value="pwidget" <?php if($app['type'] == 'pwidget'){echo 'checked';} ?>>挂件</label>
			</div>
		</div>
		<div class="input-label input-label-isresize" <?php if($app['type'] == 'pwidget'){echo 'style="display:none"';} ?>>
			<label class="label-text">窗口是否拉伸：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isresize" value="1" <?php if($app['isresize'] == 1){echo 'checked';} ?>>是</label>
				<label class="radio"><input type="radio" name="val_isresize" value="0" <?php if($app['isresize'] == 0){echo 'checked';} ?>>否</label>
			</div>
		</div>
		<div class="input-label input-label-isopenmax" <?php if($app['type'] == 'pwidget' || $app['isresize'] == 0){echo 'style="display:none"';} ?>>
			<label class="label-text">打开默认最大化：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isopenmax" value="1" <?php if($app['isopenmax'] == 1){echo 'checked';} ?>>是</label>
				<label class="radio"><input type="radio" name="val_isopenmax" value="0" <?php if($app['isopenmax'] == 0){echo 'checked';} ?>>否</label>
			</div>
		</div>
		<div class="input-label input-label-isflash" <?php if($app['type'] == 'pwidget'){echo 'style="display:none"';} ?>>
			<label class="label-text">是否为Flash：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isflash" value="1" <?php if($app['isflash'] == 1){echo 'checked';} ?>>是</label>
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isflash" value="0" <?php if($app['isflash'] == 0){echo 'checked';} ?>>否</label>
				<span class="txt">[<a href="javascript:;" rel="tooltip" title="如何设置为Flash应用，当窗口非当前窗口时，会显示遮罩层">?</a>]</span>
			</div>
		</div>
	</div>
</div>
<div class="bottom-bar">
	<div class="con">
		<a class="btn fr" href="javascript:window.parent.$.dialog.list['editdialog'].close();"><i class="icon-remove"></i> 关闭</a>
		<a class="btn btn-primary fr" id="btn-submit" href="javascript:;" style="margin-right:10px"><i class="icon-white icon-ok"></i> 确定</a>
	</div>
</div>
</form>
<?php include('sysapp/global_js.php'); ?>
<script src="../../js/webuploader-0.1.0/webuploader.min.js"></script>
<script>
$(function(){
	var uploader = WebUploader.create({
		// 选完文件后，是否自动上传。
		auto: true,
		// swf文件路径
		swf: '../../js/webuploader-0.1.0/Uploader.swf',
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
			if(data.status == 'y'){
				window.parent.HROS.app.get();
				window.parent.$.dialog.list['editdialog'].close();
			}
		}
	});
	$('input[name="val_type"]').change(function(){
		if($(this).val() == 'pwindow'){
			$('.input-label-isresize, .input-label-isopenmax, .input-label-isflash').slideDown();
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
			$('.input-label-isresize, .input-label-isopenmax, .input-label-isflash').slideUp();
		}
	});
	$('input[name="val_isresize"]').change(function(){
		if($(this).val() == '1'){
			$('.input-label-isopenmax').slideDown();
		}else{
			$('.input-label-isopenmax').slideUp();
		}
	});
	//选择应用图片
	$('.shortcut-selicon a').click(function(){
		$('.shortcut-addicon img').remove();
		$('.shortcut-addicon').addClass('bgnone').append($(this).html());
		$('#val_icon').val($(this).children('img').attr('valsrc')).focusout();
	});
});
</script>
</body>
</html>