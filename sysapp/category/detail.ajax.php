<?php
	require('../../global.php');
	
	switch($ac){
		case 'edit':
			$set = array(
				'name = "'.$val_name.'"'
			);
			if($id == ''){
				$db->insert(0, 0, 'tb_app_category', $set);
			}else{
				$db->update(0, 0, 'tb_app_category', $set, 'and tbid = '.(int)$id);
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
	}
?>