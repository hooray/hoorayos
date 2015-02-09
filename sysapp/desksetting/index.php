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
	$vertical = getAppVerticalSpacing();
	$horizontal = getAppHorizontalSpacing();
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
		<div class="input-prepend input-append">
			<button class="btn appsize-minus" type="button"><i class="icon-minus"></i></button>
			<input type="text" name="appsize" class="text-center span1" value="<?php echo $size; ?>">
			<button class="btn appsize-plus" type="button"><i class="icon-plus"></i></button>
		</div>
	</div>
</div>
<div class="input-label">
	<label class="label-text">垂直间距：</label>
	<div class="label-box form-inline control-group">
		<div class="input-prepend input-append">
			<button class="btn appverticalspacing-minus" type="button"><i class="icon-minus"></i></button>
			<input type="text" name="appverticalspacing" class="text-center span1" value="<?php echo $vertical; ?>">
			<button class="btn appverticalspacing-plus" type="button"><i class="icon-plus"></i></button>
		</div>
	</div>
</div>
<div class="input-label">
	<label class="label-text">水平间距：</label>
	<div class="label-box form-inline control-group">
		<div class="input-prepend input-append">
			<button class="btn apphorizontalspacing-minus" type="button"><i class="icon-minus"></i></button>
			<input type="text" name="apphorizontalspacing" class="text-center span1" value="<?php echo $horizontal; ?>">
			<button class="btn apphorizontalspacing-plus" type="button"><i class="icon-plus"></i></button>
		</div>
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
	var updateSize = function(size){
		if(size < 32){
			size = 32;
		}else if(size > 64){
			size = 64
		}
		$('input[name="appsize"]').val(size);
		window.parent.HROS.app.updateSize(size);
	};
	$('.appsize-minus, .appsize-plus').click(function(){
		var size = parseInt($('input[name="appsize"]').val());
		if($(this).hasClass('appsize-minus')){
			size = size - 1;
		}else{
			size = size + 1;
		}
		updateSize(size);
	});
	$('input[name="appsize"]').keyup(function(){
		var size = parseInt($('input[name="appsize"]').val());
		updateSize(size);
	});
	var updateVertical = function(vertical){
		if(vertical < 0){
			vertical = 0;
		}else if(vertical > 100){
			vertical = 100
		}
		$('input[name="appverticalspacing"]').val(vertical);
		window.parent.HROS.app.updateVertical(vertical);
	};
	$('.appverticalspacing-minus, .appverticalspacing-plus').click(function(){
		var vertical = parseInt($('input[name="appverticalspacing"]').val());
		if($(this).hasClass('appverticalspacing-minus')){
			vertical = vertical - 1;
		}else{
			vertical = vertical + 1;
		}
		updateVertical(vertical);
	});
	$('input[name="appverticalspacing"]').keyup(function(){
		var vertical = parseInt($('input[name="appverticalspacing"]').val());
		updateVertical(vertical);
	});
	var updateHorizontal = function(horizontal){
		if(horizontal < 0){
			horizontal = 0;
		}else if(horizontal > 100){
			horizontal = 100
		}
		$('input[name="apphorizontalspacing"]').val(horizontal);
		window.parent.HROS.app.updateHorizontal(horizontal);
	};
	$('.apphorizontalspacing-minus, .apphorizontalspacing-plus').click(function(){
		var horizontal = parseInt($('input[name="apphorizontalspacing"]').val());
		if($(this).hasClass('apphorizontalspacing-minus')){
			horizontal = horizontal - 1;
		}else{
			horizontal = horizontal + 1;
		}
		updateHorizontal(horizontal);
	});
	$('input[name="apphorizontalspacing"]').keyup(function(){
		var horizontal = parseInt($('input[name="apphorizontalspacing"]').val());
		updateHorizontal(horizontal);
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