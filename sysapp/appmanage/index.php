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
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>应用管理</title>
	<?php include('sysapp/global_css.php'); ?>
	<link rel="stylesheet" href="../../static/css/sys.css">
	<style>
	body{margin:0 10px}
	.appicon{width:32px;height:32px}
	.appname{margin-left:10px}
	</style>
</head>
<body>
	<div id="toorbar">
		<div class="form-inline">
			<div class="form-group">
				<a class="btn btn-primary" href="javascript:openDetailIframe('detail.php');"><i class="icon-white icon-plus"></i> 添加新应用</a>
			</div>
			<div class="form-group">
				<label class="control-label">分类：</label>
				<select class="form-control" id="s_app_category_id">
					<option value="">全部</option>
					<?php
						$appcategory = $db->select('tb_app_category', '*');
						foreach($appcategory as $ac){
							echo '<option value="'.$ac['tbid'].'">'.$ac['name'].'</option>';
						}
					?>
					<option value="0">未分类</option>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label">类型：</label>
				<select class="form-control" id="s_type">
					<option value="">全部</option>
					<option value="window">窗口</option>
					<option value="widget">挂件</option>
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
				params['app_category_id'] = $('#s_app_category_id').val();
				params['type'] = $('#s_type').val();
				return params;
			},
			columns : [
				{
					title : '图标',
					field : 'icon',
					align : 'center',
					valign : 'middle',
					width : 50
				},
				{
					title : '应用名称',
					field : 'name',
					halign : 'left',
					valign : 'middle',
					sortable : true
				},
				{
					title : '类型',
					field : 'type',
					align : 'center',
					valign : 'middle',
					width : 100
				},
				{
					title : '分类',
					field : 'app_category_id',
					align : 'center',
					valign : 'middle',
					width : 100
				},
				{
					title : '使用人数',
					field : 'usecount',
					align : 'center',
					valign : 'middle',
					width : 100
				},
				{
					title : '操作',
					field : 'do',
					align : 'center',
					valign : 'middle',
					width : 120
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
		$('#s_app_category_id, #s_type').change(function(){
			$('#table').bootstrapTable('refresh');
		});
		//删除
		$('#table').on('click', '.do-del', function(){
			var id = $(this).data().id;
			var name = $(this).data().name;
			swal({
				type : 'warning',
				title : '',
				text : '确定要删除 “' + name + '” 该应用么？',
				showCancelButton : true,
				confirmButtonText : '确认',
				cancelButtonText : '取消'
			}, function(){
				$.ajax({
					type : 'POST',
					url : 'index.ajax.php',
					data : 'ac=del&id=' + id
				}).done(function(){
					$('#table').bootstrapTable('refresh');
				});
			});
		});
	});
	</script>
</body>
</html>