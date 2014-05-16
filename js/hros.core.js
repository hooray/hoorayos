var version   = '3.2.1';        //版本号
var ajaxUrl   = 'ajax.php';     //所有ajax操作指向页面
var TEMP      = {};
var HROS      = {};

HROS.CONFIG = {
	website         : 'http://' + location.hostname + location.pathname, //网站地址，用于分享应用时调用。一般无需修改
	sinaweiboAppkey : '',       //新浪微博appkey。首页加载会自动初始化，一般无需修改
	tweiboAppkey    : '',       //腾讯微博appkey。首页加载会自动初始化，一般无需修改
	memberID        : 0,        //用户id
	desk            : 1,        //当前显示桌面
	dockPos         : '',       //应用码头位置，参数有：top,left,right
	appXY           : '',       //应用排列方式，参数有：x,y
	appSize         : '',       //图标显示尺寸，参数有：s,m
	appButtonTop    : 20,       //快捷方式top初始位置
	appButtonLeft   : 20,       //快捷方式left初始位置
	windowIndexid   : 10000,    //窗口z-index初始值
	widgetIndexid   : 1,        //挂件z-index初始值
	windowMinWidth  : 215,      //窗口最小宽度
	windowMinHeight : 59,       //窗口最小高度
	wallpaperState  : 1,        //1系统壁纸,2自定义壁纸,3网络壁纸
	wallpaper       : '',       //壁纸
	wallpaperType   : '',       //壁纸显示类型，参数有：tianchong,shiying,pingpu,lashen,juzhong
	wallpaperWidth  : 0,        //壁纸宽度
	wallpaperHeight : 0         //壁纸高度
};

HROS.VAR = {
	zoomLevel       : 1,
	isAppMoving     : false,    //桌面应用是否正在移动中，也就是ajax操作是否正在执行中
	dock            : [],
	desk1           : [],
	desk2           : [],
	desk3           : [],
	desk4           : [],
	desk5           : [],
	folder          : []
};