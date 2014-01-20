<?php
	require('../../global.php');
		
	switch($_POST['ac']){
		case 'edit':
			$db->update('tb_setting', array(
				'title' => $_POST['val_title'],
				'keywords' => $_POST['val_keywords'],
				'description' => $_POST['val_description']
			));
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
	}
?>