<?php
	require('../../global.php');
	require('connect/t163weibo/tblog.class.php');
	$oauth = new OAuth(T163WEIBO_AKEY, T163WEIBO_SKEY);
	$request_token = $oauth->getRequestToken();
	$t163weibo_url = $oauth->getAuthorizeURL($request_token['oauth_token'], T163WEIBO_CALLBACK_URL);
	$_SESSION['request_token_163'] = $request_token;
	redirect($t163weibo_url, 0);
?>