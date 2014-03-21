<?php
	require('global.php');
	
	$rs = $db->get('tb_member_app', '*', array(
		'AND' => array(
			'tbid' => $_GET['appid'],
			'member_id' => session('member_id')
		)
	));
	file_download($rs['url'], $rs['name'].'.'.$rs['ext']);
?>