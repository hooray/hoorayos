<?php
	require('global.php');
	
	//所有操作分两种类型：读和写
	//如 getWallpaper 是读操作，setWallpaper 是写操作
	//当遇到写操作时，则需要通过下面登录验证，未登录用户则中断之后的写操作，并输出错误信息
	if(in_array($_REQUEST['ac'], array('setWallpaper', 'setDockPos', 'setAppXY', 'addMyApp', 'delMyApp', 'moveMyApp', 'updateMyApp', 'addFolder', 'updateFolder', 'updateAppStar'))){
		if(!checkLogin()){
			exit('ERROR_NOT_LOGGED_IN');
		}
	}
		
	switch($_REQUEST['ac']){
		case 'checkLogin':
			echo checkLogin() ? 1 : 0;
			break;
		//获取头像
		case 'getAvatar':
			echo getAvatar(checkLogin() ? session('member_id') : 0);
			break;
		//获取主题
		case 'getWallpaper':
			echo getWallpaper();
			break;
		//更新主题
		case 'setWallpaper':
			$data = array(
				'wallpaperstate' => $_POST['wpstate'],
				'wallpapertype' => $_POST['wptype']
			);
			switch($_POST['wpstate']){
				case '0':
					$data = array(
						'wallpapertype' => $_POST['wptype']
					);
					break;
				case '1':
				case '2':
					if($_POST['wp'] != '' && $_POST['wp'] != 0){
						$data['wallpaper_id'] = $_POST['wp'];
					}					
					break;
				case '3':
					if($_POST['wp'] != ''){
						$data['wallpaperwebsite'] = $_POST['wp'];
					}
					break;
			}
			$db->update('tb_member', $data, array(
				'tbid' => session('member_id')
			));
			break;
		//更新应用码头位置
		case 'setDockPos':
			$db->update('tb_member', array(
				'dockpos' => $_POST['dock']
			), array(
				'tbid' => session('member_id')
			));
			if($_POST['dock'] == 'none'){
				$rs = $db->get('tb_member', array('dock', 'desk'.$_POST['desk']), array(
					'tbid' => session('member_id')
				));
				$dock_arr = $rs['dock'];
				$desk_arr = $rs['desk'.$_POST['desk']];
				if($dock_arr != ''){
					if($desk_arr == ''){
						$desk_arr = $dock_arr;
					}else{
						$desk_arr = $desk_arr.','.$dock_arr;
					}
					$db->update('tb_member', array(
						'dock' => '',
						'desk'.$_POST['desk'] => $desk_arr
					), array(
						'tbid' => session('member_id')
					));
				}
			}
			break;
		//更新图标排列方式
		case 'setAppXY':
			$db->update('tb_member', array(
				'appxy' => $_POST['appxy']
			), array(
				'tbid' => session('member_id')
			));
			break;
		//更新图标显示尺寸
		case 'setAppSize':
			$db->update('tb_member', array(
				'appsize' => $_POST['appsize']
			), array(
				'tbid' => session('member_id')
			));
			break;
		//更新默认桌面
		case 'setDesk':
			if($desk != '' && $_POST['desk'] >= 1 && $_POST['desk'] <= 5){
				$db->update('tb_member', array(
					'desk' => $_POST['desk']
				), array(
					'tbid' => session('member_id')
				));
			}
			break;
		//获取桌面图标
		case 'getMyApp':
			if(checkLogin()){
				$appid = $db->get('tb_member', array('dock', 'desk1', 'desk2', 'desk3', 'desk4', 'desk5'), array(
					'tbid' => session('member_id')
				));
				if($appid['dock'] != ''){
					$rs = $db->query('SELECT * FROM `tb_member_app` WHERE `tbid` IN ('.$appid['dock'].') ORDER BY FIELD (`tbid`, '.$appid['dock'].')')->fetchAll();
					$data = array();
					foreach($rs as $v){
						$tmp = array();
						if($v['type'] == 'folder'){
							$folderid[] = $v['tbid'];
						}
						$tmp['type'] = $v['type'];
						$tmp['appid'] = $v['tbid'];
						$tmp['realappid'] = $v['realid'];
						$tmp['name'] = $v['name'];
						$tmp['icon'] = $v['icon'];
						$data[] = $tmp;
					}
					$desktop['dock'] = $data;
				}
				for($i = 1; $i <= 5; $i++){
					if($appid['desk'.$i] != ''){
						$rs = $db->query('SELECT * FROM `tb_member_app` WHERE `tbid` IN ('.$appid['desk'.$i].') ORDER BY FIELD (`tbid`, '.$appid['desk'.$i].')')->fetchAll();
						$data = array();
						foreach($rs as $v){
							$tmp = array();
							if($v['type'] == 'folder'){
								$folderid[] = $v['tbid'];
							}
							$tmp['type'] = $v['type'];
							$tmp['appid'] = $v['tbid'];
							$tmp['realappid'] = $v['realid'];
							$tmp['name'] = $v['name'];
							$tmp['icon'] = $v['icon'];
							$data[] = $tmp;
						}
						$desktop['desk'.$i] = $data;
					}
				}
				if($folderid != NULL){
					$data = array();
					foreach($folderid as $v){
						$tmp = array();
						$tmp['appid'] = $v;
						$tmp['apps'] = array();
						$folderapps = $db->select('tb_member_app', '*', array(
							'AND' => array(
								'folder_id' => $v,
								'member_id' => session('member_id')
							)
						));
						foreach($folderapps as $vv){
							$tmpp = array();
							$tmpp['type'] = $vv['type'];
							$tmpp['appid'] = $vv['tbid'];
							$tmpp['realappid'] = $vv['realid'];
							$tmpp['name'] = $vv['name'];
							$tmpp['icon'] = $vv['icon'];
							$tmp['apps'][] = $tmpp;
						}
						$data[] = $tmp;
					}
					$desktop['folder'] = $data;
				}
			}else{
				$appid = $db->get('tb_setting', array('dock', 'desk1', 'desk2', 'desk3', 'desk4', 'desk5'));
				if($appid['dock'] != ''){
					$rs = $db->query('SELECT * FROM `tb_app` WHERE `tbid` IN ('.$appid['dock'].') ORDER BY FIELD (`tbid`, '.$appid['dock'].')')->fetchAll();
					$data = array();
					foreach($rs as $v){
						$tmp = array();
						$tmp['type'] = $v['type'];
						$tmp['appid'] = $v['tbid'];
						$tmp['realappid'] = $v['tbid'];
						$tmp['name'] = $v['name'];
						$tmp['icon'] = $v['icon'];
						$data[] = $tmp;
					}
					$desktop['dock'] = $data;
				}
				for($i = 1; $i <= 5; $i++){
					if($appid['desk'.$i] != ''){
						$rs = $db->query('SELECT * FROM `tb_app` WHERE `tbid` IN ('.$appid['desk'.$i].') ORDER BY FIELD (`tbid`, '.$appid['desk'.$i].')')->fetchAll();
						$data = array();
						foreach($rs as $v){
							$tmp = array();
							$tmp['type'] = $v['type'];
							$tmp['appid'] = $v['tbid'];
							$tmp['realappid'] = $v['tbid'];
							$tmp['name'] = $v['name'];
							$tmp['icon'] = $v['icon'];
							$data[] = $tmp;
						}
						$desktop['desk'.$i] = $data;
					}
				}
			}
			echo json_encode($desktop);
			break;
		//根据id获取图标
		case 'getMyAppById':
			$app = array();
			if(checkLogin()){
				switch($_POST['type']){
					case 'window':
					case 'widget':
						$rs = $db->get('tb_member_app', '*', array(
							'AND' => array(
								'realid' => $_POST['id'],
								'member_id' => session('member_id')
							)
						));
						if($rs){
							if(!$db->has('tb_app', array(
								'tbid' => $rs['realid'])
							)){
								$app['error'] = 'ERROR_NOT_FOUND';
							}
							$app['type'] = $rs['type'];
							$app['appid'] = $rs['tbid'];
							$app['realappid'] = $rs['realid'];
							$app['name'] = $rs['name'];
							$app['icon'] = $rs['icon'];
							$app['width'] = $rs['width'];
							$app['height'] = $rs['height'];
							$app['isresize'] = $rs['isresize'];
							$app['isopenmax'] = $rs['isopenmax'];
							$app['issetbar'] = $rs['issetbar'];
							$app['isflash'] = $rs['isflash'];
							if($rs['type'] == 'window' || $rs['type'] == 'widget'){
								$app['url'] = $db->get('tb_app', 'url', array(
									'tbid' => $rs['realid']
								));
							}else{
								$app['url'] = $rs['url'];
							}
						}else{
							$app['error'] = 'ERROR_NOT_INSTALLED';
						}
						break;
					case 'pwindow':
					case 'pwidget':
					case 'folder':
						$rs = $db->get('tb_member_app', '*', array(
							'AND' => array(
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							)
						));
						if($rs){
							$app['type'] = $rs['type'];
							$app['appid'] = $rs['tbid'];
							$app['realappid'] = $rs['tbid'];
							$app['name'] = $rs['name'];
							$app['icon'] = $rs['icon'];
							$app['width'] = $rs['width'];
							$app['height'] = $rs['height'];
							$app['isresize'] = $rs['isresize'];
							$app['isopenmax'] = $rs['isopenmax'];
							$app['issetbar'] = $rs['issetbar'];
							$app['isflash'] = $rs['isflash'];
							$app['url'] = $rs['url'];
						}else{
							$app['error'] = 'ERROR_NOT_FOUND';
						}
						break;
				}
			}else{
				$rs = $db->query('SELECT GROUP_CONCAT(dock, \',\', desk1, \',\', desk2, \',\', desk3, \',\', desk4, \',\', desk5) as appid from tb_setting')->fetch();
				$appid = formatAppidArray(explode(',', $rs['appid']));
				if(in_array($_POST['id'], $appid)){
					$rs = $db->get('tb_app', '*', array(
						'tbid' => $_POST['id']
					));
					$app['type'] = $rs['type'];
					$app['appid'] = $rs['tbid'];
					$app['realappid'] = $rs['tbid'];
					$app['name'] = $rs['name'];
					$app['icon'] = $rs['icon'];
					$app['width'] = $rs['width'];
					$app['height'] = $rs['height'];
					$app['isresize'] = $rs['isresize'];
					$app['isopenmax'] = $rs['isopenmax'];
					$app['issetbar'] = $rs['issetbar'];
					$app['isflash'] = $rs['isflash'];
					$app['url'] = $rs['url'];
				}else{
					$app['error'] = 'ERROR_NOT_FOUND';
				}
			}
			echo json_encode($app);
			break;
		//添加桌面图标
		case 'addMyApp':
			addApp(array(
				'type' => '',
				'id' => $_POST['id'],
				'desk' => $_POST['desk']
			));
			break;
		//删除桌面图标
		case 'delMyApp':
			delApp($_POST['id']);
			break;
		//更新桌面图标
		case 'moveMyApp':
			switch($_POST['movetype']){
				case 'dock-folder':
					$dock = $db->get('tb_member', 'dock', array(
						'tbid' => session('member_id')
					));
					$dock_arr = explode(',', $dock);
					$key = array_search($_POST['id'], $dock_arr);
					unset($dock_arr[$key]);
					$db->update('tb_member', array(
						'dock' => implode(',', $dock_arr)
					), array(
						'tbid' => session('member_id')
					));
					$db->update('tb_member_app', array(
						'folder_id' => $_POST['to']
					), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
				case 'dock-dock':
					$dock = $db->get('tb_member', 'dock', array(
						'tbid' => session('member_id')
					));
					$dock_arr = explode(',', $dock);
					//判断传入的应用id和数据库里的id是否吻合
					if($dock_arr[$_POST['from']] == $_POST['id']){
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($dock_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($dock_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($dock_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($dock_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
						if($_POST['from'] > $_POST['to']){
							unset($dock_arr[$_POST['from'] + 1]);
						}else{
							unset($dock_arr[$_POST['from']]);
						}
						$dock_arr = formatAppidArray($dock_arr);
						$db->update('tb_member', array(
							'dock' => implode(',', $dock_arr)
						), array(
							'tbid' => session('member_id')
						));
					}
					break;
				case 'dock-desk':
					$rs = $db->get('tb_member', array('dock', 'desk'.$_POST['desk']), array(
						'tbid' => session('member_id')
					));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$_POST['desk']]);
					unset($dock_arr[$_POST['from']]);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $_POST['id'];
					}else{
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($desk_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($desk_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($desk_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($desk_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
					}
					$db->update('tb_member', array(
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					), array(
						'tbid' => session('member_id')
					));
					break;
				case 'dock-otherdesk':
					$rs = $db->get('tb_member', array('dock', 'desk'.$_POST['todesk']), array(
						'tbid' => session('member_id')
					));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$_POST['todesk']]);
					unset($dock_arr[$_POST['from']]);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $_POST['id'];
					}else{
						$desk_arr[] = $_POST['id'];
					}
					$db->update('tb_member', array(
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['todesk'] => implode(',', $desk_arr)
					), array(
						'tbid' => session('member_id')
					));
					break;
				case 'desk-folder':
					$desk = $db->get('tb_member', 'desk'.$_POST['desk'], array(
						'tbid' => session('member_id')
					));
					$desk_arr = explode(',', $desk);
					$key = array_search($_POST['id'], $desk_arr);
					unset($desk_arr[$key]);
					$db->update('tb_member', array(
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					), array(
						'tbid' => session('member_id')
					));
					$db->update('tb_member_app', array(
						'folder_id' => $_POST['to']
					), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
				case 'desk-dock':
					$rs = $db->get('tb_member', array('dock', 'desk'.$_POST['desk']), array(
						'tbid' => session('member_id')
					));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$_POST['desk']]);
					unset($desk_arr[$_POST['from']]);
					if($dock_arr[0] == ''){
						$dock_arr[0] = $_POST['id'];
					}else{
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($dock_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($dock_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($dock_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($dock_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
					}
					if(count($dock_arr) > 7){
						$desk_arr[] = $dock_arr[7];
						unset($dock_arr[7]);
					}
					$db->update('tb_member', array(
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					), array(
						'tbid' => session('member_id')
					));
					break;
				case 'desk-desk':
					$desk = $db->get('tb_member', 'desk'.$_POST['desk'], array(
						'tbid' => session('member_id')
					));
					$desk_arr = explode(',', $desk);
					//判断传入的应用id和数据库里的id是否吻合
					if($desk_arr[$_POST['from']] == $_POST['id']){
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($desk_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($desk_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($desk_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($desk_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
						if($_POST['from'] > $_POST['to']){
							unset($desk_arr[$_POST['from'] + 1]);
						}else{
							unset($desk_arr[$_POST['from']]);
						}
						$desk_arr = formatAppidArray($desk_arr);
						$db->update('tb_member', array(
							'desk'.$_POST['desk'] => implode(',', $desk_arr)
						), array(
							'tbid' => session('member_id')
						));
					}
					break;
				case 'desk-otherdesk':
					$rs = $db->get('tb_member', array('desk'.$_POST['fromdesk'], 'desk'.$_POST['todesk']), array(
						'tbid' => session('member_id')
					));
					$fromdesk_arr = explode(',', $rs['desk'.$_POST['fromdesk']]);
					$todesk_arr = explode(',', $rs['desk'.$_POST['todesk']]);
					unset($fromdesk_arr[$_POST['from']]);
					if($todesk_arr[0] == ''){
						$todesk_arr[0] = $_POST['id'];
					}else{
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($todesk_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($todesk_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($todesk_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($todesk_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
					}
					$db->update('tb_member', array(
						'desk'.$_POST['fromdesk'] => implode(',', $fromdesk_arr),
						'desk'.$_POST['todesk'] => implode(',', $todesk_arr)
					), array(
						'tbid' => session('member_id')
					));
					break;
				case 'folder-folder':
					$db->update('tb_member_app', array(
						'folder_id' => $_POST['to']
					), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
				case 'folder-dock':
					$rs = $db->get('tb_member', array('dock', 'desk'.$_POST['desk']), array(
						'tbid' => session('member_id')
					));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$_POST['desk']]);
					if($dock_arr[0] == ''){
						$dock_arr[0] = $_POST['id'];
					}else{
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($dock_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($dock_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($dock_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($dock_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
					}
					if(count($dock_arr) > 7){
						$desk_arr[] = $dock_arr[7];
						unset($dock_arr[7]);
					}
					$db->update('tb_member', array(
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					), array(
						'tbid' => session('member_id')
					));
					$db->update('tb_member_app', array(
						'folder_id' => 0
					), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
				case 'folder-desk':
					$desk = $db->get('tb_member', 'desk'.$_POST['desk'], array(
						'tbid' => session('member_id')
					));
					$desk_arr = explode(',', $desk);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $_POST['id'];
					}else{
						if($_POST['to'] == 0){
							if($_POST['boa'] == 'b'){
								array_splice($desk_arr, 0, 0, $_POST['id']);
							}else{
								array_splice($desk_arr, 1, 0, $_POST['id']);
							}
						}else{
							if($_POST['boa'] == 'b'){
								array_splice($desk_arr, $_POST['to'], 0, $_POST['id']);
							}else{
								array_splice($desk_arr, $_POST['to'] + 1, 0, $_POST['id']);
							}
						}
					}
					$db->update('tb_member', array(
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					), array(
						'tbid' => session('member_id')
					));
					$db->update('tb_member_app', array(
						'folder_id' => 0
					), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
				case 'folder-otherdesk':
					$todesk = $db->get('tb_member', 'desk'.$_POST['todesk'], array(
						'tbid' => session('member_id')
					));
					$todesk_arr = explode(',', $todesk);
					if($todesk_arr[0] == ''){
						$todesk_arr[0] = $_POST['id'];
					}else{
						$todesk_arr[] = $_POST['id'];
					}
					$db->update('tb_member', array(
						'desk'.$_POST['todesk'] => implode(',', $todesk_arr)
					), array(
						'tbid' => session('member_id')
					));
					$db->update('tb_member_app', array(
						'folder_id' => 0
					), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
			}
			break;
		//新建文件夹
		case 'addFolder':
			addApp(array(
				'type' => 'folder',
				'icon' => $_POST['icon'],
				'name' => $_POST['name'],
				'desk' => (int)$_POST['desk']
			));
			break;
		//文件夹重命名
		case 'updateFolder':
			$db->update('tb_member_app', array(
				'icon' => $_POST['icon'],
				'name' => $_POST['name']
			), array(
				'AND' => array(
					'tbid' => $_POST['id'],
					'member_id' => session('member_id')
				)
			));
			break;
		//获取应用评分
		case 'getAppStar':
			$startnum = $db->get('tb_app', 'starnum', array(
				'tbid' => $_POST['id']
			));
			echo is_int($startnum) || $startnum == 0 ? (int)$startnum : sprintf('%.1f', $startnum);
			break;
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
					'starnum' => $_POST['starnum'],
					'dt' => date('Y-m-d H:i:s'),
					'member_id' => session('member_id')
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
//		case 'html5upload':
//			$r = new stdClass();
//			//文件名转码，防止中文出现乱码，最后输出时再转回来
//			$file_array = explode('.', iconv('UTF-8', 'gb2312', $_FILES['xfile']['name']));
//			//取出扩展名
//			$extension = $file_array[count($file_array) - 1];
//			unset($file_array[count($file_array) - 1]);
//			//取出文件名
//			$name = implode('.', $file_array);
//			//拼装新文件名（含扩展名）
//			$file = $name.'_'.sha1(@microtime().$_FILES['xfile']['name']).'.'.$extension;
//			//验证文件是否合格
//			if(in_array($extension, $uploadFileUnType)){
//				$r->error = "上传文件类型系统不支持";
//			}else if($_FILES['xfile']['size'] > ($uploadFileMaxSize * 1048576)){
//				$r->error = "上传文件单个大小不能超过 $uploadFileMaxSize MB";
//			}else{
//				$icon = '';
//				foreach($uploadFileType as $uft){
//					if($uft['ext'] == $extension){
//						$icon = $uft['icon'];
//						break;
//					}
//				}
//				if($icon == ''){
//					$icon = 'img/ui/file_unknow.png';
//				}
//				//生成文件存放路径
//				$dir = 'uploads/member/'.session('member_id').'/file/';
//				if(!is_dir($dir)){
//					//循环创建目录
//					recursive_mkdir($dir);
//				}
//				//上传
//				move_uploaded_file($_FILES['xfile']["tmp_name"], $dir.$file);
//				
//				$r->dir = $dir;
//				$r->file = iconv('gb2312', 'UTF-8', $file);
//				$r->name = iconv('gb2312', 'UTF-8', $name);
//				$r->extension = iconv('gb2312', 'UTF-8', $extension);
//				$r->icon = $icon;
//			}
//			echo json_encode($r);
//			break;
	}
?>