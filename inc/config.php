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

trim(@extract($_POST));
trim(@extract($_GET));
trim(@extract($_REQUEST));

$_CONFIG = array();
//站点安全设置
$_CONFIG['authkey'] = 'hoorayos'; //站点加密密钥，可随意更改

//文件上传大小限制，单位MB
$uploadFileMaxSize = 20;
//允许文件上传类型
$uploadFileType = array(
	array('ext'=>'rar', 'icon'=>'img/ui/file_rar.png'),
	array('ext'=>'zip', 'icon'=>'img/ui/file_zip.png'),
	array('ext'=>'7z', 'icon'=>'img/ui/file_7z.png'),
	array('ext'=>'jpeg', 'icon'=>'img/ui/file_jpeg.png'),
	array('ext'=>'jpg', 'icon'=>'img/ui/file_jpeg.png'),
	array('ext'=>'gif', 'icon'=>'img/ui/file_gif.png'),
	array('ext'=>'bmp', 'icon'=>'img/ui/file_bmp.png'),
	array('ext'=>'png', 'icon'=>'img/ui/file_png.png'),
	array('ext'=>'ico', 'icon'=>'img/ui/file_ico.png'),
	array('ext'=>'doc', 'icon'=>'img/ui/file_word.png'),
	array('ext'=>'docx', 'icon'=>'img/ui/file_word.png'),
	array('ext'=>'xls', 'icon'=>'img/ui/file_excel.png'),
	array('ext'=>'xlsx', 'icon'=>'img/ui/file_excel.png'),
	array('ext'=>'ppt', 'icon'=>'img/ui/file_ppt.png'),
	array('ext'=>'pptx', 'icon'=>'img/ui/file_ppt.png'),
	array('ext'=>'pdf', 'icon'=>'img/ui/file_pdf.png'),
	array('ext'=>'wma', 'icon'=>'img/ui/file_wma.png'),
	array('ext'=>'mp3', 'icon'=>'img/ui/file_mp3.png'),
	array('ext'=>'txt', 'icon'=>'img/ui/file_txt.png'),
	array('ext'=>'js', 'icon'=>'img/ui/file_txt.png'),
	array('ext'=>'css', 'icon'=>'img/ui/file_txt.png'),
	array('ext'=>'html', 'icon'=>'img/ui/file_txt.png')
);
//禁止文件上传类型
$uploadFileUnType = array('exe', 'cmd', 'php');
//应用分类
$apptype = array(
	array('id'=>1, 'name'=>'系统'),
	array('id'=>2, 'name'=>'游戏'),
	array('id'=>3, 'name'=>'影音'),
	array('id'=>4, 'name'=>'图书'),
	array('id'=>5, 'name'=>'生活'),
	array('id'=>6, 'name'=>'工具')
);
//错误代码
$errorcode = array(
	'noLogin'=>'1000',
	'noAdmin'=>'1001',
	'noPermissions'=>'1002'
);

//数据库连接配置信息
$db_hoorayos_config = array(
	'dsn'=>'mysql:host=localhost;dbname=hoorayos',
	'name'=>'root',
	'password'=>''
);

//创建数据库连接
$db = new HRDB($db_hoorayos_config);

//社区登录公用变量配置信息
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
?>