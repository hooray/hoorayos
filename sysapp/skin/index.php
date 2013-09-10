<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	
	//读取皮肤目录
	$fp = opendir('img/skins/');
	while($file = readdir($fp)){
		if(($file != '.') && ($file != '..')){
			$fileExt = strtolower(strrchr($file, '.'));
			if($fileExt == '.css'){
				$temp['name'] = basename($file, '.css');
				$temp['img'] = 'img/skins/'.$temp['name'].'/preview.png';
				$arr_file[] = $temp;
			}
		}
	}
	closedir($fp);
	
	$skin = getSkin();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>皮肤设置</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<div class="title">
	<ul>
		<li><a href="../wallpaper/index.php">壁纸设置</a></li>
		<li class="focus">皮肤设置</li>
	</ul>
</div>
<ul class="skin">
	<?php
		if($arr_file != NULL){
			foreach($arr_file as $file){
				if($file['name'] == $skin){
					echo '<li class="selected" skin="'.$file['name'].'"><img src="../../'.$file['img'].'" style="width:256px;height:156px"><div></div></li>';
				}else{
					echo '<li skin="'.$file['name'].'"><img src="../../'.$file['img'].'" style="width:256px;height:156px"><div></div></li>';
				}
			}
		}
	?>
</ul>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	$('.skin li').on('click', function(){
		$('.skin li').removeClass('selected');
		$(this).addClass('selected');
		var skin = $(this).attr('skin');
		$.ajax({
			url : 'index.ajax.php',
			data : 'ac=update&skin=' + skin,
			success : function(){
				window.parent.ZENG.msgbox.show("设置成功，正在切换皮肤，如果长时间没更新，请刷新页面", 4, 5000);
				window.parent.HROS.base.setSkin(skin, function(){
					window.parent.ZENG.msgbox._hide();
				});
			}
		});
	});
});
</script>
</body>
</html>