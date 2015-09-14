<?php
header('Content-Type: text/html; charset=utf-8');
$result = array();
$result['success'] = false;
$success_num = 0;
$msg = '';
//上传目录
$dir = $_SERVER['DOCUMENT_ROOT']."\upload";
// 取服务器时间+8位随机码作为部分文件名，确保文件名无重复。
$filename = date("YmdHis").'_'.floor(microtime() * 1000).'_'.createRandomCode(8);
// 处理原始图片开始------------------------------------------------------------------------>
//默认的 file 域名称是__source，可在插件配置参数中自定义。参数名：src_field_name
$source_pic = $_FILES["__source"];
//如果在插件中定义可以上传原始图片的话，可在此处理，否则可以忽略。
if ($source_pic)
{
	if ( $source_pic['error'] > 0 )
	{
		$msg .= $source_pic['error'];
	}
	else
	{
		//原始图片的文件名，如果是本地或网络图片为原始文件名、如果是摄像头拍照则为 *FromWebcam.jpg
		$sourceFileName = $source_pic["name"];
		//原始文件的扩展名(不包含“.”)
		$sourceExtendName = substr($sourceFileName, strripos($sourceFileName, "."));
		//保存路径
		$savePath = "$dir\php_source_$filename.".$sourceExtendName;
		//当前头像基于原图的初始化参数（只有上传原图时才会发送该数据，且发送的方式为POST），用于修改头像时保证界面的视图跟保存头像时一致，提升用户体验度。
		//修改头像时设置默认加载的原图url为当前原图url+该参数即可，可直接附加到原图url中储存，不影响图片呈现。
		$init_params = $_POST["__initParams"];
		$result['sourceUrl'] = toVirtualPath($savePath).$init_params;
		move_uploaded_file($source_pic["tmp_name"], $savePath);
		$success_num++;
	}
}
//<------------------------------------------------------------------------处理原始图片结束
// 处理头像图片开始------------------------------------------------------------------------>
//头像图片(file 域的名称：__avatar1,2,3...)。
$avatars = array("__avatar1", "__avatar2", "__avatar3");
$avatars_length = count($avatars);
for ( $i = 0; $i < $avatars_length; $i++ )
{ 
	$avatar = $_FILES[$avatars[$i]];
	$avatar_number = $i + 1;
	if ( $avatar['error'] > 0 )
	{
		$msg .= $avatar['error'];
	}
	else
	{
		$savePath = "$dir\php_avatar" . $avatar_number . "_$filename.jpg";
		$result['avatarUrls'][$i] = toVirtualPath($savePath);
		move_uploaded_file($avatar["tmp_name"], $savePath);
		$success_num++;
	}
} 
//<------------------------------------------------------------------------处理头像图片结束
//upload_url中传递的额外的参数，如果定义的method为get请换为$_GET
$result["userid"]	= $_POST["userid"];
$result["username"]	= $_POST["username"];

$result['msg'] = $msg;
if ($success_num > 0)
{
	$result['success'] = true;
}
//返回图片的保存结果（返回内容为json字符串）
print json_encode($result);

/**************************************************************
*  生成指定长度的随机码。
*  @param int $length 随机码的长度。
*  @access public
**************************************************************/
function createRandomCode($length)
{
	$randomCode = "";
	$randomChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0; $i < $length; $i++)
	{
		$randomCode .= $randomChars { mt_rand(0, 35) };
	}
	return $randomCode;
}

/**************************************************************
*  将物理路径转为虚拟路径。
*  @param string $physicalPpath 物理路径。
*  @access public
**************************************************************/
function toVirtualPath($physicalPpath)
{
	$virtualPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $physicalPpath);
	$virtualPath = str_replace("\\", "/", $virtualPath);
	return $virtualPath;
}
?>