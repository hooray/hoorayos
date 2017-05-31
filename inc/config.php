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
	'rar'  => 'static/img/fileicon/zip.png',
	'zip'  => 'static/img/fileicon/zip.png',
	'7z'   => 'static/img/fileicon/zip.png',
	'jpeg' => 'static/img/fileicon/image.png',
	'jpg'  => 'static/img/fileicon/image.png',
	'gif'  => 'static/img/fileicon/image.png',
	'bmp'  => 'static/img/fileicon/image.png',
	'png'  => 'static/img/fileicon/image.png',
	'doc'  => 'static/img/fileicon/word.png',
	'docx' => 'static/img/fileicon/word.png',
	'xls'  => 'static/img/fileicon/excel.png',
	'xlsx' => 'static/img/fileicon/excel.png',
	'ppt'  => 'static/img/fileicon/ppt.png',
	'pptx' => 'static/img/fileicon/ppt.png',
	'pdf'  => 'static/img/fileicon/pdf.png',
	'wma'  => 'static/img/fileicon/music.png',
	'mp3'  => 'static/img/fileicon/music.png',
	'txt'  => 'static/img/fileicon/txt.png'
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
use Medoo\Medoo;
$db = new Medoo(array(
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