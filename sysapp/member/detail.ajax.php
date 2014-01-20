<?php
	require('../../global.php');
	
	switch($_POST['ac']){
		case 'edit':
			$val_password = $val_password == '' ? $val_password : sha1($val_password);			
			if($_POST['id'] == ''){
				$data = array(
					'username' => $_POST['val_username'],
					'password' => $_POST['val_password'] == '' ? $_POST['val_password'] : sha1($_POST['val_password']),
					'type' => $_POST['val_type']
				);
				if($_POST['val_type'] == 1){
					$data['permission_id'] = $_POST['val_permission_id'];
				}
				$db->insert('tb_member', $data);
			}else{
				$data = array(
					'type' => $_POST['val_type']
				);
				if($_POST['val_password'] != ''){
					$data['password'] = sha1($_POST['val_password']);
				}
				if($_POST['val_type'] == 1){
					$data['permission_id'] = $_POST['val_permission_id'];
				}else{
					$data['permission_id'] = '';
				}
				$db->update('tb_member', $data, array(
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