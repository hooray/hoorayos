<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>我的应用</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
<style>
body{margin:10px 10px 0}
.appicon{width:32px;height:32px}
.appname{margin-left:10px}
</style>
</head>

<body>
<div class="well well-small" style="margin-bottom:10px;padding-bottom:0">
	<div class="form-inline">
		<div class="control-group">
			<label>名称：</label>
			<input type="text" name="search_1" id="search_1" class="span3">
			<a class="btn" menu="search" href="javascript:;" style="margin-left:10px"><i class="icon-search"></i> 搜索</a>
			<a class="btn btn-primary fr" href="javascript:openDetailIframe('myapp.edit.php');"><i class="icon-white icon-plus"></i> 添加新应用</a>
			<a class="btn fr" href="javascript:window.parent.closeDetailIframe2();" style="margin-right:10px"><i class="icon-arrow-left"></i> 返回应用市场</a>
		</div>
		<div class="control-group">
			<label>分类：</label>
			<select name="search_2" id="search_2" style="width:140px">
				<option value="">全部</option>
				<?php
					foreach($apptype as $at){
						echo '<option value="'.$at['id'].'">'.$at['name'].'</option>';
					}
				?>
			</select>
			<label style="margin-left:10px">类型：</label>
			<select name="search_3" id="search_3" style="width:140px">
				<option value="">全部</option>
				<option value="app">窗口</option>
				<option value="widget">挂件</option>
			</select>
		</div>
		<div class="control-group">
			<label>状态：</label>
			<select name="search_4" id="search_4" style="width:140px">
				<option value="1">我的上线应用</option>
				<option value="0">等待上线应用</option>
				<option value="2">审核中的应用</option>
				<option value="3">审核失败应用</option>
			</select>
		</div>
	</div>
</div>
<table class="list-table">
	<thead>
		<tr class="col-name">
			<th>应用名称</th>
			<th style="width:80px">类型</th>
			<th style="width:80px">分类</th>
			<th style="width:80px">使用人数</th>
			<th style="width:80px">操作</th>
		</tr>
		<tr class="sep-row"><td colspan="100"></td></tr>
		<tr class="toolbar">
			<td colspan="100">
				<b style="margin:0 10px">符合条件的记录</b>有<font class="list-count">0</font>条
			</td>
		</tr>
		<tr class="sep-row"><td colspan="100"></td></tr>
	</thead>
	<tbody class="list-con"></tbody>
	<tfoot><tr><td colspan="100">
		<div class="pagination pagination-centered">
			<div id="pagination"></div>
		</div>
		<input id="pagination_setting" type="hidden" per="10">
	</td></tr></tfoot>
</table>
<?php if(isset($add)){ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:0;left:0;width:100%;height:100%">
		<iframe frameborder="0" src="myapp.edit.php" style="width:100%;height:100%"></iframe>
	</div>
<?php }else{ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:-100px;left:0;width:100%;height:100%;display:none">
		<iframe frameborder="0" style="width:100%;height:100%"></iframe>
	</div>
<?php } ?>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	//加载列表
	getPageList(0);
	//删除，推荐
	$('.list-con').on('click', '.do-del', function(){
		var appid = $(this).attr('appid');
		var appname = $(this).parents('tr').children('td:first-child').text();
		$.dialog({
			id : 'del',
			content : '确定要删除 “' + appname + '” 该应用么？',
			ok : function(){
				$.ajax({
					type : 'POST',
					url : 'index.ajax.php',
					data : 'ac=del&appid=' + appid
				}).done(function(){
					$('#pagination').trigger('currentPage');
				});
			},
			cancel: true
		});
	}).on('click', '.do-recommend', function(){
		var appid = $(this).attr('appid');
		$.ajax({
			type : 'POST',
			url : 'index.ajax.php',
			data : 'ac=recommend&appid=' + appid
		}).done(function(){
			$('#pagination').trigger('currentPage');
		});
	});
	//搜索
	$('a[menu=search]').click(function(){
		getPageList(0);
	});
});
function initPagination(current_page){
	$('#pagination').pagination(parseInt($('#pagination_setting').attr('count')), {
		current_page : current_page,
		items_per_page : parseInt($('#pagination_setting').attr('per')),
		num_display_entries : 9,
		num_edge_entries : 2,
		callback : getPageList,
		prev_text : '上一页',
		next_text : '下一页'
	});
}
function getPageList(current_page){
	ZENG.msgbox.show('正在加载中，请稍后...', 6, 100000);
	var from = current_page * parseInt($('#pagination_setting').attr('per')), to = parseInt($('#pagination_setting').attr('per'));
	$.ajax({
		type : 'POST', 
		url : 'myapp.ajax.php', 
		data : 'ac=getList&from=' + from + '&to=' + to + '&search_1=' + $('#search_1').val() + '&search_2=' + $('#search_2').val() + '&search_3=' + $('#search_3').val() + '&search_4=' + $('#search_4').val(),
		success : function(msg){
			var arr = msg.split('<{|*|}>');
			$('#pagination_setting').attr('count', arr[0]);
			$('.list-count').text(arr[0]);
			$('.list-con').html(arr[1]);
			initPagination(current_page);
			ZENG.msgbox._hide();
		}
	}); 
}
</script>
</body>
</html>