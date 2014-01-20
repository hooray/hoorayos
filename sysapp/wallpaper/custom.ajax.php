<?php
	require('../../global.php');
	
	switch($_POST['ac']){
		case 'del':
			$db->delete('tb_pwallpaper', array(
				'AND' => array(
					'tbid' => $_POST['id'],
					'member_id' => session('member_id')
				)
			));
			break;
	}
?>