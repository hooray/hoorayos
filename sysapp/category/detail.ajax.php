<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'edit':
			$data = array(
				'name' => $_POST['val_name']
			);
			if($_POST['id'] == ''){
				$db->insert('tb_app_category', $data);
			}else{
				$db->update('tb_app_category', $data, array(
					'tbid' => $_POST['id']
				));
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
	}
?>