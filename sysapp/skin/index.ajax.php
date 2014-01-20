<?php
	require('../../global.php');
	
	switch($_POST['ac']){
		case 'update':
			$db->update('tb_member', array(
				'skin' => $_POST['skin']
			), array(
				'tbid' => session('member_id')
			));
			break;
	}
?>