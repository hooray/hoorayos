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
			'isflash' => 0,
			'verifytype' => 0
		);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>应用管理</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<form action="detail.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<input type="hidden" name="id" value="<?php echo $_GET['appid']; ?>">
<div class="creatbox">
	<div class="middle">
		<p class="detile-title">编辑应用</p>
		<div class="input-label">
			<label class="label-text">应用图片：</label>
			<div class="label-box form-inline control-group">
				<div class="shortcutbox">
					<?php if($app['icon'] != ''){ ?>
						<div class="shortcut-addicon bgnone"><input type="file" id="uploadfilebtn" style="position:absolute;right:0;bottom:0;opacity:0;filter:alpha(opacity=0);display:block;width:200px;height:100px"><img src="../../<?php echo $app['icon']; ?>"></div>
					<?php }else{ ?>
						<div class="shortcut-addicon"><input type="file" id="uploadfilebtn" style="position:absolute;right:0;bottom:0;opacity:0;filter:alpha(opacity=0);display:block;width:200px;height:100px"><img src=""></div>
					<?php } ?>
					<div class="shortcut-selicon">
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
			<label class="label-text">应用分类：</label>
			<div class="label-box form-inline control-group">
				<select name="val_app_category_id" datatype="*" nullmsg="请选择应用分类">
					<option value="">请选择应用分类</option>
					<?php
						$appcategory = $db->select('tb_app_category', '*', array(
							'ORDER' => 'tbid ASC'
						));
						foreach($appcategory as $ac){
							if($ac['tbid'] == $app['app_category_id']){
								echo '<option value="'.$ac['tbid'].'" selected>'.$ac['name'].'</option>';
							}else{
								echo '<option value="'.$ac['tbid'].'">'.$ac['name'].'</option>';
							}
						}
					?>
				</select>
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
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_type" value="window" <?php if($app['type'] == 'window'){echo 'checked';} ?>>窗口</label>
				<label class="radio"><input type="radio" name="val_type" value="widget" <?php if($app['type'] == 'widget'){echo 'checked';} ?>>挂件</label>
			</div>
		</div>
		<div class="input-label input-label-isresize" <?php if($app['type'] == 'widget'){echo 'style="display:none"';} ?>>
			<label class="label-text">窗口是否拉伸：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isresize" value="1" <?php if($app['isresize'] == 1){echo 'checked';} ?>>是</label>
				<label class="radio"><input type="radio" name="val_isresize" value="0" <?php if($app['isresize'] == 0){echo 'checked';} ?>>否</label>
			</div>
		</div>
		<div class="input-label input-label-isopenmax" <?php if($app['type'] == 'widget' || $app['isresize'] == 0){echo 'style="display:none"';} ?>>
			<label class="label-text">打开默认最大化：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isopenmax" value="1" <?php if($app['isopenmax'] == 1){echo 'checked';} ?>>是</label>
				<label class="radio"><input type="radio" name="val_isopenmax" value="0" <?php if($app['isopenmax'] == 0){echo 'checked';} ?>>否</label>
			</div>
		</div>
		<div class="input-label input-label-isflash" <?php if($app['type'] == 'widget'){echo 'style="display:none"';} ?>>
			<label class="label-text">是否为Flash：</label>
			<div class="label-box form-inline control-group">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isflash" value="1" <?php if($app['isflash'] == 1){echo 'checked';} ?>>是</label>
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_isflash" value="0" <?php if($app['isflash'] == 0){echo 'checked';} ?>>否</label>
				<span class="txt">[<a href="javascript:;" rel="tooltip" title="如何设置为Flash应用，当窗口非当前窗口时，会显示遮罩层">?</a>]</span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">应用介绍：</label>
			<div class="label-box form-inline control-group">
				<textarea class="textarea" name="val_remark" id="val_remark" style="width:300px;height:100px"><?php echo $app['remark']; ?></textarea>
			</div>
		</div>
	</div>
