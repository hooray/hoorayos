<?php
	require('../../global.php');
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>应用市场</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<div class="sub-nav">
		<div class="category-box">
			<a href="javascript:;" class="active" title="全部" data-val="0">全部</a>
			<?php
				if(checkLogin()){
					$type = $db->get('tb_member', 'type', array(
						'tbid' => session('member_id')
					));
				}
				foreach($db->select('tb_app_category', '*') as $ac){
					if(($ac['issystem'] == 1 && $type == 1) || $ac['issystem'] == 0){
						echo '<a href="javascript:;" title="'.$ac['name'].'" data-val="'.$ac['tbid'].'">'.$ac['name'].'</a>';
					}
				}
				echo '<a href="javascript:;" title="挂件" data-val="-1">挂件</a>';
			?>
		</div>
		<?php if(checkLogin()){ ?>
			<div class="myapp-box">
				<a href="javascript:;" title="我的应用" data-val="-2">我的应用</a>
			</div>
		<?php } ?>
		<input type="hidden" id="search_1" value="">
	</div>
	<div class="main-wrap">
		<div class="app-content">
			<div class="row">
				<div class="col-xs-12">
					<div class="input-group">
						<input type="text" class="form-control" id="search_2" placeholder="请输入搜索关键字" value="<?php echo $_GET['searchkey']; ?>">
						<span class="input-group-btn">
							<button id="search_2_do" class="btn btn-primary">
								<i class="fa fa-search"></i> 搜索
							</button>
						</span>
					</div>
				</div>
			</div>
			<div class="app-list"></div>
			<div id="pagination" class="pagination-box"></div>
			<input id="pagination_setting" type="hidden" per="10" />
		</div>
	</div>
	<?php include('sysapp/global_js.php'); ?>
	<script>
	$(function(){
		//加载列表
		getPageList(0);
		$('.sub-nav .category-box a, .sub-nav .myapp-box a').click(function(){
			$('.sub-nav .category-box a, .sub-nav .myapp-box a').removeClass('active');
			$(this).addClass('active');
			$('#search_1').val($(this).data().val);
			getPageList(0);
		});
		//搜索
		$('#search_2').on('keydown', function(e){
			if(e.keyCode == '13'){
				$('#search_2_do').click();
			}
		});
		$('#search_2_do').click(function(){
			$('.sub-nav .category-box a').removeClass('active').eq(0).addClass('active');
			$('#search_1').val(0);
			getPageList(0);
		});
		//添加，删除，打开应用
		$('.app-list').on('click', '.btn-add', function(){
			if(window.parent.HROS.base.checkLogin()){
				window.parent.HROS.app.add($(this).attr('real_app_id'), function(){
					$('#pagination').trigger('currentPage');
					window.parent.HROS.app.get();
				});
			}else{
				window.parent.HROS.base.loginDialog('您尚未登录，赶快登录去添加您喜爱的应用吧！');
			}
		}).on('click', '.btn-remove', function(){
			if(window.parent.HROS.base.checkLogin()){
				var realappid = $(this).attr('real_app_id'), type = $(this).attr('app_type');
				window.parent.HROS.app.remove($(this).attr('app_id'), function(){
					$('#pagination').trigger('currentPage');
					window.parent.HROS.widget.removeCookie(realappid, type);
					window.parent.HROS.app.get();
				});
			}else{
				window.parent.HROS.base.login();
			}
		}).on('click', '.btn-run', function(){
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
			data : 'ac=getList&from=' + from + '&to=' + to + '&search_1=' + $('#search_1').val() + '&search_2=' + $('#search_2').val(),
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