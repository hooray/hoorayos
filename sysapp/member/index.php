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
	else if(!checkPermissions(3)){
		redirect('../error.php?code='.$errorcode['noPermissions']);
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>用户管理</title>
	<?php include('sysapp/global_css.php'); ?>
	<style>
		body{
			margin: 0 10px;
		}
		.membername{
			margin-left: 10px;
		}
	</style>
</head>
<body>
	<div id="toorbar">
		<div class="form-inline">
			<div class="form-group">
				<a class="btn btn-primary" href="javascript:openDetailIframe('detail.php');"><i class="glyphicon glyphicon-plus"></i> 添加新用户</a>
			</div>
			<div class="form-group">
				<label class="control-label">类型：</label>
				<select class="form-control" id="s_type">
					<option value="">全部</option>
					<option value="0">普通会员</option>
					<option value="1">管理员</option>
				</select>
			</div>
		</div>
	</div>
	<table id="table"></table>
	<?php include('sysapp/global_module_detailIframe.php'); ?>
	<?php include('sysapp/global_js.php'); ?>
	<script>
	$(function(){
		$('#table').bootstrapTable({
			height : $(window).height(),
			url : 'index.ajax.php',
			queryParams : function(params){
				params['ac'] = 'getList';
				params['type'] = $('#s_type').val();
				return params;
			},
			columns : [
				{
					title : '头像',
					field : 'avatar',
					align : 'center',
					valign : 'middle',
					width : 70
				},
				{
					title : '用户名',
					field : 'username',
					halign : 'left',
					valign : 'middle',
					sortable : true
				},
				{
					title : '类型',
					field : 'type',
					align : 'center',
					valign : 'middle',
					width : 150
				},
				{
					title : '操作',
					field : 'do',
					align : 'center',
					valign : 'middle',
					width : 150
				}
			],
			striped : true, // 开启隔行换色
			showRefresh : true, // 显示刷新按钮
			// 工具栏
			toolbar : '#toorbar',
			// 搜索
			search : true,
			// 分页
			pagination : true, // 开启分页
			sidePagination : 'server', // 在服务器进行分页
			paginationPreText : '上一页',
			paginationNextText : '下一页',
		});
		$('#s_type').change(function(){
			$('#table').bootstrapTable('refresh');
		});
		//删除
		$('#table').on('click', '.do-del', function(){
			var id = $(this).data().id;
			var username = $(this).data().username;
			swal({
				type : 'warning',
				title : '删除 “' + username + '” 用户',
				showCancelButton : true,
				confirmButtonText : '确认',
				cancelButtonText : '取消',
				closeOnConfirm : false,
				showLoaderOnConfirm : true
			}, function(){
				$.ajax({
					type : 'POST',
					url : 'index.ajax.php',
					data : 'ac=del&id=' + id
				}).done(function(){
					swal({
						type: 'success',
						title: '操作成功',
						timer: 2000,
						showConfirmButton: false
					});
					$('#table').bootstrapTable('refresh');
				});
			});
		});
	});
	</script>
</body>
</html>