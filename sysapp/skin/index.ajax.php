<?php
	require('../../global.php');
	
	switch($ac){
		case 'update':
			$db->update(0, 0, 'tb_member', "skin = '$skin'", 'and tbid = '.session('member_id'));
			break;
	}
?>