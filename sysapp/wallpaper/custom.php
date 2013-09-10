<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	
	$wallpaper = $db->select(0, 1, 'tb_member', 'wallpapertype,wallpaperwebsite', 'and tbid = '.session('member_id'));
	$wallpaperList = $db->select(0, 0, 'tb_pwallpaper', '*', 'and member_id = '.session('member_id'));
	foreach($wallpaperList as &$value){
		$value['surl'] = getSimgSrc($value['url']);
	}
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
		<a class="btn" href="index.php">系统壁纸</a><a class="btn disabled">自定义</a>
	</div>
	<div class="fr">
		<label>显示方式：</label>
		<select name="wallpapertype" id="wallpapertype" style="width:100px">
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
		<a class="btn btn-mini fr" style="overflow:hidden;position:relative">上传壁纸<input type="file" id="uploadfilebtn" style="position:absolute;right:0;bottom:0;opacity:0;filter:alpha(opacity=0);display:block;width:200px;height:100px"></a>
		<strong>自定义壁纸：</strong>最多上传6张，每张上传的壁纸大小不超过1M
	</div>
	<div class="view">
		<ul>
			<?php
				foreach($wallpaperList as $v){
					echo '<li id="'.$v['tbid'].'" style="background:url(../../'.$v['surl'].')"><a href="javascript:;">删 除</a></li>';
				}
			?>
		</ul>
	</div>
</div>
<div class="wapppaperwebsite form-inline">
	<label>网络壁纸：</label>
	<div class="input-append">
		<input type="text" id="wallpaperurl" style="width:350px" placeholder="请输入一个URL地址（地址以 jpg, jpeg, png, gif, html, htm 结尾）" value="<?php echo $wallpaper['wallpaperwebsite']; ?>"><button type="button" class="btn">应用</button>
	</div>
</div>
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
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
			data : 'ac=del&id=' + id,
			success : function(){
				$('#' + id).remove();
			}
		});
		return false;
	});
	$('.wapppaperwebsite button').on('click', function(){
		window.parent.HROS.wallpaper.update(3, $('#wallpapertype').val(), $('#wallpaperurl').val());
	});
	$('#uploadfilebtn').on('change', function(e){
		var files = e.target.files || e.dataTransfer.files;
		if(files.length == 0){
			return;
		}
		//检测文件是不是图片
		if(files[0].type.indexOf('image') === -1){
			alert('请上传图片');
			return false;
		}
		//检测文件大小是否超过1M
		if(files[0].size > 1024*1024){
			alert('图片大小超过1M');
			return;
		}
		var fd = new FormData();
		fd.append('xfile', files[0]);
		var xhr = new XMLHttpRequest();
		if(xhr.upload){
			$.dialog({
				id: 'uploadImg',
				title: '正在上传',
				content: '<div id="imgProgress" class="progress progress-striped active" style="width:200px;margin-bottom:0"><div class="bar"></div></div>',
				cancel: false
			});
			xhr.upload.addEventListener('progress', function(e){
				if(e.lengthComputable){
					var loaded = Math.ceil(e.loaded / e.total * 100);
					$('#imgProgress .bar').css({
						width: loaded + '%'
					});
				}
			}, false);
			xhr.addEventListener('load', function(e){
				$('#uploadfilebtn').val('');
				$.dialog.list['uploadImg'].close();
				if(xhr.readyState == 4 && xhr.status == 200){
					var result = jQuery.parseJSON(e.target.responseText);
					if(result.state == 'SUCCESS'){
						$('.wapppapercustom .view ul').append('<li id="'+result.tbid+'" style="background:url(../../'+result.surl+')"><a href="javascript:;">删 除</a></li>');
						window.parent.HROS.wallpaper.update(2, $('#wallpapertype').val(), result.tbid);
					}else{
						ZENG.msgbox.show(result.state, 5, 2000);
					}
				}
			}, false);
			xhr.open('post', 'custom.ajax.php?ac=uploadImg', true);
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.send(fd);
		}
	});
});
</script>
</body>
</html>