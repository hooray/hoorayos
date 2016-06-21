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
	else if(!checkPermissions(5)){
		redirect('../error.php?code='.$errorcode['noPermissions']);
	}

	if(isset($_GET['categoryid'])){
		$name = $db->get('tb_app_category', 'name', array(
			'tbid' => $_GET['categoryid']
		));
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>用户管理</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../static/css/sys.css">
</head>

<body>
<form action="detail.ajax.php" method="post" name="form" id="form">
<input type="hidden" name="ac" value="edit">
<input type="hidden" name="id" value="<?php echo $_GET['categoryid']; ?>">
<div class="creatbox">
	<div class="middle">
		<p class="detile-title">
			<strong>编辑类目</strong>
		</p>
		<div class="input-label">
			<label class="label-text">类目名称：</label>
			<div class="label-box form-inline control-group">
				<input type="text" name="val_name" value="<?php echo $name; ?>" datatype="*" nullmsg="请输入类目名称">
				<span class="help-inline"></span>
			</div>
		</div>
	</div>
</div>
<div class="bottom-bar">
	<div class="con">
		<a class="btn btn-primary fr" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
		<a class="btn fr" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="icon-chevron-up"></i> 返回类目列表</a>
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
								window.parent.$('#pagination').trigger('currentPage');
							});
						}
					});
				}
			}
		}
	});
});
</script>
</body>
</html>
