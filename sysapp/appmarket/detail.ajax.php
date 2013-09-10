<?php
	require('../../global.php');
	
	switch($ac){
		//更新应用评分
		case 'updateAppStar':
			$isscore = $db->select(0, 2, 'tb_app_star', 'tbid', 'and app_id = '.$id.' and member_id = '.session('member_id'));
			if($isscore == 0){
				$set = array(
					'app_id = '.$id,
					'member_id = '.session('member_id'),
					'starnum = '.$starnum,
					'dt = now()'
				);
				$db->insert(0, 0, 'tb_app_star', $set);
				$scoreavg = $db->select(0, 1, 'tb_app_star', 'avg(starnum) as starnum', 'and app_id = '.$id);
				$db->update(0, 0, 'tb_app', 'starnum = "'.$scoreavg['starnum'].'"', 'and tbid = '.$id);
				echo true;
			}else{
				echo false;
			}
			break;
	}
?>