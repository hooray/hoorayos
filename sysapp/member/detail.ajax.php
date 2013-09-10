<?php
	require('../../global.php');
	
	switch($ac){
		case 'edit':
			$val_password = $val_password == '' ? $val_password : sha1($val_password);			
			if($id == ''){
				$set = array(
					"username = '$val_username'",
					"password = '$val_password'",
					"type = $val_type"
				);
				if($val_type == 1){
					$set[] = "permission_id = '$val_permission_id'";
				}
				$db->insert(0, 0, 'tb_member', $set);
			}else{
				$set = array("type = $val_type");
				if($val_password != ''){
					$set[] = "password = '$val_password'";
				}
				if($val_type == 1){
					$set[] = "permission_id = '$val_permission_id'";
				}else{
					$set[] = "permission_id = ''";
				}
				$db->update(0, 0, 'tb_member', $set, "and tbid = $id");
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
	}
?>