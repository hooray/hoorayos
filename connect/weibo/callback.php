<?php
	require('../../global.php');
	require('saetv2.ex.class.php');
	$o = new SaeTOAuthV2(WEIBO_AKEY, WEIBO_SKEY);
	if(isset($_REQUEST['code'])){
		$keys = array();
		$keys['code'] = $_REQUEST['code'];
		$keys['redirect_uri'] = WEIBO_CALLBACK_URL;
		try{
			$token = $o->getAccessToken('code', $keys) ;
		}catch(OAuthException $e){
			
		}
	}
	if($token){
		$c = new SaeTClientV2(WEIBO_AKEY, WEIBO_SKEY, $token['access_token']);
		$uid_get = $c->get_uid();
		$user = $c->show_user_by_id($uid_get['uid']);
		cookie('fromsite', 'weibo', 3600);
		cookie('fromsitename', $user['screen_name'], 3600);
		session('openid', $token['uid']);
		session('openname', $user['screen_name']);
		session('openavatar', $user['profile_image_url']);
		session('openurl', 'http://weibo.com/'.$user['profile_url']);
		echo '新浪微博授权成功';
	}else{
		echo '新浪微博授权失败';
	}
?>
