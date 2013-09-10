<?php
	require('../../global.php');
	require('connect/renren/class/config.inc.php');
	$renren_url = 'https://graph.renren.com/oauth/authorize?client_id='.RENREN_AID.'&response_type=code&scope='.$config->scope.'&state=a%3d1%26b%3d2&redirect_uri='.RENREN_CALLBACK_URL.'&x_renew=true';
	redirect($renren_url, 0);
?>