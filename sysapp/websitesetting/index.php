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
	$global_title = 'index';
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>网站设置</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<?php include('global_title.php'); ?>
<form action="index.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<div class="input-label">
	<div class="label-text">网站标题：</div>
	<div class="label-box form-inline control-group">
		<input type="text" name="val_title" style="width:250px" value="<?php echo $set['title']; ?>" datatype="*" nullmsg="请填写网站标题">
		<span class="help-inline errormsg"></span>
	</div>
</div>
<div class="input-label">
	<div class="label-text">SEO关键词：</div>
	<div class="label-box form-inline control-group">
		<input type="text" name="val_keywords" style="width:250px" value="<?php echo $set['keywords']; ?>" datatype="*" nullmsg="请填写SEO关键字">
		<p class="help-inline infomsg" style="display:none">推荐写法：“关键词1,关键词2,关键词3”，必须为英文逗号，不超过100字符</p>
		<p class="help-inline errormsg"></p>
	</div>
</div>
<div class="input-label">
	<div class="label-text">SEO描述信息：</div>
	<div class="label-box form-inline control-group">
		<input type="text" name="val_description" style="width:250px" value="<?php echo $set['description']; ?>" datatype="*" nullmsg="请填写SEO描述信息">
		<p class="help-inline infomsg" style="display:none">推荐写法：尽量把关键词重复2-3次</p>
		<p class="help-inline errormsg"></p>
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
				ZENG.msgbox.show('设置已保存，页面刷新后生效！', 4, 2000);
			}
		}
	});
});
</script>
</body>
</html>