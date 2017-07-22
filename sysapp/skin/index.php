<?php
	require('../../global.php');

	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>皮肤设置</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<ul class="nav nav-tabs nav-title-bar">
	    <li><a href="../wallpaper/index.php">壁纸设置</a></li>
	    <li class="active"><a>皮肤设置</a></li>
	</ul>
	<ul class="skin">
		<?php
			//读取皮肤目录
			$fp = opendir('static/css/skins/');
			while($file = readdir($fp)){
				if(($file != '.') && ($file != '..')){
					$fileExt = strtolower(strrchr($file, '.'));
					if($fileExt == '.css'){
						$temp['name'] = basename($file, '.css');
						$temp['img'] = 'static/css/skins/'.$temp['name'].'/preview.png';
						$arr_file[] = $temp;
					}
				}
			}
			closedir($fp);
			if($arr_file != NULL){
				$skin = getSkin();
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
				type : 'POST',
				url : 'index.ajax.php',
				data : 'ac=update&skin=' + skin
			}).done(function(){
				window.parent.ZENG.msgbox.show("设置成功，正在切换皮肤，如果长时间没更新，请刷新页面", 4, 5000);
				window.parent.HROS.base.setSkin(skin, function(){
					window.parent.ZENG.msgbox._hide();
				});
			});
		});
	});
	</script>
</body>
</html>