<?php
	require('../../global.php');

	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}

	$wallpaper = $db->get('tb_member', '*', array(
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
<div class="title">
	<ul>
		<li class="focus">壁纸设置</li>
		<li><a href="../skin/index.php">皮肤设置</a></li>
	</ul>
</div>
<div class="wallpapertype form-inline">
	<div class="btn-group fl">
		<a class="btn btn-default" href="index.php">系统壁纸</a><a class="btn btn-default disabled">自定义</a>
	</div>
	<div class="fr">
		<label style="vertical-align:top">显示方式：</label>
		<select class="form-control" name="wallpapertype" id="wallpapertype" style="width:100px;display:inline-block">
			<option value="tianchong" <?php if($wallpaper['wallpapertype'] == 'tianchong'){echo 'selected';} ?>>填充</option>
			<option value="shiying" <?php if($wallpaper['wallpapertype'] == 'shiying'){echo 'selected';} ?>>适应</option>
			<option value="pingpu" <?php if($wallpaper['wallpapertype'] == 'pingpu'){echo 'selected';} ?>>平铺</option>
			<option value="lashen" <?php if($wallpaper['wallpapertype'] == 'lashen'){echo 'selected';} ?>>拉伸</option>
			<option value="juzhong" <?php if($wallpaper['wallpapertype'] == 'juzhong'){echo 'selected';} ?>>居中</option>
		</select>
	</div>
</div>
<div class="wapppapercustom">
	<div class="tip">
		<a href="javascript:;" id="upload" class="btn btn-primary btn-xs fr" style="position:relative">上传壁纸</a>
		<strong>自定义壁纸：</strong>最多上传6张，每张上传的壁纸大小不超过1M
	</div>
	<div class="view">
		<ul>
			<?php
				foreach($db->select('tb_pwallpaper', '*', array(
					'member_id' => session('member_id')
				)) as $v){
					echo '<li id="'.$v['tbid'].'" style="background:url(../../'.getSimgSrc($v['url']).')"><a href="javascript:;">删 除</a></li>';
				}
			?>
		</ul>
	</div>
</div>
<div class="wapppaperwebsite">
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon1">网络壁纸</span>
		<input type="text" class="form-control" id="wallpaperurl" placeholder="请输入一个URL地址" value="<?php echo $wallpaper['wallpaperwebsite']; ?>">
		<span class="input-group-btn">
			<button type="button" class="btn btn-default">应用</button>
		</span>
	</div>
</div>
<?php include('sysapp/global_js.php'); ?>
<script src="../../static/plugins/webuploader-0.1.5/webuploader.min.js"></script>
<script>
$(function(){
	var uploader = WebUploader.create({
		// 选完文件后，是否自动上传。
		auto: true,
		// swf文件路径
		swf: '../../js/webuploader-0.1.5/Uploader.swf',
		// 文件接收服务端。
		server: 'custom.ajax.php?ac=uploadImg',
		// 选择文件的按钮。可选。
		// 内部根据当前运行是创建，可能是input元素，也可能是flash.
		pick: {
			id: '#upload',
			multiple: false
		},
		// 只允许选择图片文件。
		accept: {
			title: 'Images',
			extensions: 'gif,jpg,jpeg,bmp,png',
			mimeTypes: 'image/*'
		}
	});
	uploader.on('beforeFileQueued', function(file){
		if(file.size > 1000 * 1024){
			alert('文件大于1000Kb，请压缩后再上传');
			return false;
		}
	});
	uploader.on('uploadSuccess', function(file, cb){
		$('.wapppapercustom .view ul').append('<li id="'+cb.tbid+'" style="background:url(../../'+cb.surl+')"><a href="javascript:;">删 除</a></li>');
		window.parent.HROS.wallpaper.update(2, $('#wallpapertype').val(), cb.tbid);
		uploader.removeFile(file);
	});
	$('#wallpapertype').on('change', function(){
		window.parent.HROS.wallpaper.update(0, $('#wallpapertype').val(), '');
	});
	$('.wapppapercustom .view').on('click', 'li', function(){
		window.parent.HROS.wallpaper.update(2, $('#wallpapertype').val(), $(this).attr('id'));
	});
	$('.wapppapercustom .view').on('click', 'li a', function(){
		var id = $(this).parent().attr('id');
		$.ajax({
			type : 'POST',
			url : 'custom.ajax.php',
			data : 'ac=del&id=' + id
		}).done(function(msg){
			if(msg != 'ERROR'){
				$('#' + id).remove();
			}else{
				ZENG.msgbox.show('当前壁纸正在使用，删除失败！', 5, 2000);
			}
		});
		return false;
	});
	$('.wapppaperwebsite button').on('click', function(){
		window.parent.HROS.wallpaper.update(3, $('#wallpapertype').val(), $('#wallpaperurl').val());
	});
});
</script>
</body>
</html>