</div>
<div class="bottom-bar">
	<div class="con">
		<?php if($app['verifytype'] == 2){ ?>
		<a class="btn btn-success fl" id="btn-pass" href="javascript:;" appid="<?php echo $appid; ?>"><i class="icon-white icon-ok"></i> 审核通过</a>
		<a class="btn fl" id="btn-unpass" href="javascript:;" appid="<?php echo $appid; ?>" style="margin-left:10px"><i class="icon-remove"></i> 审核不通过</a>
		<a class="btn" id="btn-preview" href="javascript:;" style="margin-left:10px"><i class="icon-eye-open"></i> 预览应用</a>
		<?php }else{ ?>
		<a class="btn" id="btn-preview" href="javascript:;"><i class="icon-eye-open"></i> 预览应用</a>
		<?php } ?>
		<a class="btn btn-primary fr" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
		<a class="btn fr" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="icon-chevron-up"></i> 返回应用列表</a>
	</div>
</div>
</form>
<div id="unpassinfo" class="form-inline" style="display:none;width:300px">
	<div>拒绝审核通过理由：</div>
	<label class="radio" style="margin-right:10px"><input type="radio" name="unpassinfo" value="信息不完整" checked>信息不完整</label>
	<label class="radio" style="margin-right:10px"><input type="radio" name="unpassinfo" value="应用已存在">应用已存在</label>
	<label class="radio" style="margin-right:10px"><input type="radio" name="unpassinfo" value="内容低俗">内容低俗</label>
</div>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	var form = $('#form').Validform({
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
	$('input[name="val_type"]').change(function(){
		if($(this).val() == 'window'){
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
	$('#uploadfilebtn').on('change', function(e){
		var files = e.target.files || e.dataTransfer.files;
		if(files.length == 0){
			return;
		}
		//检测文件是不是图片
		if(files[0].type.indexOf('image') === -1){
			alert('请上传图片');
			return false;
		}
		//检测文件大小是否超过1M
		if(files[0].size > 1024*1024){
			alert('图片大小超过1M');
			return;
		}
		var fd = new FormData();
		fd.append('xfile', files[0]);
		var xhr = new XMLHttpRequest();
		if(xhr.upload){
			$.dialog({
				id: 'uploadImg',
				title: '正在上传',
				content: '<div id="imgProgress" class="progress progress-striped active" style="width:200px;margin-bottom:0"><div class="bar"></div></div>',
				cancel: false
			});
			xhr.upload.addEventListener('progress', function(e){
				if(e.lengthComputable){
					var loaded = Math.ceil(e.loaded / e.total * 100);
					$('#imgProgress .bar').css({
						width: loaded + '%'
					});
				}
			}, false);
			xhr.addEventListener('load', function(e){
				$('#uploadfilebtn').val('');
				$.dialog.list['uploadImg'].close();
				if(xhr.readyState == 4 && xhr.status == 200){
					var result = jQuery.parseJSON(e.target.responseText);
					if(result.state == 'SUCCESS'){
						$('.shortcut-addicon img').remove();
						$('.shortcut-addicon').addClass('bgnone').append('<img src="../../' + result.url + '" />');
						$('#val_icon').val(result.url).focusout();
					}else{
						ZENG.msgbox.show(result.state, 5, 2000);
					}
				}
			}, false);
			xhr.open('post', 'detail.php?ac=uploadImg', true);
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.send(fd);
		}
	});
	$('#btn-pass').on('click', function(){
		var appid = $(this).attr('appid');
		$.dialog({
			id : 'del',
			content : '确认审核通过该应用？',
			ok : function(){
				$.ajax({
					type : 'POST',
					url : 'detail.ajax.php',
					data : 'ac=pass&appid=' + appid
				}).done(function(){
					window.parent.closeDetailIframe(function(){
						window.parent.$('#pagination').trigger('currentPage');
					});
				});
			},
			cancel: true
		});
	});
	$('#btn-unpass').on('click', function(){
		var appid = $(this).attr('appid');
		$.dialog({
			id : 'del',
			content : document.getElementById('unpassinfo'),
			ok : function(){
				$.ajax({
					type : 'POST',
					url : 'detail.ajax.php',
					data : 'ac=unpass&appid=' + appid + '&info=' + $('#unpassinfo input[name="unpassinfo"]').val()
				}).done(function(){
					window.parent.closeDetailIframe(function(){
						window.parent.$('#pagination').trigger('currentPage');
					});
				});
			},
			cancel: true
		});
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
			$.dialog({
				icon : 'error',
				content : '应用无法预览，请讲内容填写完整后再尝试预览'
			});
		}
	});
});
</script>
</body>
</html>