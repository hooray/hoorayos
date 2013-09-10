<?php
require('doubanv2.class.php');
require('config.php');
session_start();
$access_token = $_SESSION['token'];
$douban = new Douban_Tclientv2(APIKEY,Secret,$access_token);
$myinfo = $douban->user_login();
var_dump($myinfo);
?>