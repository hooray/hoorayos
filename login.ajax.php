<?php
	require('global.php');
		
	switch($_POST['ac']){
		//登录
		case 'login':
			$rememberMe = isset($_POST['rememberMe']) ? 1 : 0;
			$row = $db->get('tb_member', '*', array(
				'AND' => array(
					'username' => $_POST['username'],
					'password' => sha1($_POST['password'])
				)
			));
			if($row){
				$db->update('tb_member', array(
					'lastlogindt' => $db->get('tb_member', 'thislogindt', array(
						'tbid' => $row['tbid']
					)),
					'lastloginip' => $db->get('tb_member', 'thisloginip', array(
						'tbid' => $row['tbid']
					)),
					'thislogindt' => date('Y-m-d H:i:s'),
					'thisloginip' => getIp()
				), array(
					'tbid' => $row['tbid']
				));
				session('member_id', $row['tbid']);
				cookie('memberID', $row['tbid'], 3600 * 24 * 7);
				$cb['info'] = '';
				$cb['status'] = 'y';
				//判断是否为第三方登录
				if(cookie('fromsite') && session('?openid') && session('?openname')){
					if($row['openid_'.cookie('fromsite')] != ''){
						$cb['info'] = 'ERROR_OPENID_IS_USED';
						$cb['status'] = 'n';
					}else{
						$db->update(0, 0, 'tb_member', array(
							'openid_'.cookie('fromsite') => session('openid'),
							'openname_'.cookie('fromsite') => session('openname'),
							'openavatar_'.cookie('fromsite') => session('openavatar'),
							'openurl_'.cookie('fromsite') => session('openurl')
						), array(
							'tbid' => $row['tbid']
						));
						cookie('fromsite', NULL);
						session('openid', NULL);
						session('openname', NULL);
						session('openavatar', NULL);
						session('openurl', NULL);
					}
				}
				//处理登录用户信息到cookie
				$userinfo = array();
				$userinfo['username'] = $_POST['username'];
				$userinfo['password'] = $_POST['rememberMe'] ? authcode($_POST['password'], 'ENCODE') : '';
				$userinfo['rememberMe'] = $_POST['rememberMe'];
				$userinfo['avatar'] = getAvatar($row['tbid'], 'l');
				cookie('userinfo', json_encode($userinfo), 3600 * 24 * 7);
			}else{
				$cb['info'] = '';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		//第三方登录
		case '3login':
			//检测所需数据是否存在
			if(cookie('fromsite') && session('?openid') && session('?openname')){
				$row = $db->get('tb_member', '*', array(
					'openid_'.cookie('fromsite') => session('openid')
				));
				if($row){
					$db->update('tb_member', array(
						'lastlogindt' => $db->get('tb_member', 'thislogindt', array(
							'tbid' => $row['tbid']
						)),
						'lastloginip' => $db->get('tb_member', 'thisloginip', array(
							'tbid' => $row['tbid']
						)),
						'thislogindt' => date('Y-m-d H:i:s'),
						'thisloginip' => getIp()
					), array(
						'tbid' => $row['tbid']
					));
					session('member_id', $row['tbid']);
					cookie('memberID', $row['tbid'], 3600 * 24 * 7);
					//清空数据
					cookie('fromsite', NULL);
					session('openid', NULL);
					session('openname', NULL);
					session('openavatar', NULL);
					session('openurl', NULL);
				}else{
					echo 'ERROR_NOT_BIND';
				}
			}else{
				echo 'ERROR_LACK_OF_DATA';
			}
			break;
		//注册
		case 'register':
			if(!$db->has('tb_member', array(
				'username' => $_POST['reg_username']
			))){
				$db->insert('tb_member', array(
					'username' => $_POST['reg_username'],
					'password' => sha1($_POST['reg_password']),
					'lockpassword' => sha1($_POST['reg_password']),
					'thislogindt' => date('Y-m-d H:i:s'),
					'thisloginip' => getIp(),
					'regdt' => date('Y-m-d H:i:s')
				));
				$cb['info'] = $_POST['reg_username'];
				$cb['status'] = 'y';
			}else{
				$cb['info'] = '';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		case 'checkUsername':
			if(!$db->has('tb_member', array(
				'username' => $_POST['param']
			))){
				$cb['info'] = '';
				$cb['status'] = 'y';
			}else{
				$cb['info'] = '用户名已存在，请更换';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		//登出
		case 'logout':
			session('member_id', NULL);
			cookie('memberID', NULL);
			//手动退出，取消userinfo cookie里的自动登录选项
			$userinfo = json_decode(stripslashes(cookie('userinfo')), true);
			$userinfo['rememberMe'] = 0;
			$userinfo['password'] = '';
			cookie('userinfo', json_encode($userinfo));
			break;
		//解锁登录
		case 'unlock':
			$userinfo = json_decode(stripslashes(cookie('userinfo')), true);
			$row = $db->get('tb_member', '*', array(
				'username' => $userinfo['username'],
				'lockpassword' => sha1($_POST['password'])
			));
			if($row){
				session('member_id', $row['tbid']);
				cookie('memberID', $row['tbid'], 3600 * 24 * 7);
			}else{
				echo 'ERROR_LOCKPASSWORD';
			}
			break;
	}
?>