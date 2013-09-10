<?php
	require('../../global.php');
	
	switch($ac){
		case 'checkPassword':
			$row = $db->select(0, 1, 'tb_member', 'password', 'and tbid = '.session('member_id'));
			if(empty($row)){
				$cb['info'] = '原密码错误';
				$cb['status'] = 'n';
			}else{
				if($row['password'] != sha1($param)){
					$cb['info'] = '原密码错误';
					$cb['status'] = 'n';
				}else{
					$cb['info'] = '';
					$cb['status'] = 'y';
				}
			}
			echo json_encode($cb);
			break;
		case 'editPassword':
			$rs = $db->update(0, 1, 'tb_member', 'password = "'.sha1($password).'"', 'and tbid = '.session('member_id'));
			if($rs){
				$cb['info'] = '';
				$cb['status'] = 'y';
			}else{
				$cb['info'] = '';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		case 'unbind':
			$set = array(
				'openid_'.$fromsite.' = ""',
				'openname_'.$fromsite.' = ""',
				'openavatar_'.$fromsite.' = ""',
				'openurl_'.$fromsite.' = ""'
			);
			$db->update(0, 0, 'tb_member', $set, 'and tbid = '.session('member_id'));
			break;
		//第三方登录
		case '3loginBind':
			//检测所需数据是否存在
			if(cookie('fromsite') && session('?openid') && session('?openname')){
				$row = $db->select(0, 1, 'tb_member', '*', 'and openid_'.cookie('fromsite').' = "'.session('openid').'" and tbid != '.session('member_id'));
				if(empty($row)){
					$set = array(
						'openid_'.cookie('fromsite').' = "'.session('openid').'"',
						'openname_'.cookie('fromsite').' = "'.session('openname').'"',
						'openavatar_'.cookie('fromsite').' = "'.session('openavatar').'"',
						'openurl_'.cookie('fromsite').' = "'.session('openurl').'"'
					);
					$db->update(0, 0, 'tb_member', $set, 'and tbid = '.session('member_id'));
				}else{
					echo 'ERROR_OPENID_IS_USED';
				}
				//清空数据
				cookie('fromsite', NULL);
				session('openid', NULL);
				session('openname', NULL);
				session('openavatar', NULL);
				session('openurl', NULL);
			}else{
				echo 'ERROR_LACK_OF_DATA';
			}
			break;
	}
?>