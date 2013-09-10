<?php
	require('../../global.php');
	require('connect/baidu/Baidu.php');
	$baidu = new Baidu(BAIDU_AKEY, BAIDU_SKEY, BAIDU_CALLBACK_URL, new BaiduCookieStore(BAIDU_AKEY));
	$baidu_url = $baidu->getLoginUrl('', 'popup');
	redirect($baidu_url, 0);
?>