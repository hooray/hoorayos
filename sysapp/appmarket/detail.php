<?php
	require('../../global.php');
	
	$app = $db->select(0, 1, 'tb_app', '*', 'and tbid = '.$id);
	$myapplist = array();
	foreach($db->select(0, 0, 'tb_member_app', 'realid', 'and member_id = '.session('member_id')) as $value){
		if($value['realid'] != ''){
			$myapplist[] = $value['realid'];
		}
	}
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
<div class="detail-wrap">
	<div class="app-contents">
		<div class="mbox">
			<div class="title">
				<a href="javascript:window.parent.closeDetailIframe2();" class="btn-back">返回</a>
			</div>
			<div class="app-title">
				<img src="../../<?php echo $app['icon']; ?>" alt="<?php echo $app['name']; ?>">
				<span class="app-name"><?php echo $app['name']; ?></span>
				<span class="app-desc"><i><?php echo $app['usecount']; ?></i> 人在使用</span>
				<?php if(in_array($app['tbid'], $myapplist)){ ?>
					<a href="javascript:;" app_id="<?php echo $app['tbid']; ?>" app_type="<?php echo $app['type']; ?>" class="btn-run">打开应用</a>
				<?php }else{ ?>
					<a href="javascript:;" app_id="<?php echo $app['tbid']; ?>" app_type="<?php echo $app['type']; ?>" class="btn-add">添加应用</a>
				<?php } ?>
				<div class="grade-box">
					<div class="star-num"><?php echo floor($app['starnum']); ?></div>
					<div class="star-box"><i style="width:<?php echo $app['starnum']*20; ?>%"></i>
						<ul>
							<li class="grade-1" num="1"><a href="javascript:;"><em>很不好用</em></a></li>
							<li class="grade-2" num="2"><a href="javascript:;"><em>体验一般般</em></a></li>
							<li class="grade-3" num="3"><a href="javascript:;"><em>比较好用</em></a></li>
							<li class="grade-4" num="4"><a href="javascript:;"><em>很好用</em></a></li>
							<li class="grade-5" num="5"><a href="javascript:;"><em>棒极了，推荐</em></a></li>
						</ul>
					</div>
				</div>
			</div>
			<h4>应用介绍</h4>
			<h5>
				<?php
					if($app['member_id'] == 0){
						$developer = '<b style="color:red">平台提供</b>';
					}else{
						$member = $db->select(0, 1, 'tb_member', 'username', 'and tbid = '.$app['member_id']);
						$developer = $member['username'];
					}
					foreach($apptype as $at){
						if($at['id'] == $app['kindid']){
							$kindname = $at['name'];
						}
					}
				?>
				<em>开发者：</em><?php echo $developer; ?>
				<em style="margin-left:10px">所属分类：</em><?php echo $kindname; ?>
				<em style="margin-left:10px">发布时间：</em><?php echo date('Y-m-d', strtotime($app['dt'])); ?>
			</h5>
			<div class="app-text breakword"><?php echo $app['remark']; ?></div>
		</div>
	</div>
</div>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	//添加应用
	$('.btn-add').click(function(){
		if(window.top.HROS.base.checkLogin()){
			var appid = $(this).attr('app_id');
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
	});
	//打开应用
	$('.btn-run').click(function(){
		if($(this).attr('app_type') == 'app'){
			window.top.HROS.window.create($(this).attr('app_id'));
		}else{
			window.top.HROS.widget.create($(this).attr('app_id'));
		}
	});
	//评分
	$('.grade-box ul li').click(function(){
		var num = $(this).attr('num');
		if(!isNaN(num) && /^[1-5]$/.test(num)){
			if(window.top.HROS.base.checkLogin()){
				$.ajax({
					type : 'POST',
					url : 'detail.ajax.php',
					data : 'ac=updateAppStar&id=<?php echo $id; ?>&starnum=' + num,
					success : function(msg){
						if(msg){
							ZENG.msgbox.show("打分成功！", 4, 2000);
							location.reload();
						}else{
							ZENG.msgbox.show("你已经打过分了！", 1, 2000);
						}
					}
				});
			}else{
				window.top.HROS.base.login();
			}
		}
	});
});
</script>
</body>
</html>