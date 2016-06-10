<?php
	require('../../global.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>添加应用</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<ul class="nav nav-tabs" style="margin-top:5px;margin-bottom:0">
	<?php
		$category = $db->select('tb_app_category', '*', array(
			'issystem' => 0
		));
		if($category != NULL){
			foreach($category as $c){
				echo '<li class="dropdown"><a href="#category_'.$c['tbid'].'" data-toggle="tab">'.$c['name'].'</a></li>';
			}
		}
	?>
</ul>
<div class="tab-content">
	<?php
		if($category != NULL){
			foreach($category as $c){
				echo '<div class="alert_addapps tab-pane" id="category_'.$c['tbid'].'">';
				foreach($db->select('tb_app', array('tbid', 'name', 'icon'), array(
					'AND' => array(
						'app_category_id' => $c['tbid'],
						'verifytype' => 1
					)
				)) as $v){
					echo '<div class="app" title="'.$v['name'].'" appid="'.$v['tbid'].'">';
						echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
						echo '<div class="name">'.$v['name'].'</div>';
						echo '<span class="selected"></span>';
					echo '</div>';
				}
				echo '</div>';
			}
		}
	?>
</div>
<input type="hidden" id="value_1">
<?php include('sysapp/global_js.php'); ?>
<script>
$(function(){
	$('.nav-tabs a:first').tab('show');
	var dialog = window.parent.dialog.get(window);
	if(dialog.data.appsid != ''){
		$('#value_1').val(dialog.data.appsid);
		var appsid = dialog.data.appsid.split(',');
		$('.app').each(function(){
			for(var i=0; i<appsid.length; i++){
				if(appsid[i] == $(this).attr('appid')){
					$(this).addClass('act');
					break;
				}
			}
		});
	}
	$('.app').click(function(){
		if($(this).hasClass('act')){
			var appsid = $('#value_1').val().split(',');
			var newappsid = [];
			for(var i=0, j=0; i<appsid.length; i++){
				if(appsid[i] != $(this).attr('appid')){
					newappsid[j] = appsid[i];
					j++;
				}
			}
			$('#value_1').val(newappsid.join(','));
			$(this).removeClass('act');
		}else{
			if($('#value_1').val() != ''){
				var appsid = $('#value_1').val().split(',');
				appsid[appsid.length] = $(this).attr('appid');
				$('#value_1').val(appsid.join(','));
			}else{
				$('#value_1').val($(this).attr('appid'));
			}
			$(this).addClass('act');
		}
		dialog.data.appsid = $('#value_1').val();
	});
});
</script>
</body>
</html>
