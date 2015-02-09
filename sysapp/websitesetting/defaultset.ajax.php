<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'edit':
			$db->update('tb_setting', array(
				'isforcedlogin' => $_POST['val_isforcedlogin'],
				'dock' => $_POST['val_dock'],
				'desk1' => $_POST['val_desk1'],
				'desk2' => $_POST['val_desk2'],
				'desk3' => $_POST['val_desk3'],
				'desk4' => $_POST['val_desk4'],
				'desk5' => $_POST['val_desk5'],
				'desk' => $_POST['val_desk'],
				'appxy' => $_POST['val_appxy'],
				'appsize' => $_POST['val_appsize'],
				'appverticalspacing' => $_POST['val_appverticalspacing'],
				'apphorizontalspacing' => $_POST['val_apphorizontalspacing'],
				'dockpos' => $_POST['val_dockpos'],
				'skin' => $_POST['val_skin'],
				'wallpaper_id' => $_POST['val_wallpaper_id'],
				'wallpapertype' => $_POST['val_wallpapertype']
			));
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'updateApps':
			foreach($db->select('tb_app', array('tbid', 'name', 'icon'), array(
				'tbid' => explode(',', $_POST['appsid'])
			)) as $v){
				echo '<div class="app" appid="'.$v['tbid'].'"><img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'"><span class="del">删</span></div>';
			}
			break;
	}
?>