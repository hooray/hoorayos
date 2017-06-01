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

	$set = $db->get('tb_setting', '*');
	$global_title = 'index';
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>网站设置</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<?php include('global_title.php'); ?>
	<form action="index.ajax.php" method="post" id="form" class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-body">
				<input type="hidden" name="ac" value="edit">
				<div class="form-group">
					<label for="inputTitle" class="col-sm-2 control-label">网站标题：</label>
					<div class="col-sm-10">
						<input type="text" name="val_title" class="form-control" id="inputTitle" value="<?php echo $set['title']; ?>" datatype="*" nullmsg="请填写网站标题">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputKeywords" class="col-sm-2 control-label">SEO关键词：</label>
					<div class="col-sm-10">
						<input type="text" name="val_keywords" class="form-control" id="inputKeywords" value="<?php echo $set['keywords']; ?>" datatype="*" nullmsg="请填写SEO关键词" placeholder="请填写SEO关键词，可填写多个，用英文逗号组成，建议不超过100个字符，如：关键词1,关键词2,关键词3">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputDescription" class="col-sm-2 control-label">SEO描述信息：</label>
					<div class="col-sm-10">
						<input type="text" name="val_description" class="form-control" id="inputDescription" value="<?php echo $set['description']; ?>" datatype="*" nullmsg="请填写SEO描述信息" placeholder="请填写SEO描述信息">
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<a class="btn btn-primary btn-block" id="form-submit" href="javascript:;">保存设置</a>
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
						title: '操作成功',
						text: '页面刷新后生效',
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