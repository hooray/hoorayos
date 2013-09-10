<?php
	require('../../global.php');
	require('connect/sinaweibo/saetv2.ex.class.php');
	$o = new SaeTOAuthV2(SINAWEIBO_AKEY, SINAWEIBO_SKEY);
	$sinaweibo_url = $o->getAuthorizeURL(SINAWEIBO_CALLBACK_URL);
	redirect($sinaweibo_url, 0);
?>