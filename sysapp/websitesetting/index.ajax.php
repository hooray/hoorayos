<?php
	require('../../global.php');
		
	switch($ac){
		case 'edit':
			$set = array(
				'title = "'.$val_title.'"',
				'keywords = "'.$val_keywords.'"',
				'description = "'.$val_description.'"'
			);
			$db->update(0, 0, 'tb_setting', $set);
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
	}
?>