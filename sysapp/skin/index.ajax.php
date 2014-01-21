<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'update':
			$db->update('tb_member', array(
				'skin' => $_POST['skin']
			), array(
				'tbid' => session('member_id')
			));
			break;
	}
?>