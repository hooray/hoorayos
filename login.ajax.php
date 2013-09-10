<?php
	require('global.php');
		
	switch($ac){
		//登录
		case 'login':
			$rememberMe = isset($rememberMe) ? 1 : 0;
			$sqlwhere = array(
				'username = "'.$username.'"',
				'password = "'.sha1($password).'"'
			);
			$row = $db->select(0, 1, 'tb_member', '*', $sqlwhere);
			if(!empty($row)){
				$db->update(0, 0, 'tb_member', 'lastlogindt = thislogindt, lastloginip = thisloginip, thislogindt = now(), thisloginip = "'.getIp().'"', 'and tbid = '.$row['tbid']);
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
						$set = array(
							'openid_'.cookie('fromsite').' = "'.session('openid').'"',
							'openname_'.cookie('fromsite').' = "'.session('openname').'"',
							'openavatar_'.cookie('fromsite').' = "'.session('openavatar').'"',
							'openurl_'.cookie('fromsite').' = "'.session('openurl').'"'
						);
						$db->update(0, 0, 'tb_member', $set, 'and tbid = '.$row['tbid']);
						cookie('fromsite', NULL);
						session('openid', NULL);
						session('openname', NULL);
						session('openavatar', NULL);
						session('openurl', NULL);
					}
				}
				//处理登录用户信息到cookie
				$userinfo = array();
				$userinfo['username'] = $username;
				$userinfo['password'] = $rememberMe ? authcode($password, 'ENCODE') : '';
				$userinfo['rememberMe'] = $rememberMe;
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
				$row = $db->select(0, 1, 'tb_member', '*', 'and openid_'.cookie('fromsite').' = "'.session('openid').'"');
				if(!empty($row)){
					$db->update(0, 0, 'tb_member', 'lastlogindt = thislogindt, lastloginip = thisloginip, thislogindt = now(), thisloginip = "'.getIp().'"', 'and tbid = '.$row['tbid']);
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
			$isreg = $db->select(0, 1, 'tb_member', 'tbid', 'and username = "'.$reg_username.'"');
			if(empty($isreg)){
				$set = array(
					'username = "'.$reg_username.'"',
					'password = "'.sha1($reg_password).'"',
					'thislogindt = now()',
					'thisloginip = "'.getIp().'"',
					'regdt = now()'
				);
				$db->insert(0, 0, 'tb_member', $set);
				$cb['info'] = $reg_username;
				$cb['status'] = 'y';
			}else{
				$cb['info'] = '';
				$cb['status'] = 'n';
			}
			echo json_encode($cb);
			break;
		case 'checkUsername':
			$isreg = $db->select(0, 1, 'tb_member', 'tbid', 'and username = "'.$param.'"');
			if(empty($isreg)){
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
	}
?>