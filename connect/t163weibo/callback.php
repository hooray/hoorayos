<?php
	require('../../global.php');
	require('connect/t163weibo/tblog.class.php');
	$oauth = new OAuth(T163WEIBO_AKEY, T163WEIBO_SKEY, $_SESSION['request_token_163']['oauth_token'], $_SESSION['request_token_163']['oauth_token_secret']);
	if($access_token = $oauth->getAccessToken($_REQUEST['oauth_token'])){
		$tblog = new TBlog(T163WEIBO_AKEY, T163WEIBO_SKEY, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$me = $tblog->verify_credentials();
		cookie('fromsite', 't163weibo', 3600);
		session('openid', $me['id']);
		session('openname', $me['name']);
		session('openavatar', $me['profile_image_url']);
		session('openurl', 'http://t.163.com/'.$me['screen_name']);
		echo '网易微博授权成功';
	}else{
		echo '网易微博授权失败';
	}
?>