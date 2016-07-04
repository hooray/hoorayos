<?php
header("Content-type: text/html; charset=utf-8");

ob_start();
session('[start]');

error_reporting(E_ERROR | E_WARNING | E_PARSE);

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
	'COOKIE_PREFIX' => '', // Cookie前缀 避免冲突
	'COOKIE_EXPIRE' => 0, // Cookie有效期
	'COOKIE_PATH' => '/', // Cookie路径
	'COOKIE_DOMAIN' => '', // Cookie有效域名
    'COOKIE_SECURE' => false, // Cookie安全传输
    'COOKIE_HTTPONLY' => '', // Cookie httponly设置
	'SESSION_PREFIX' => 'hoorayos' // session 前缀
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
//单个文件上传大小，单位MB
$uploadFileSingleSize = 2;
//总文件上传大小，单位MB
$uploadFileSize = 10;

//错误代码
$errorcode = array(
	'noLogin'=>'1000',
	'noAdmin'=>'1001',
	'noPermissions'=>'1002'
);

//创建数据库连接
$db = new medoo(array(
	'database_type' => 'mysql',
	'database_name' => 'hoorayos',
	'server' => 'localhost',
	'port' => 3306,
	'username' => 'root',
	'password' => '',
	'charset' => 'utf8',
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
define('WEIBO_AKEY',               '');
define('WEIBO_SKEY',               '');
define('WEIBO_CALLBACK_URL',       'http://[替换网站域名]/connect/weibo/callback.php');
?>
