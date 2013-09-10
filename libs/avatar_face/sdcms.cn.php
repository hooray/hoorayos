<?php   
/*
*仿新浪微博上传头像功能
*联系QQ:631897379
*演示：http://www.16934.net/demo
*png1,png2,png3分别为3个尺寸头像的参数，经过base64解密后保存即可
*/
$filename120 = "1_120.jpg"; 
$filename48 = "1_48.jpg"; 
$filename24 = "1_24.jpg";   

$somecontent1=base64_decode($_POST['png1']);   
$somecontent2=base64_decode($_POST['png2']);  
$somecontent3=base64_decode($_POST['png3']);    

if($handle=fopen($filename120,"w+")) 
{   
	if(!fwrite($handle,$somecontent1)==FALSE)
	{   
		fclose($handle);
	}
}  

if($handle=fopen($filename48,"w+"))
{   
	if(!fwrite($handle,$somecontent2)==FALSE) 
	{   
		fclose($handle);  
	}
} 

if($handle=fopen($filename24,"w+"))
{   
	if(!fwrite($handle,$somecontent3)==FALSE)
	{   
		fclose($handle);  
	}
}  
echo "success=上传成功";
?>