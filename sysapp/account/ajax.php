<?php
	require('../../global.php');
	
	switch($_POST['ac']){
		case 'checkPassword':
			if($db->has('tb_member', array(
				'AND' => array(
					'tbid' => session('member_id'),
					'password' => sha1($_POST['param'])
				)
			))){
				$cb['info'] = '';
				$cb['status'] = 'y';
			}else{
				$cb['info'] = '原密码错误';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		case 'editPassword':
			$rs = $db->update('tb_member', array(
				'password' => sha1($_POST['password'])
			), array(
				'tbid' => session('member_id')
			));
			if($rs){
				$cb['info'] = '';
				$cb['status'] = 'y';
			}else{
				$cb['info'] = '';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		case 'editLockPassword':
			$rs = $db->update('tb_member', array(
				'lockpassword' => sha1($_POST['lockpassword'])
			), array(
				'tbid' => session('member_id')
			));
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
			$db->update('tb_member', array(
				'openid_'.$_POST['fromsite'] => '',
				'openname_'.$_POST['fromsite'] => '',
				'openavatar_'.$_POST['fromsite'] => '',
				'openurl_'.$_POST['fromsite'] => ''
			), array(
				'tbid' => session('member_id')
			));
			break;
		//第三方登录
		case '3loginBind':
			//检测所需数据是否存在
			if(cookie('fromsite') && session('?openid') && session('?openname')){
				if(!$db->has('tb_member', '*', array(
					'AND' => array(
						'openid_'.cookie('fromsite') => session('openid'),
						'tbid[!]' => session('member_id')
					)
				))){
					$db->update('tb_member', array(
						'openid_'.cookie('fromsite') => session('openid'),
						'openname_'.cookie('fromsite') => session('openname'),
						'openavatar_'.cookie('fromsite') => session('openavatar'),
						'openurl_'.cookie('fromsite') => session('openurl')
					), array(
						'tbid' => session('member_id')
					));
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