<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	$avatar = '../../'.getAvatar(session('member_id'), 'l');
	$global_title = 'avatar';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>修改头像</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../static/css/sys.css">
</head>

<body>
<?php include('global_title.php'); ?>
<div style="width:630px;margin:0 auto;text-align:center;margin-top:20px">
	<p id="swfContainer">本组件需要安装Flash Player后才可使用，请从<a href="http://www.adobe.com/go/getflashplayer">这里</a>下载安装。</p>
</div>
<?php include('sysapp/global_js.php'); ?>
<script src="../../libs/fullAvatarEditor-2.3/scripts/swfobject.js"></script>
<script src="../../libs/fullAvatarEditor-2.3/scripts/fullAvatarEditor.js"></script>
<script>
swfobject.addDomLoadEvent(function () {
	var swf = new fullAvatarEditor('../../libs/fullAvatarEditor-2.3/fullAvatarEditor.swf', '../../libs/fullAvatarEditor-2.3/expressInstall.swf', 'swfContainer', {
			id: 'swf',
			upload_url: 'ajax.php?ac=avatar', //上传接口
			method: 'post', //传递到上传接口中的查询参数的提交方式。更改该值时，请注意更改上传接口中的查询参数的接收方式
			src_upload: 0, //是否上传原图片的选项，有以下值：0-不上传；1-上传；2-显示复选框由用户选择
			avatar_box_border_width: 0,
			avatar_sizes: '120*120|48*48|24*24',
			avatar_sizes_desc: '120*120像素|48*48像素|24*24像素',
			avatar_tools_visible: false,
			avatar_box_border_width: 1
		}, function (msg) {
			switch(msg.code){
				case 1:
					//alert("页面成功加载了组件！");
					break;
				case 2: 
					//alert("已成功加载图片到编辑面板。");
					break;
				case 3:
					if(msg.type == 0){
						//alert("摄像头已准备就绪且用户已允许使用。");
					}else if(msg.type == 1){
						//alert("摄像头已准备就绪但用户未允许使用！");
					}else{
						//alert("摄像头被占用！");
					}
					break;
				case 5: 
					if(msg.type == 0){
						window.parent.HROS.startMenu.getAvatar();
					}
					break;
			}
		}
	);
});
</script>
</body>
</html>