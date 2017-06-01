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

	if(isset($_GET['memberid'])){
		$member = $db->get('tb_member', '*', array(
			'tbid' => $_GET['memberid']
		));
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>用户管理</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<form action="detail.ajax.php" method="post" name="form" id="form" class="form-horizontal">
		<div class="title-bar">编辑用户</div>
		<div class="creatbox">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="ac" value="edit">
					<input type="hidden" name="id" value="<?php echo $_GET['memberid']; ?>">
					<div class="form-group">
						<label for="inputUsername" class="col-sm-2 control-label">用户名：</label>
						<div class="col-sm-10">
							<?php if(isset($_GET['memberid'])){ ?>
							<p class="form-control-static"><?php echo $member['username']; ?></p>
							<?php }else{ ?>
							<input type="text" name="val_username" class="form-control" id="inputUsername" datatype="s6-18" ajaxurl="detail.ajax.php?ac=checkUsername" nullmsg="请输入用户名" errormsg="用户名长度为6-18个字符">
							<?php } ?>
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword" class="col-sm-2 control-label">用户密码：</label>
						<div class="col-sm-10">
							<input type="password" name="val_password" class="form-control" id="inputPassword" <?php if(!isset($_GET['memberid'])){ ?>datatype="*" nullmsg="请输入用户密码"<?php } ?>>
							<span class="help-block"><?php if(isset($_GET['memberid'])){ echo '（如果无需修改则不填）'; } ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">用户类型：</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="val_type" value="0" <?php if($member['type'] == 0 || !isset($_GET['memberid'])){echo 'checked';} ?>>普通会员
							</label>
							<label class="radio-inline">
								<input type="radio" name="val_type" value="1" <?php if($member['type'] == 1){echo 'checked';} ?>>管理员
							</label>
						</div>
					</div>
					<div class="form-group form-group-permission <?php if($member['type'] == 0){echo 'hide';} ?>">
						<label class="col-sm-2 control-label">用户权限：</label>
						<div class="col-sm-10">
							<select class="form-control" name="val_permission_id" data-plugin="bootstrapSelect" title="请选择权限">
								<?php
								foreach($db->select('tb_permission', array('tbid', 'name')) as $v){
									if($member['permission_id'] == $v['tbid']){
										echo '<option value="'.$v['tbid'].'" selected>'.$v['name'].'</option>';
									}else{
										echo '<option value="'.$v['tbid'].'">'.$v['name'].'</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom-bar">
			<a class="btn btn-primary pull-right" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
			<a class="btn btn-default pull-right" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="icon-chevron-up"></i> 返回用户列表</a>
		</div>
	</form>
	<?php include('sysapp/global_js.php'); ?>
	<script>
	$(function(){
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
				if($('input[name="id"]').val() != ''){
					if(data.status == 'y'){
						window.parent.closeDetailIframe(function(){
							window.parent.$('#pagination').trigger('currentPage');
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
			if($(this).val() == 1){
				$('.form-group-permission').removeClass('hide');
			}else{
				$('.form-group-permission').addClass('hide');
			}
		});
	});
	</script>
</body>
</html>