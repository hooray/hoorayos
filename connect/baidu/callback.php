<?php
	require('../../global.php');
	require('connect/baidu/Baidu.php');
	$baidu = new Baidu(BAIDU_AKEY, BAIDU_SKEY, BAIDU_CALLBACK_URL, new BaiduCookieStore(BAIDU_AKEY));
	$user = $baidu->getLoggedInUser();
	if($user){
		$apiClient = $baidu->getBaiduApiClientService();
		$profile = $apiClient->api('/rest/2.0/passport/users/getInfo', array('fields' => 'userid,username,sex,birthday,portrait'));
		if($profile === false){
			//get user profile failed
			var_dump(var_export(array('errcode' => $baidu->errcode(), 'errmsg' => $baidu->errmsg()), true));
			$user = null;
		}
	}
	if($user){
		cookie('fromsite', 'baidu', 3600);
		session('openid', $profile['userid']);
		session('openname', $profile['username']);
		session('openavatar', 'http://tb.himg.baidu.com/sys/portraitn/item/'.$profile['portrait']);
		session('openurl', 'http://www.baidu.com/p/'.$profile['username']);
		echo '百度授权成功';
	}else{
		echo '百度授权失败';
	}
?>