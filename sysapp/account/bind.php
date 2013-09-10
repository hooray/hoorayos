<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	$member = $db->select(0, 1, 'tb_member', '*', 'and tbid = '.session('member_id'));
	$global_title = 'bind';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>社区绑定</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<?php include('global_title.php'); ?>
<?php if(SINAWEIBO_AKEY && SINAWEIBO_SKEY){ ?>
<div class="input-label">
	<label class="label-text">新浪微博：</label>
	<div class="label-box form-inline">
		<?php if($member['openid_sinaweibo'] != ''){ ?>
			<a href="<?php echo $member['openurl_sinaweibo']; ?>" target="_blank">
				<img src="<?php echo $member['openavatar_sinaweibo']; ?>" class="img-circle img-polaroid" width="20" height="20">
				<?php echo $member['openname_sinaweibo']; ?>
				<i class="icon-share"></i>
			</a>
			<a href="javascript:;" class="btn btn-link pull-right unbind" data-type="sinaweibo">解除绑定</a>
		<?php }else{ ?>
			<a href="javascript:;" class="btn btn-link pull-right bind" data-type="sinaweibo">绑定</a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php if(TWEIBO_AKEY && TWEIBO_SKEY){ ?>
<div class="input-label">
	<label class="label-text">腾讯微博：</label>
	<div class="label-box form-inline">
		<?php if($member['openid_tweibo'] != ''){ ?>
			<a href="<?php echo $member['openurl_tweibo']; ?>" target="_blank">
				<img src="<?php echo $member['openavatar_tweibo']; ?>" class="img-circle img-polaroid" width="20" height="20">
				<?php echo $member['openname_tweibo']; ?>
				<i class="icon-share"></i>
			</a>
			<a href="javascript:;" class="btn btn-link pull-right unbind" data-type="tweibo">解除绑定</a>
		<?php }else{ ?>
			<a href="javascript:;" class="btn btn-link pull-right bind" data-type="tweibo">绑定</a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php if(T163WEIBO_AKEY && T163WEIBO_SKEY){ ?>
<div class="input-label">
	<label class="label-text">网易微博：</label>
	<div class="label-box form-inline">
		<?php if($member['openid_t163weibo'] != ''){ ?>
			<a href="<?php echo $member['openurl_t163weibo']; ?>" target="_blank">
				<img src="<?php echo $member['openavatar_t163weibo']; ?>" class="img-circle img-polaroid" width="20" height="20">
				<?php echo $member['openname_t163weibo']; ?>
				<i class="icon-share"></i>
			</a>
			<a href="javascript:;" class="btn btn-link pull-right unbind" data-type="t163weibo">解除绑定</a>
		<?php }else{ ?>
			<a href="javascript:;" class="btn btn-link pull-right bind" data-type="t163weibo">绑定</a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php if(RENREN_AID && RENREN_AKEY && RENREN_SKEY){ ?>
<div class="input-label">
	<label class="label-text">人人网：</label>
	<div class="label-box form-inline">
		<?php if($member['openid_renren'] != ''){ ?>
			<a href="<?php echo $member['openurl_renren']; ?>" target="_blank">
				<img src="<?php echo $member['openavatar_renren']; ?>" class="img-circle img-polaroid" width="20" height="20">
				<?php echo $member['openname_renren']; ?>
				<i class="icon-share"></i>
			</a>
			<a href="javascript:;" class="btn btn-link pull-right unbind" data-type="renren">解除绑定</a>
		<?php }else{ ?>
			<a href="javascript:;" class="btn btn-link pull-right bind" data-type="renren">绑定</a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php if(BAIDU_AKEY && BAIDU_SKEY){ ?>
<div class="input-label">
	<label class="label-text">百度：</label>
	<div class="label-box form-inline">
		<?php if($member['openid_baidu'] != ''){ ?>
			<a href="<?php echo $member['openurl_baidu']; ?>" target="_blank">
				<img src="<?php echo $member['openavatar_baidu']; ?>" class="img-circle img-polaroid" width="20" height="20">
				<?php echo $member['openname_baidu']; ?>
				<i class="icon-share"></i>
			</a>
			<a href="javascript:;" class="btn btn-link pull-right unbind" data-type="baidu">解除绑定</a>
		<?php }else{ ?>
			<a href="javascript:;" class="btn btn-link pull-right bind" data-type="baidu">绑定</a>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php include('sysapp/global_js.php'); ?>
<script>
var childWindow, int;
$(function(){
	$('.bind').click(function(){
		checkUserLogin();
		childWindow = window.open('../../connect/' + $(this).data('type') + '/redirect.php', 'LoginWindow', 'width=850,height=520,menubar=0,scrollbars=1,resizable=1,status=1,titlebar=0,toolbar=0,location=1');
	});
	$('.unbind').click(function(){
		$.ajax({
			type : 'POST',
			url : 'ajax.php',
			data : 'ac=unbind&fromsite=' + $(this).data('type')
		}).done(function(){
			location.reload();
		});
	});
});
function checkUserLogin(){
	$.removeCookie('fromsite', {path:'/'});
	int = setInterval(function(){
		getLoginCookie(int);
	}, 500);
}
function getLoginCookie(){
	if($.cookie('fromsite')){
		childWindow.close();
		window.clearInterval(int);
		var title;
		switch($.cookie('fromsite')){
			case 'sinaweibo': title = '新浪微博'; break;
			case 'tweibo': title = '腾讯微博'; break;
			case 't163weibo': title = '网易微博'; break;
			case 'renren': title = '人人网'; break;
			case 'baidu': title = '百度'; break;
			default: return false;
		}
		//验证该三方登录账号是否已绑定过本地账号，没有则绑定到自己账号
		$.ajax({
			url:'ajax.php',
			data:'ac=3loginBind',
			success: function(msg){
				if(msg == 'ERROR_LACK_OF_DATA'){
					window.parent.ZENG.msgbox.show('未知错误，建议重启浏览器后重新操作', 1, 2000);
				}else if(msg == 'ERROR_OPENID_IS_USED'){
					window.parent.ZENG.msgbox.show('该' + title + '账号已经绑定过其它本地账号', 1, 2000);
				}else{
					location.reload();
				}
			}
		});
	}
}
</script>
</body>
</html>