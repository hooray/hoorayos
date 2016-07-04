<?php
	require('../../global.php');

	switch($_REQUEST['ac']){
		case 'edit':
			$data = array(
				'name' => $_POST['val_name'],
				'apps_id' => $_POST['val_apps_id']
			);
			if($_POST['id'] == ''){
				$db->insert('tb_permission', $data);
			}else{
				$db->update('tb_permission', $data, array(
					'tbid' => $_POST['id']
				));
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'updateApps':
			foreach($db->select('tb_app', '*', array(
				'tbid' => explode(',', $_POST['appsid'])
			)) as $a){
				echo '<div class="app" appid="'.$a['tbid'].'"><img src="../../'.$a['icon'].'" alt="'.$a['name'].'" title="'.$a['name'].'"><span class="del">删</span></div>';
			}
			break;
	}
?>
