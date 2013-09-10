<?php
	require('../../global.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>应用市场</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<div class="sub-nav tabbable tabs-left">
	<ul class="nav nav-tabs">
		<li class="all active" value="0"><a href="javascript:;">全部</a></li>
		<?php
			if(checkLogin()){
				$mytype = $db->select(0, 1, 'tb_member', 'type', 'and tbid = '.session('member_id'));
				foreach($apptype as $at){
					if(($at['id'] == 1 && $mytype['type'] == 1) || $at['id'] != 1){
						echo '<li value="'.$at['id'].'"><a href="javascript:;">'.$at['name'].'</a></li>';
					}
				}
				echo '<li class="myapps" value="-1"><a href="javascript:;">我的　应用</a></li>';
			}else{
				foreach($apptype as $at){
					if($at['id'] != 1){
						echo '<li value="'.$at['id'].'"><a href="javascript:;">'.$at['name'].'</a></li>';
					}
				}
			}
		?>
	</ul>
	<input type="hidden" name="search_1" id="search_1" value="">
</div>
<div class="wrap">
	<div class="col-sub">
		<div class="search-box">
			<div class="input-append">
				<input type="text" name="keyword" id="keyword" style="width:158px" value="<?php echo $searchkey; ?>"><button id="search_3" class="btn"><i class="icon-search"></i></button>
			</div>
		</div>
		<div class="mbox commend-day">
			<?php
				$recommendApp = $db->select(0, 1, 'tb_app', '*', 'and isrecommend = 1');
			?>
			<h3>今日推荐</h3>
			<div class="commend-container">
				<a href="javascript:openDetailIframe2('detail.php?id=<?php echo $recommendApp['tbid']; ?>');">
					<img src="../../<?php echo $recommendApp['icon']; ?>" alt="<?php echo $recommendApp['name']; ?>">			
				</a>
			</div>
			<div class="commend-text">
				<h4>
					<strong><?php echo $recommendApp['name']; ?></strong>
					<span><?php echo $recommendApp['usecount']; ?>人在用</span>				
				</h4>
				<div class="con" title="<?php echo $recommendApp['remark']; ?>"><?php echo $recommendApp['remark']; ?></div>
				<?php
					$myapplist = array();
					foreach($db->select(0, 0, 'tb_member_app', 'tbid, realid', 'and member_id = '.session('member_id')) as $value){
						if($value['realid'] != ''){
							$myapplist[] = $value['realid'];
						}
					}
					if(in_array($recommendApp['tbid'], $myapplist)){
						echo '<a href="javascript:;" real_app_id="'.$recommendApp['tbid'].'" app_type="'.$recommendApp['type'].'" class="btn-run">打开应用</a>';
					}else{
						echo '<a href="javascript:;" real_app_id="'.$recommendApp['tbid'].'" class="btn-add">添加应用</a>';
					}
				?>
			</div>
			<span class="star-box"><i style="width:<?php echo $recommendApp['starnum']*20; ?>%"></i></span>
		</div>
		<div class="mbox develop">
			<h3>我是开发者</h3>
			<div class="developer">
				<?php if(checkLogin()){ ?>
					<?php
						$userinfo = $db->select(0, 1, 'tb_member', '*', 'and tbid = '.session('member_id'));
						$myappcount = $db->select(0, 2, 'tb_app', '*', 'and member_id = '.session('member_id').' and verifytype = 1');
						$myappunverifycount = $db->select(0, 2, 'tb_app', '*', 'and member_id = '.session('member_id').' and verifytype != 1');
					?>
					<p>开发者：<?php echo $userinfo['username']; ?></p>
					<p>我开发的应用：<font style="font-weight:bold"><?php echo $myappcount; ?></font> 个</p>
					<p>未发布的应用：<font style="font-weight:bold"><?php echo $myappunverifycount; ?></font> 个</p>
					<div class="text-center"><a href="javascript:openDetailIframe2('myapp.manage.php');" class="btn btn-primary">管理我的应用</a> <a href="javascript:openDetailIframe2('myapp.manage.php?add=1');" class="btn btn-danger">开发新应用</a></div>
				<?php }else{ ?>
					<div class="text-center" style="margin-top:40px"><a href="javascript:window.top.HROS.base.login();;" class="btn btn-primary btn-large">您还没登录，点我登录</a></div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-main">
		<div class="mbox app-list-box">
			<div class="title">
				<ul>
					<li class="focus" value="1"><a href="javascript:;">最新应用</a></li>
					<li value="2"><a href="javascript:;">最热门</a></li>
					<li value="3"><a href="javascript:;">最高评价</a></li>
					<input type="hidden" name="search_2" id="search_2" value="1">
				</ul>
			</div>
			<ul class="app-list"></ul>
			<div class="pagination pagination-centered" style="margin-top:6px" id="pagination"></div>
			<input id="pagination_setting" type="hidden" per="5" />
		</div>
	</div>
</div>
<?php if(isset($id)){ ?>
	<div id="detailIframe" style="background:#fff;position:fixed;z-index:1;top:0;left:60px;right:0;height:100%">
		<iframe frameborder="0" src="detail.php?id=<?php echo $id; ?>" style="width:100%;height:100%"></iframe>
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
		$('#search_1').val($(this).attr('value'));
		$('.app-list-box .title li').removeClass('active').eq(0).addClass('active');
		$('#search_2').val(1);
		getPageList(0);
	});
	$('.app-list-box .title li').click(function(){
		$('.app-list-box .title li').removeClass('focus');
		$(this).addClass('focus');
		$('#search_2').val($(this).attr('value'));
		getPageList(0);
	});
	//搜索按钮
	$('#search_3').click(function(){
		$('.app-list-box .title li').removeClass('focus').eq(0).addClass('focus');
		$('.sub-nav ul li').removeClass('active').eq(0).addClass('active');
		$('#search_1').val(0);
		$('#search_2').val(1);
		getPageList(0);
	});
	//添加，删除，打开应用
	$('.app-list').on('click', '.btn-add-s', function(){
		if(window.top.HROS.base.checkLogin()){
			$(this).removeClass().addClass('btn-loading-s');
			window.top.HROS.app.add($(this).attr('real_app_id'), function(){
				$('#pagination').trigger('currentPage');
				window.top.HROS.app.get();
			});
		}else{
			window.top.$.dialog({
				title: '温馨提示',
				icon: 'warning',
				content: '您尚未登录，赶快登录去添加您喜爱的应用吧！',
				ok: function(){
					window.top.HROS.base.login();
				}
			});
		}
	}).on('click', '.btn-remove-s', function(){
		if(window.top.HROS.base.checkLogin()){
			$(this).removeClass().addClass('btn-loading-s');
			var realappid = $(this).attr('real_app_id'), type = $(this).attr('app_type');
			window.top.HROS.app.remove($(this).attr('app_id'), function(){
				$('#pagination').trigger('currentPage');
				window.top.HROS.widget.removeCookie(realappid, type);
				window.top.HROS.app.get();
			});
		}else{
			window.top.HROS.base.login();
		}
	}).on('click', '.btn-run-s', function(){
		if($(this).attr('app_type') == 'app'){
			window.top.HROS.window.create($(this).attr('real_app_id'), $(this).attr('app_type'));
		}else{
			window.top.HROS.widget.create($(this).attr('real_app_id'), $(this).attr('app_type'));
		}
	});
	$('.commend-day').on('click', '.btn-add', function(){
		if(window.top.HROS.base.checkLogin()){
			var appid = $(this).attr('real_app_id');
			window.top.HROS.app.add(appid, function(){
				window.top.HROS.app.get();
				location.reload();
			});
		}else{
			window.top.$.dialog({
				title: '温馨提示',
				icon: 'warning',
				content: '您尚未登录，赶快登录去添加您喜爱的应用吧！',
				ok: function(){
					window.top.HROS.base.login();
				}
			});
		}
	}).on('click', '.btn-run', function(){
		if($(this).attr('app_type') == 'app'){
			window.top.HROS.window.create($(this).attr('real_app_id'));
		}else{
			window.top.HROS.widget.create($(this).attr('real_app_id'));
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
		data : 'ac=getList&from=' + from + '&to=' + to + '&search_1=' + $('#search_1').val() + '&search_2=' + $('#search_2').val() + '&search_3=' + $('#keyword').val(),
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