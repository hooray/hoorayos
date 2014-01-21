<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		//更新应用评分
		case 'updateAppStar':
			if(!$db->has('tb_app_star', array(
				'AND' => array(
					'app_id' => $_POST['id'],
					'member_id' => session('member_id')
				)
			))){
				$db->insert('tb_app_star', array(
					'app_id' => $_POST['id'],
					'member_id' => session('member_id'),
					'starnum' => $_POST['starnum'],
					'dt' => date('Y-m-d H:i:s')
				));
				$starnumavg = $db->avg('tb_app_star', 'starnum', array(
					'app_id' => $_POST['id']
				));
				$db->update('tb_app', array(
					'starnum' => $starnumavg
				), array(
					'tbid' => $_POST['id']
				));
				echo true;
			}else{
				echo false;
			}
			break;
	}
?>