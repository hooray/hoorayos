<?php
	require('../../global.php');
	require('api/qqConnectAPI.php');
	$qc = new QC();
	$acs = $qc->qq_callback();
	$oid = $qc->get_openid();
	$qc = new QC($acs, $oid);
	if($oid){
		$user_info = $qc->get_user_info();
		cookie('fromsite', 'qq', 3600);
		session('openid', $oid);
		session('openname', $user_info['nickname']);
		session('openavatar', $user_info['figureurl_qq_2']);
		session('openurl', '');
		echo 'QQ授权成功';
	}else{
		echo 'QQ授权失败';
	}
?>