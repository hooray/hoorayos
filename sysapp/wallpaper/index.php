<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	
	$wallpaperList = $db->select(0, 0, 'tb_wallpaper', '*', '', 'tbid asc');
	foreach($wallpaperList as &$v){
		$v['s_url'] = getFileInfo($v['url'], 'simg');
	}
	$wallpapertype = $db->select(0, 1, 'tb_member', 'wallpapertype', 'and tbid='.session('member_id'));
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>壁纸设置</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<div class="title">
	<ul>
		<li class="focus">壁纸设置</li>
		<li><a href="../skin/index.php">皮肤设置</a></li>
	</ul>
</div>
<div class="wallpapertype form-inline">
	<div class="btn-group fl">
		<a class="btn disabled">系统壁纸</a><a class="btn" href="custom.php">自定义</a>
	</div>
	<div class="fr">
		<label>显示方式：</label>
		<select name="wallpapertype" id="wallpapertype" style="width:100px">
			<option value="tianchong" <?php if($wallpapertype['wallpapertype'] == 'tianchong'){echo 'selected';} ?>>填充</option>
			<option value="shiying" <?php if($wallpapertype['wallpapertype'] == 'shiying'){echo 'selected';} ?>>适应</option>
			<option value="pingpu" <?php if($wallpapertype['wallpapertype'] == 'pingpu'){echo 'selected';} ?>>平铺</option>
			<option value="lashen" <?php if($wallpapertype['wallpapertype'] == 'lashen'){echo 'selected';} ?>>拉伸</option>
			<option value="juzhong" <?php if($wallpapertype['wallpapertype'] == 'juzhong'){echo 'selected';} ?>>居中</option>
		</select>
	</div>
</div>
<ul class="wallpaper">
	<?php
		foreach($wallpaperList as $key => $value){
			if($key % 3 == 2){
				echo '<li class="three" wpid="'.$value['tbid'].'">';
			}else{
				echo '<li wpid="'.$value['tbid'].'">';
			}
				echo '<img src="../../'.$value['s_url'].'">';
				echo '<div>'.$value['title'].'</div>';
			echo '</li>';
		}
	?>
</ul>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	$("#wallpapertype").on('change',function(){
		window.parent.HROS.wallpaper.update(0, $('#wallpapertype').val(), '');
	});
	$('.wallpaper li').on('click',function(){
		window.parent.HROS.wallpaper.update(1, $('#wallpapertype').val(), $(this).attr('wpid'));
	});
});
</script>
</body>
</html>