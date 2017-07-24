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
	else if(!checkPermissions(4)){
		redirect('../error.php?code='.$errorcode['noPermissions']);
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>权限管理</title>
	<?php include('sysapp/global_css.php'); ?>
	<style>
		body{
			margin: 0 10px;
		}
	</style>
</head>
<body>
	<div id="toorbar">
		<a class="btn btn-primary" href="javascript:openDetailIframe('detail.php');"><i class="glyphicon glyphicon-plus"></i> 创建新权限</a>
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
				return params;
			},
			columns : [
				{
					title : '权限名称',
					field : 'name',
					halign : 'left',
					valign : 'middle',
					sortable : true,
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
		//删除
		$('#table').on('click', '.do-del', function(){
			var id = $(this).data().id;
			var name = $(this).data().name;
			swal({
				type : 'warning',
				title : '删除 “' + name + '” 权限',
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