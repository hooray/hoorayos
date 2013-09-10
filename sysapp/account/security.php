<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	$member = $db->select(0, 1, 'tb_member', '*', 'and tbid = '.session('member_id'));
	$global_title = 'security';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>账号安全</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<?php include('global_title.php'); ?>
<form action="ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="editPassword">
<div class="input-label">
	<label class="label-text">原密码：</label>
	<div class="label-box form-inline control-group">
		<input type="password" datatype="*6-18" ajaxurl="ajax.php?ac=checkPassword" nullmsg="请输入原密码" errormsg="密码长度为6-18个字符">
		<p class="help-inline infomsg" style="display:none">请输入原密码，密码长度为6-18个字符</p>
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<label class="label-text">新密码：</label>
	<div class="label-box form-inline control-group">
		<input type="password" name="password" id="password" datatype="*6-18" nullmsg="请输入新密码" errormsg="密码长度为6-18个字符">
		<p class="help-inline infomsg" style="display:none">请输入新密码，密码长度为6-18个字符</p>
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label">
	<label class="label-text">确认新密码：</label>
	<div class="label-box form-inline control-group">
		<input type="password" datatype="*" recheck="password" nullmsg="请确认新密码" errormsg="您两次输入的账号密码不一致">
		<p class="help-inline infomsg" style="display:none">请确认新密码</p>
		<span class="help-inline"></span>
	</div>
</div>
<div class="input-label" style="background:none;padding-left:0;text-align:center">
	<a class="btn" id="form-submit" href="javascript:;">保存</a>
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
				window.parent.ZENG.msgbox.show('密码修改成功', 4, 2000);
			}else{
				window.parent.ZENG.msgbox.show('密码修改失败', 5, 2000);
			}
		}
	});
});
</script>
</body>
</html>