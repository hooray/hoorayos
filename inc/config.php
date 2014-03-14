<?php
header("Content-type: text/html; charset=utf-8");

ob_start();
session('[start]');

error_reporting(E_ERROR | E_WARNING);

//定义魔术变量
if(version_compare(PHP_VERSION, '5.4.0', '<')){
    ini_set('magic_quotes_runtime', 0);
    define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? TRUE : FALSE);
}else{
    define('MAGIC_QUOTES_GPC', FALSE);
}

//设置时区
date_default_timezone_set('Asia/Shanghai');

//把所有全局变量用discuz的daddslashes函数进行过滤
$_GET = daddslashes($_GET, 1, TRUE);
$_POST = daddslashes($_POST, 1, TRUE);
$_REQUEST = daddslashes($_REQUEST, 1, TRUE);
$_COOKIE = daddslashes($_COOKIE, 1, TRUE);

$_CONFIG = array(
	'authkey' => 'hoorayos', //站点加密密钥，可随意更改
	'COOKIE_PREFIX' => '',
	'COOKIE_EXPIRE' => 0,
	'COOKIE_PATH' => '/',
	'COOKIE_DOMAIN' => '',
	'SESSION_PREFIX' => 'hoorayos'
);

//文件上传类型
$uploadFileType = array(
	'rar'  => 'img/ui/file_zip.png',
	'zip'  => 'img/ui/file_zip.png',
	'7z'   => 'img/ui/file_zip.png',
	'jpeg' => 'img/ui/file_image.png',
	'jpg'  => 'img/ui/file_image.png',
	'gif'  => 'img/ui/file_image.png',
	'bmp'  => 'img/ui/file_image.png',
	'png'  => 'img/ui/file_image.png',
	'doc'  => 'img/ui/file_word.png',
	'docx' => 'img/ui/file_word.png',
	'xls'  => 'img/ui/file_excel.png',
	'xlsx' => 'img/ui/file_excel.png',
	'ppt'  => 'img/ui/file_ppt.png',
	'pptx' => 'img/ui/file_ppt.png',
	'pdf'  => 'img/ui/file_pdf.png',
	'wma'  => 'img/ui/file_music.png',
	'mp3'  => 'img/ui/file_music.png',
	'txt'  => 'img/ui/file_txt.png'
);
//文件上传大小限制，单位MB
$uploadFileMaxSize = 20000;

//错误代码
$errorcode = array(
	'noLogin'=>'1000',
	'noAdmin'=>'1001',
	'noPermissions'=>'1002'
);

//创建数据库连接
$db = new medoo(array(
	'database_name' => 'hoorayos',
	'username' => 'root',
	'password' => '',
	'option' => array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	)
));

//社区登录公用变量配置信息
//QQ
define('QQ_AKEY',                  '');
define('QQ_SKEY',                  '');
define('QQ_CALLBACK_URL',          'http://[替换网站域名]/connect/qq/callback.php');
//新浪微博
define('SINAWEIBO_AKEY',           '');
define('SINAWEIBO_SKEY',           '');
define('SINAWEIBO_CALLBACK_URL',   'http://[替换网站域名]/connect/sinaweibo/callback.php');
//腾讯微博
define('TWEIBO_AKEY',              '');
define('TWEIBO_SKEY',              '');
define('TWEIBO_CALLBACK_URL',      'http://[替换网站域名]/connect/tweibo/callback.php');
//网易微博
define('T163WEIBO_AKEY',           '');
define('T163WEIBO_SKEY',           '');
define('T163WEIBO_CALLBACK_URL',   'http://[替换网站域名]/connect/t163weibo/callback.php');
//人人网
define('RENREN_AID',               '');
define('RENREN_AKEY',              '');
define('RENREN_SKEY',              '');
define('RENREN_CALLBACK_URL',      'http://[替换网站域名]/connect/renren/callback.php');
//百度
define('BAIDU_AKEY',               '');
define('BAIDU_SKEY',               '');
define('BAIDU_CALLBACK_URL',       'http://[替换网站域名]/connect/baidu/callback.php');
define('BAIDU_DOMAIN',             '.[替换网站域名]');
//豆瓣
define('DOUBAN_AKEY',              '');
define('DOUBAN_SKEY',              '');
define('DOUBAN_CALLBACK_URL',      'http://[替换网站域名]/connect/douban/callback.php');
?>