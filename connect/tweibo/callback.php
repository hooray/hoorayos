<?php
	require('../../global.php');
	require('connect/tweibo/Tencent.php');
	OAuth::init(TWEIBO_AKEY, TWEIBO_SKEY);
	if($_SESSION['t_access_token'] || ($_SESSION['t_openid'] && $_SESSION['t_openkey'])){
		$r = Tencent::api('user/info');
		$r = json_decode($r);
		cookie('fromsite', 'tweibo', 3600);
		session('openid', $r->data->openid);
		session('openname', $r->data->nick);
		session('openavatar', $r->data->head.'/20');
		session('openurl', 'http://t.qq.com/'.$r->data->name);
		echo '腾讯微博授权成功';
	}else{
		if($_GET['code']){//已获得code
			$code = $_GET['code'];
			$openid = $_GET['openid'];
			$openkey = $_GET['openkey'];
			//获取授权token
			$url = OAuth::getAccessToken($code, TWEIBO_CALLBACK_URL);
			$r = Http::request($url);
			parse_str($r, $out);
			//存储授权数据
			if($out['access_token']){
				$_SESSION['t_access_token'] = $out['access_token'];
				$_SESSION['t_refresh_token'] = $out['refresh_token'];
				$_SESSION['t_expire_in'] = $out['expires_in'];
				$_SESSION['t_code'] = $code;
				$_SESSION['t_openid'] = $openid;
				$_SESSION['t_openkey'] = $openkey;
				//验证授权
				$r = OAuth::checkOAuthValid();
				if($r){
					header('Location: '.TWEIBO_CALLBACK_URL);//刷新页面
				}else{
					echo '腾讯微博授权失败';
				}
			}else{
				exit($r);
			}
		}else{
			echo '腾讯微博授权失败';
		}
	}
?>