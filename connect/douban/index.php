<?php
require('doubanv2.class.php');
require('config.php');
$o = new DoubanOAuthV2(APIKEY,Secret);
$login_url = $o->getAuthorizeURL( CALLBACK_URL );
$login_url = "https://www.douban.com/service/auth2/auth".$login_url;
header("location:$login_url")
?>