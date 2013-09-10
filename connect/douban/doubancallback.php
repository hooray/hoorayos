<?php
session_start();
require('doubanv2.class.php');
require('config.php');
$o = new DoubanOAuthV2( APIKEY,Secret);
if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {

	}
}

if ($token) {
	$_SESSION['token'] = $token;
	@setcookie( 'douban_'.$o->client_id, http_build_query($token) );
	header('location:main.php');
}else{
	header('location:index.php');
}
?>