<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
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
				if(!$db->has('tb_member', array(
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
		case 'avatar':
			$result = array();
			$result['success'] = false;
			$success_num = 0;
			$msg = '';
			//上传目录
			$dir = 'uploads/member/'.session('member_id').'/avatar/';
			recursive_mkdir($dir);
			//处理头像图片
			$result['avatarUrls'][0] = $dir.'120.jpg';
			move_uploaded_file($_FILES['__avatar1']['tmp_name'], $result['avatarUrls'][0]);
			$success_num++;
			$result['avatarUrls'][1] = $dir.'48.jpg';
			move_uploaded_file($_FILES['__avatar2']['tmp_name'], $result['avatarUrls'][1]);
			$success_num++;
			$result['avatarUrls'][2] = $dir.'24.jpg';
			move_uploaded_file($_FILES['__avatar3']['tmp_name'], $result['avatarUrls'][2]);
			$success_num++;
			
			$result['msg'] = $msg;
			if($success_num > 0){
				$result['success'] = true;
				//更新cookie头像
				if(cookie('userinfo') != NULL){
					$userinfo = json_decode(stripslashes(cookie('userinfo')), true);
					$userinfo['avatar'] = getAvatar(session('member_id'), 'l');
					cookie('userinfo', json_encode($userinfo), time() + 3600 * 24 * 7);
				}
			}
			//返回图片的保存结果（返回内容为json字符串）
			echo json_encode($result);
			break;
	}
?>