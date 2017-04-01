<?php
	require('../../global.php');

	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}

	$wallpapertype = $db->get('tb_member', 'wallpapertype', array(
		'tbid' => session('member_id')
	));
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>壁纸设置</title>
	<?php include('sysapp/global_css.php'); ?>
	<link rel="stylesheet" href="../../static/css/sys.css">
</head>
<body>
	<ul class="nav nav-tabs nav-title-bar">
	    <li class="active"><a>壁纸设置</a></li>
	    <li><a href="../skin/index.php">皮肤设置</a></li>
	</ul>
	<div class="wallpapertype form-inline">
		<div class="btn-group fl">
			<a class="btn btn-default disabled">系统壁纸</a><a class="btn btn-default" href="custom.php">自定义</a>
		</div>
		<div class="fr">
			<label>显示方式：</label>
			<select class="form-control" name="wallpapertype" id="wallpapertype" style="width:100px;display:inline-block">
				<option value="tianchong" <?php if($wallpapertype == 'tianchong'){echo 'selected';} ?>>填充</option>
				<option value="shiying" <?php if($wallpapertype == 'shiying'){echo 'selected';} ?>>适应</option>
				<option value="pingpu" <?php if($wallpapertype == 'pingpu'){echo 'selected';} ?>>平铺</option>
				<option value="lashen" <?php if($wallpapertype == 'lashen'){echo 'selected';} ?>>拉伸</option>
				<option value="juzhong" <?php if($wallpapertype == 'juzhong'){echo 'selected';} ?>>居中</option>
			</select>
		</div>
	</div>
	<div class="wallpaper">
		<ul>
			<?php
				foreach($db->select('tb_wallpaper', '*') as $v){
					echo '<li wpid="'.$v['tbid'].'">';
						echo '<img src="../../'.getFileInfo($v['url'], 'simg').'">';
						echo '<div>'.$v['title'].'</div>';
					echo '</li>';
				}
			?>
		</ul>
	</div>
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
