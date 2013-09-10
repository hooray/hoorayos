<?php
	require('../../global.php');
	require('connect/tweibo/Tencent.php');
	OAuth::init(TWEIBO_AKEY, TWEIBO_SKEY);
	$tweibo_url = OAuth::getAuthorizeURL(TWEIBO_CALLBACK_URL);
	redirect($tweibo_url, 0);
?>