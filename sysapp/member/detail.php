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
	
	if(isset($memberid)){
		$member = $db->select(0, 1, 'tb_member', '*', 'and tbid = '.$memberid);
	}
	$permission = $db->select(0, 0, 'tb_permission', 'tbid,name');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>用户管理</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<form action="detail.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<input type="hidden" name="id" value="<?php echo $memberid; ?>">
<div class="creatbox">
	<div class="middle">
		<p class="detile-title">
			<strong>编辑用户</strong>
		</p>
		<div class="input-label">
			<label class="label-text">用户名：</label>
			<div class="label-box form-inline control-group">
				<?php
					if(isset($memberid)){
						echo $member['username'];
					}else{
				?>
				<input type="text" name="val_username" datatype="*" nullmsg="请输入用户名">
				<?php } ?>
				<span class="help-inline"></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">用户密码：</label>
			<div class="label-box form-inline control-group">
				<input type="password" name="val_password" <?php if(!isset($memberid)){ ?>datatype="*" nullmsg="请输入用户密码"<?php } ?>>
				<span class="help-inline"><?php if(isset($memberid)){ echo '（如果无需修改则不填）'; } ?></span>
			</div>
		</div>
		<div class="input-label">
			<label class="label-text">用户类型：</label>
			<div class="label-box form-inline">
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_type" value="0" <?php if($member['type'] == 0 || !isset($memberid)){echo 'checked';} ?>>普通会员</label>
				<label class="radio" style="margin-right:10px"><input type="radio" name="val_type" value="1" <?php if($member['type'] == 1){echo 'checked';} ?>>管理员</label>
			</div>
		</div>
		<div class="input-label input-label-permission <?php if($member['type'] == 0){echo 'hide';} ?>">
			<label class="label-text">用户权限：</label>
			<div class="label-box form-inline">
				<?php
					foreach($permission as $v){
						echo '<label class="checkbox" style="margin-right:10px"><input type="checkbox" name="val_permission_id" value="'.$v['tbid'].'" ';
						if($member['permission_id'] == $v['tbid']){
							echo 'checked';
						}
						echo '>'.$v['name'].'</label>';
					}
				?>
				<span class="help-inline">[<a href="javascript:;" rel="tooltip" title="权限最多只能选一项">?</a>]</span>
			</div>
		</div>
	</div>
</div>
<div class="bottom-bar">
	<div class="con">
		<a class="btn btn-primary fr" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
		<a class="btn fr" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="icon-chevron-up"></i> 返回用户列表</a>
	</div>
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
		if($(this).val() == 1){
			$('.input-label-permission').slideDown();
		}else{
			$('.input-label-permission').slideUp();
		}
	});
	checkboxMax();
	$('input[name="val_permission_id"]').change(function(){
		checkboxMax();
	});
});
function checkboxMax(){
	if($('input[name="val_permission_id"]').filter(':checked').length >= 1){
		$('input[name="val_permission_id"]').not(':checked').each(function(){
			$(this).prop('disabled', true);
		});
	}else{
		$('input[name="val_permission_id"]').not(':checked').each(function(){
			$(this).prop('disabled', false);
		});
	}
}
</script>
</body>
</html>