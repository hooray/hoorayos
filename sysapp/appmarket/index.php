<?php
	require('../../global.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>应用市场</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../static/css/sys.css">
</head>

<body>
<div class="sub-nav tabbable tabs-left">
	<ul class="nav nav-tabs">
		<li class="all active" value="0"><a href="javascript:;" title="全部">全部</a></li>
		<?php
			if(checkLogin()){
				$type = $db->get('tb_member', 'type', array(
					'tbid' => session('member_id')
				));
			}
			foreach($db->select('tb_app_category', '*') as $ac){
				if(($ac['issystem'] == 1 && $type == 1) || $ac['issystem'] == 0){
					echo '<li val="'.$ac['tbid'].'"><a href="javascript:;" title="'.$ac['name'].'">'.$ac['name'].'</a></li>';
				}
			}
			echo '<li val="-1"><a href="javascript:;" title="挂件">挂件</a></li>';
			if(checkLogin()){
				echo '<li class="myapps" val="-2"><a href="javascript:;" title="我的应用">我的<br>应用</a></li>';
			}
		?>
	</ul>
	<input type="hidden" name="search_1" id="search_1" value="">
</div>
<div class="wrap">
	<div class="col-sub">
		<div class="search-box">
			<div class="input-append">
				<input type="text" name="search_3" id="search_3" placeholder="请输入搜索关键字" style="width:150px" value="<?php echo $_GET['searchkey']; ?>">
				<button id="search_3_remove" class="btn" style="padding:4px 5px"><i class="icon-remove"></i></button>
				<button id="search_3_do" class="btn" style="padding:4px 5px"><i class="icon-search"></i></button>
			</div>
		</div>
	</div>
	<div class="col-main">
		<div class="mbox app-list-box">
			<div class="title">
				<ul>
					<li class="focus" val="1"><a href="javascript:;">最新应用</a></li>
					<li val="2"><a href="javascript:;">最热门</a></li>
					<li val="3"><a href="javascript:;">最高评价</a></li>
					<input type="hidden" name="search_2" id="search_2" value="1">
				</ul>
			</div>
			<ul class="app-list"></ul>
			<div class="pagination pagination-centered" style="margin-top:6px" id="pagination"></div>
			<input id="pagination_setting" type="hidden" per="5" />
		</div>
	</div>
</div>
<?php if(isset($_GET['id'])){ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:0;left:60px;right:0;height:100%">
		<iframe frameborder="0" src="detail.php?id=<?php echo $_GET['id']; ?>" style="width:100%;height:100%"></iframe>
	</div>
<?php }else{ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:0;left:140px;right:0;height:100%;display:none">
		<iframe frameborder="0" style="width:100%;height:100%"></iframe>
	</div>
<?php } ?>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	//加载列表
	getPageList(0);
	//detailIframe
	openDetailIframe2 = function(url){
		ZENG.msgbox.show('正在载入中，请稍后...', 6, 100000);
		$('#detailIframe iframe').attr('src', url).load(function(){
			ZENG.msgbox._hide();
			$('#detailIframe').animate({
				'left' : '60px',
				'opacity' : 'show'
			}, 500);
		});
	};
	closeDetailIframe2 = function(callback){
		$('#detailIframe').animate({
			'left' : 0,
			'opacity' : 'hide'
		}, 500, function(){
			$('#detailIframe').css('left', '140px');
			callback && callback();
		});
	};
	$('.sub-nav ul li').click(function(){
		closeDetailIframe2();
		$('.sub-nav ul li').removeClass('active');
		$(this).addClass('active');
		$('#search_1').val($(this).attr('val'));
		$('.app-list-box .title li').removeClass('active').eq(0).addClass('active');
		$('#search_2').val(1);
		getPageList(0);
	});
	$('.app-list-box .title li').click(function(){
		$('.app-list-box .title li').removeClass('focus');
		$(this).addClass('focus');
		$('#search_2').val($(this).attr('val'));
		getPageList(0);
	});
	//搜索
	$('#search_3').on('keydown', function(e){
		if(e.keyCode == '13'){
			$('#search_3_do').click();
		}
	});
	$('#search_3_do').click(function(){
		$('.app-list-box .title li').removeClass('focus').eq(0).addClass('focus');
		$('.sub-nav ul li').removeClass('active').eq(0).addClass('active');
		$('#search_1').val(0);
		$('#search_2').val(1);
		getPageList(0);
	});
	$('#search_3_remove').click(function(){
		$('#search_3').val('');
		getPageList(0);
	});
	//添加，删除，打开应用
	$('.app-list').on('click', '.btn-add-s', function(){
		if(window.parent.HROS.base.checkLogin()){
			$(this).removeClass().addClass('btn-loading-s');
			window.parent.HROS.app.add($(this).attr('real_app_id'), function(){
				$('#pagination').trigger('currentPage');
				window.parent.HROS.app.get();
			});
		}else{
			window.parent.HROS.base.loginDialog('您尚未登录，赶快登录去添加您喜爱的应用吧！');
		}
	}).on('click', '.btn-remove-s', function(){
		if(window.parent.HROS.base.checkLogin()){
			$(this).removeClass().addClass('btn-loading-s');
			var realappid = $(this).attr('real_app_id'), type = $(this).attr('app_type');
			window.parent.HROS.app.remove($(this).attr('app_id'), function(){
				$('#pagination').trigger('currentPage');
				window.parent.HROS.widget.removeCookie(realappid, type);
				window.parent.HROS.app.get();
			});
		}else{
			window.parent.HROS.base.login();
		}
	}).on('click', '.btn-run-s', function(){
		if($(this).attr('app_type') == 'window'){
			window.parent.HROS.window.create($(this).attr('app_id'), 'window', $(this).attr('real_app_id'));
		}else{
			window.parent.HROS.widget.create($(this).attr('app_id'), 'widget', $(this).attr('real_app_id'));
		}
	});
});
function initPagination(current_page){
	$('#pagination').pagination(parseInt($('#pagination_setting').attr('count')), {
		current_page : current_page,
		items_per_page : parseInt($('#pagination_setting').attr('per')),
		num_display_entries : 5,
		num_edge_entries : 1,
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
		url : 'index.ajax.php',
		data : 'ac=getList&from=' + from + '&to=' + to + '&search_1=' + $('#search_1').val() + '&search_2=' + $('#search_2').val() + '&search_3=' + $('#search_3').val(),
		success : function(msg){
			var arr = msg.split('<{|*|}>');
			$('#pagination_setting').attr('count', arr[0]);
			$('.app-list').html(arr[1]);
			initPagination(current_page);
			ZENG.msgbox._hide();
		}
	});
}
</script>
</body>
</html>