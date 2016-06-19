<?php
	require('../../global.php');

	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	$member = $db->get('tb_member', '*', array(
		'tbid' => session('member_id')
	));
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
	<form action="ajax.php" method="post" id="form" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-heading">修改登录密码</div>
			<div class="panel-body">
				<input type="hidden" name="ac" value="editPassword">
				<div class="form-group">
					<label for="inputOldpassword" class="col-sm-2 control-label">原密码：</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="inputOldpassword" datatype="*6-18" ajaxurl="ajax.php?ac=checkPassword" nullmsg="请输入原密码" errormsg="密码长度为6-18个字符">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="col-sm-2 control-label">新密码：</label>
					<div class="col-sm-10">
						<input type="password" name="password" class="form-control" id="inputPassword" datatype="*6-18" nullmsg="请输入原密码" errormsg="密码长度为6-18个字符">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword2" class="col-sm-2 control-label">确认新密码：</label>
					<div class="col-sm-10">
						<input type="password" name="password" class="form-control" id="inputPassword2" datatype="*" recheck="password" nullmsg="请确认新密码" errormsg="您两次输入的密码不一致">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<a class="btn btn-primary btn-block" id="form-submit" href="javascript:;">确认修改</a>
					</div>
				</div>
			</div>
		</div>
	</form>
	<form action="ajax.php" method="post" id="form2" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-heading">修改锁屏密码</div>
			<div class="panel-body">
				<input type="hidden" name="ac" value="editLockPassword">
				<div class="form-group">
					<label for="inputLockpassword" class="col-sm-2 control-label">锁屏密码：</label>
					<div class="col-sm-10">
						<input type="password" name="lockpassword" class="form-control" id="inputLockpassword" datatype="*" nullmsg="请输入新的锁屏密码">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<a class="btn btn-primary btn-block" id="form-submit2" href="javascript:;">确认修改</a>
					</div>
				</div>
			</div>
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
					swal({
						type: 'success',
						title: '修改成功',
						timer: 2000,
						showConfirmButton: false
					});
				}else{
					swal({
						type: 'error',
						title: '修改失败',
						timer: 2000,
						showConfirmButton: false
					});
				}
			}
		});
		$('#form2').Validform({
			btnSubmit: '#form-submit2',
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
					swal({
						type: 'success',
						title: '修改成功',
						timer: 2000,
						showConfirmButton: false
					});
				}else{
					swal({
						type: 'error',
						title: '修改失败',
						timer: 2000,
						showConfirmButton: false
					});
				}
			}
		});
	});
	</script>
</body>
</html>
