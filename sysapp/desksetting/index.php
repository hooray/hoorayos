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
	<link rel="stylesheet" href="../../static/css/sys.css">
	<link rel="stylesheet" href="../../js/bootstrap-slider-7.1.1/dist/css/bootstrap-slider.min.css">
	<style media="screen">
		.slider.slider-horizontal{
			margin-top: 8px;
		}
	</style>
</head>
<body>
	<div class="title">桌面设置</div>
	<form class="form-horizontal">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">默认显示桌面：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="desk" value="1" <?php if($desk == 1){echo 'checked';} ?>>第 1 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="desk" value="2" <?php if($desk == 2){echo 'checked';} ?>>第 2 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="desk" value="3" <?php if($desk == 3){echo 'checked';} ?>>第 3 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="desk" value="4" <?php if($desk == 4){echo 'checked';} ?>>第 4 屏桌面
						</label>
						<label class="radio-inline">
							<input type="radio" name="desk" value="5" <?php if($desk == 5){echo 'checked';} ?>>第 5 屏桌面
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标排列方式：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="appxy" value="x" <?php if($xy == 'x'){echo 'checked';} ?>>横向排列
						</label>
						<label class="radio-inline">
							<input type="radio" name="appxy" value="y" <?php if($xy == 'y'){echo 'checked';} ?>>纵向排列
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标显示尺寸：</label>
					<div class="col-sm-10">
						<input id="inputAppsize" name="appsize" data-slider-id='inputAppsizeSlider' type="text" data-slider-min="32" data-slider-max="128" data-slider-step="1" data-slider-ticks="[32, 48, 64, 128]" data-slider-ticks-labels='["小", "中", "大", "超大"]' data-slider-ticks-positions="[0, 25, 50, 100]" data-slider-ticks-snap-bounds="1" data-slider-value="<?php echo $size; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标垂直间距：</label>
					<div class="col-sm-10">
						<input id="inputAppverticalspacing" name="appverticalspacing" data-slider-id='inputAppverticalspacingSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-ticks="[0, 25, 50, 75, 100]" data-slider-ticks-labels='["0", "25", "50", "75", "100"]' data-slider-ticks-snap-bounds="1" data-slider-value="<?php echo $vertical; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">图标水平间距：</label>
					<div class="col-sm-10">
						<input id="inputApphorizontalspacing" name="apphorizontalspacing" data-slider-id='inputApphorizontalspacingSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-ticks="[0, 25, 50, 75, 100]" data-slider-ticks-labels='["超紧缩", "紧缩", "标准", "加宽", "超加宽"]' data-slider-ticks-snap-bounds="1" data-slider-value="<?php echo $horizontal; ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">应用码头位置：</label>
					<div class="col-sm-10">
						<label class="radio-inline">
							<input type="radio" name="dockpos" value="top" <?php if($pos == 'top'){echo 'checked';} ?>>顶部
						</label>
						<label class="radio-inline">
							<input type="radio" name="dockpos" value="left" <?php if($pos == 'left'){echo 'checked';} ?>>左侧
						</label>
						<label class="radio-inline">
							<input type="radio" name="dockpos" value="right" <?php if($pos == 'right'){echo 'checked';} ?>>右侧
						</label>
						<label class="radio-inline">
							<input type="radio" name="dockpos" value="none" <?php if($pos == 'none'){echo 'checked';} ?>>隐藏
						</label>
					</div>
				</div>
			</div>
		</div>
	</form>
	<?php include('sysapp/global_js.php'); ?>
	<script src="../../js/bootstrap-slider-7.1.1/dist/bootstrap-slider.min.js"></script>
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
		$('#inputAppsize').slider({
			formatter: function(value){
				return '图标尺寸：' + value;
			}
		}).on('slideStop', function(){
			window.parent.HROS.app.updateSize(parseInt($(this).val()));
		});
		$('#inputAppverticalspacing').slider({
			formatter: function(value){
				return '图标垂直间距：' + value;
			}
		}).on('slideStop', function(){
			window.parent.HROS.app.updateVertical(parseInt($(this).val()));
		});
		$('#inputApphorizontalspacing').slider({
			formatter: function(value){
				return '图标水平间距：' + value;
			}
		}).on('slideStop', function(){
			window.parent.HROS.app.updateHorizontal(parseInt($(this).val()));
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
