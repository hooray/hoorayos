<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	
	$desk = getDesk();
	$xy = getAppXY();	
	$size = getAppSize();
	$pos = getDockPos();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>桌面设置</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<div class="title">默认桌面(登录后默认显示)</div>
<div class="input-label">
	<label class="label-text">默认显示：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="desk" value="1" <?php if($desk == 1){echo 'checked';} ?>>第1屏桌面</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="desk" value="2" <?php if($desk == 2){echo 'checked';} ?>>第2屏桌面</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="desk" value="3" <?php if($desk == 3){echo 'checked';} ?>>第3屏桌面</label>
		<label class="radio" style="margin-right:10px"><input type="radio" name="desk" value="4" <?php if($desk == 4){echo 'checked';} ?>>第4屏桌面</label>
		<label class="radio"><input type="radio" name="desk" value="5" <?php if($desk == 5){echo 'checked';} ?>>第5屏桌面</label>
	</div>
</div>
<div class="title">桌面图标设置</div>
<div class="input-label">
	<label class="label-text">排列方式：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="appxy" value="x" <?php if($xy == 'x'){echo 'checked';} ?>>横向排列</label>
		<label class="radio"><input type="radio" name="appxy" value="y" <?php if($xy == 'y'){echo 'checked';} ?>>纵向排列</label>
	</div>
</div>
<div class="input-label">
	<label class="label-text">显示尺寸：</label>
	<div class="label-box form-inline control-group">
		<label class="radio" style="margin-right:10px"><input type="radio" name="appsize" value="s" <?php if($size == 's'){echo 'checked';} ?>>小图标</label>
		<label class="radio"><input type="radio" name="appsize" value="m" <?php if($size == 'm'){echo 'checked';} ?>>大图标</label>
	</div>
</div>
<div class="title">应用码头设置</div>
<div class="dock_setting">
	<table>
		<tr>
			<td colspan="3">
				<div class="set_top form-inline"><label class="radio"><input type="radio" name="dockpos" value="top" <?php if($pos == 'top'){echo 'checked';} ?>>顶部</label></div>
			</td>
		</tr>
		<tr>
			<td width="75">
				<div class="set_left form-inline"><label class="radio"><input type="radio" name="dockpos" value="left" <?php if($pos == 'left'){echo 'checked';} ?>>左侧</label></div>
			</td>
			<td class="set_view set_view_<?php echo $pos; ?>"></td>
			<td width="75">
				<div class="set_right form-inline"><label class="radio"><input type="radio" name="dockpos" value="right" <?php if($pos == 'right'){echo 'checked';} ?>>右侧</label></div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div class="set_none form-inline"><label class="radio"><input type="radio" name="dockpos" value="none" <?php if($pos == 'none'){echo 'checked';} ?>>停用并隐藏（如果应用码头存在应用，则会将应用转移到当前桌面）</label></div>
			</td>
		</tr>
	</table>
</div>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	$('input[name="desk"]').change(function(){
		var desk = $('input[name="desk"]:checked').val();
		window.parent.HROS.deskTop.updateDefaultDesk(desk);
	});
	$('input[name="appxy"]').change(function(){
		var xy = $('input[name="appxy"]:checked').val();
		window.parent.HROS.app.updateXY(xy);
	});
	$('input[name="appsize"]').change(function(){
		var size = $('input[name="appsize"]:checked').val();
		window.parent.HROS.app.updateSize(size);
	});
	$('input[name="dockpos"]').change(function(){
		var pos = $('input[name="dockpos"]:checked').val();
		$('.set_view').removeClass('set_view_top set_view_left set_view_right set_view_none');
		$('.set_view').addClass('set_view_' + pos);
		window.parent.HROS.dock.updatePos(pos);
	});
});
</script>
</body>
</html>