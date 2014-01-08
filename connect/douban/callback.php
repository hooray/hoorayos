<?php
	require('../../global.php');
	require('connect/douban/DoubanOauth.php');
	$appConfig = array(
		'client_id' => DOUBAN_AKEY,
		'secret' => DOUBAN_SKEY,
		'redirect_uri' => DOUBAN_CALLBACK_URL,
		// 可选参数（默认为douban_basic_common），授权范围。
		'scope' => 'douban_basic_common',
		// 可选参数（默认为false），是否在header中发送accessToken。
		'need_permission' => true
	);
	// 生成一个豆瓣Oauth类实例
	$douban = new DoubanOauth($appConfig);
	if(isset($_GET['code'])){
		// 设置authorizeCode
		$douban->setAuthorizeCode($_GET['code']);
		// 通过authorizeCode获取accessToken，至此完成用户授权
		$douban->requestAccessToken();
	
		$me = $douban->api('User.me.GET')->makeRequest();
		$me = json_decode($me);
		cookie('fromsite', 'douban', 3600);
		session('openid', $me->uid);
		session('openname', $me->name);
		session('openavatar', $me->avatar);
		session('openurl', $me->alt);
	}
?>