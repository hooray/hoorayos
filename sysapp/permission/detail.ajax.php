<?php
	require('../../global.php');
	
	switch($ac){
		case 'edit':
			$set = array(
				"name = '$val_name'",
				"apps_id = '$val_apps_id'"
			);
			if($id == ''){
				$db->insert(0, 0, 'tb_permission', $set);
			}else{
				$db->update(0, 0, 'tb_permission', $set, 'and tbid = '.$id);
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'updateApps':
			$appsrs = $db->select(0, 0, 'tb_app', 'tbid, name, icon', 'and tbid in ('.$appsid.')');
			foreach($appsrs as $a){
				echo '<div class="app" appid="'.$a['tbid'].'"><img src="../../'.$a['icon'].'" alt="'.$a['name'].'" title="'.$a['icon'].'"><span class="del">删</span></div>';
			}
			break;
	}
?>