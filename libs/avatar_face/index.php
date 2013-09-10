<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>仿新浪微博上传头像功能，头像上传组件，在线头像上传，在线拍摄头像,avatar_face,avatar_test</title>
<meta name="Keywords" content="flash头像上传组件，仿新浪微博头像上传组件，仿DZ头像上传组件，头像图片剪裁上传组件" />
<meta name="Description" content="flash头像制作组件，flash头像上传组件,avatar_face,avatar_test" />
<style> 
*{line-height:30px;font-family:verdana;color:#333;}
h1,h3{margin:15px 0 5px 0;padding:0;font-size:17px;font-family:microsoft yahei;font-weight:normal;border-bottom:1px dashed #ccc;color:000;}
span{color:#f30;margin:0 4px;}
</style>
<script language="JavaScript" type="text/javascript"> 
function avatar_success()
{
	alert("头像保存成功"); 
	location.href="./";
}
</script>
</head>
 
<body>
<h1>Flash头像上传组件效果预览</h1>
<embed src="face.swf" quality="high" wmode="opaque" FlashVars="defaultImg=1_120.jpg?id=<?php echo create_password(6); ?>" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="530" height="480"></embed><br />
效果演示网址：<a href="http://www.16934.net/demo/" title="仿新浪微博上传头像" target="_blank">http://www.16934.net/demo/</a>
<h3>Flash头像上传组件功能介绍</h3>
1.上传并预览，用户可以任意选择区域，支持头像旋转
<br />
2.无论图片过大还是过小，都可以按照固定大小显示<br />
3.支持头像拍照保存<br />
4.支持Asp/php/Jsp/Asp.Net等语言的任意调用<br />
5.兼容性好，任何浏览器都正常使用
<h3>注意事项</h3>
1.本演示需要在支持Php语言环境下使用<br />
2.需要浏览器安装flash播放器(一般都支持)
<h3>联系咨询</h3>
<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=631897379&site=qq&menu=yes"><img src='http://wpa.qq.com/pa?p=1:631897379:41' border='0' vspace='8' align='absmiddle' alt='联系咨询' /></a>

<?php

function create_password($pw_length = 8)
{
    $randpwd = '';
    for ($i = 0; $i < $pw_length; $i++) 
    {
        $randpwd .= chr(mt_rand(33, 126));
    }
    return $randpwd;
} 

?>
</body>
</html>

