<?php
	require('../../global.php');
	require('saetv2.ex.class.php');
	$o = new SaeTOAuthV2(WEIBO_AKEY, WEIBO_SKEY);
	$weibo_url = $o->getAuthorizeURL(WEIBO_CALLBACK_URL);
	redirect($weibo_url, 0);
?>