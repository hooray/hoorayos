<?php
	require('../../global.php');
	require('connect/renren/class/RenrenOAuthApiService.class.php');
	require('connect/renren/class/RenrenRestApiService.class.php');
	$code = $_GET["code"];
	$oauthApi = new RenrenOAuthApiService;
	$post_params = array(
		'client_id' => RENREN_AKEY,
		'client_secret' => RENREN_SKEY,
		'redirect_uri' => RENREN_CALLBACK_URL,
		'grant_type' => 'authorization_code',
		'code' => $code
	);
	$token_url = 'http://graph.renren.com/oauth/token';
	$access_info = $oauthApi->rr_post_curl($token_url, $post_params);//使用code换取token
	if($access_info["access_token"]){
		cookie('fromsite', 'renren', 3600);
		session('openid', $access_info["access_token"]);
		session('openname', $access_info['user']['name']);
		session('openavatar', $access_info['user']['avatar'][1]['url']);
		session('openurl', 'http://www.renren.com/'.$access_info['user']['id']);
		echo '人人网授权成功';
	}else{
		echo '人人网授权失败';
	}
?>