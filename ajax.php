<?php
	require('global.php');

	//所有操作分两种类型：读和写
	//如 getWallpaper 是读操作，setWallpaper 是写操作
	//当遇到写操作时，则需要通过下面登录验证，未登录用户则中断之后的写操作，并输出错误信息
	if(in_array($_REQUEST['ac'], ['setWallpaper', 'setDockPos', 'setAppXY', 'addMyApp', 'delMyApp', 'moveMyApp', 'updateMyApp', 'addFolder', 'updateFolder', 'updateAppStar'])){
		if(!checkLogin()){
			exit('ERROR_NOT_LOGGED_IN');
		}
	}

	switch($_REQUEST['ac']){
		//获取头像
		case 'getAvatar':
			$cb['avatar'] = getAvatar(checkLogin() ? session('member_id') : 0);
			echo json_encode($cb);
			break;
		//获取主题
		case 'getWallpaper':
			$cb['wallpaper'] = getWallpaper();
			echo json_encode($cb);
			break;
		//更新主题
		case 'setWallpaper':
			$data = [
				'wallpaperstate' => $_POST['wpstate'],
				'wallpapertype' => $_POST['wptype']
			];
			switch($_POST['wpstate']){
				case '0':
					$data = ['wallpapertype' => $_POST['wptype']];
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
			$db->update('tb_member', $data, ['tbid' => session('member_id')]);
			echo json_encode(['status' => true]);
			break;
		//更新应用码头位置
		case 'setDockPos':
			$db->update('tb_member', ['dockpos' => $_POST['dock']], ['tbid' => session('member_id')]);
			if($_POST['dock'] == 'none'){
				$rs = $db->get('tb_member', ['dock', 'desk'.$_POST['desk']], ['tbid' => session('member_id')]);
				$dock_arr = $rs['dock'];
				$desk_arr = $rs['desk'.$_POST['desk']];
				if($dock_arr != ''){
					if($desk_arr == ''){
						$desk_arr = $dock_arr;
					}else{
						$desk_arr = $desk_arr.','.$dock_arr;
					}
					$db->update('tb_member', [
						'dock' => '',
						'desk'.$_POST['desk'] => $desk_arr
					], ['tbid' => session('member_id')]);
				}
			}
			echo json_encode(['status' => true]);
			break;
		//更新图标排列方式
		case 'setAppXY':
			$db->update('tb_member', ['appxy' => $_POST['appxy']], ['tbid' => session('member_id')]);
			echo json_encode(['status' => true]);
			break;
		//更新图标显示尺寸
		case 'setAppSize':
			$db->update('tb_member', ['appsize' => $_POST['appsize']], ['tbid' => session('member_id')]);
			echo json_encode(['status' => true]);
			break;
		//更新图标垂直间距
		case 'setAppVerticalSpacing':
			$db->update('tb_member', ['appverticalspacing' => $_POST['appverticalspacing']], ['tbid' => session('member_id')]);
			echo json_encode(['status' => true]);
			break;
		//更新图标水平间距
		case 'setAppHorizontalSpacing':
			$db->update('tb_member', ['apphorizontalspacing' => $_POST['apphorizontalspacing']], ['tbid' => session('member_id')]);
			echo json_encode(['status' => true]);
			break;
		//更新默认桌面
		case 'setDesk':
			if($_POST['desk'] != '' && $_POST['desk'] >= 1 && $_POST['desk'] <= 5){
				$db->update('tb_member', ['desk' => $_POST['desk']], ['tbid' => session('member_id')]);
			}
			echo json_encode(['status' => true]);
			break;
		//获取桌面图标
		case 'getMyApp':
			if(checkLogin()){
				$appid = $db->get('tb_member', ['dock', 'desk1', 'desk2', 'desk3', 'desk4', 'desk5'], ['tbid' => session('member_id')]);
				if($appid['dock'] != ''){
					$data = [];
					foreach($db->select('tb_member_app', '*', [
						'tbid' => explode(',', $appid['dock']),
						'ORDER' => ['tbid' => explode(',', $appid['dock'])]
					]) as $v){
						$tmp = [];
						if($v['type'] == 'folder'){
							$folderid[] = $v['tbid'];
						}
						$tmp['type'] = $v['type'];
						$tmp['appid'] = $v['tbid'];
						$tmp['realappid'] = $v['realid'];
						$tmp['name'] = $v['name'];
						if($v['type'] == 'file'){
							$tmp['name'] .= '.'.$v['ext'];
						}
						$tmp['icon'] = $v['icon'];
						$data[] = $tmp;
					}
					$desktop['dock'] = $data;
				}
				for($i = 1; $i <= 5; $i++){
					if($appid['desk'.$i] != ''){
						$data = [];
						foreach($db->select('tb_member_app', '*', [
							'tbid' => explode(',', $appid['desk'.$i]),
							'ORDER' => ['tbid' => explode(',', $appid['desk'.$i])]
						]) as $v){
							$tmp = [];
							if($v['type'] == 'folder'){
								$folderid[] = $v['tbid'];
							}
							$tmp['type'] = $v['type'];
							$tmp['appid'] = $v['tbid'];
							$tmp['realappid'] = $v['realid'];
							$tmp['name'] = $v['name'];
							if($v['type'] == 'file'){
								$tmp['name'] .= '.'.$v['ext'];
							}
							$tmp['icon'] = $v['icon'];
							$data[] = $tmp;
						}
						$desktop['desk'.$i] = $data;
					}
				}
				if($folderid != NULL){
					$data = [];
					foreach($folderid as $v){
						$tmp = [];
						$tmp['appid'] = $v;
						$tmp['apps'] = [];
						$folderapps = $db->select('tb_member_app', '*', [
							'AND' => [
								'folder_id' => $v,
								'member_id' => session('member_id')
							]
						]);
						foreach($folderapps as $vv){
							$tmpp = [];
							$tmpp['type'] = $vv['type'];
							$tmpp['appid'] = $vv['tbid'];
							$tmpp['realappid'] = $vv['realid'];
							$tmpp['name'] = $vv['name'];
							if($vv['type'] == 'file'){
								$tmpp['name'] .= '.'.$vv['ext'];
							}
							$tmpp['icon'] = $vv['icon'];
							$tmp['apps'][] = $tmpp;
						}
						$data[] = $tmp;
					}
					$desktop['folder'] = $data;
				}
			}else{
				$appid = $db->get('tb_setting', ['dock', 'desk1', 'desk2', 'desk3', 'desk4', 'desk5']);
				if($appid['dock'] != ''){
					$data = [];
					foreach($db->select('tb_app', '*', [
						'tbid' => explode(',', $appid['dock']),
						'ORDER' => ['tbid' => explode(',', $appid['dock'])]
					]) as $v){
						$tmp = [];
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
						$data = [];
						foreach($db->select('tb_app', '*', [
							'tbid' => explode(',', $appid['desk'.$i]),
							'ORDER' => ['tbid' => explode(',', $appid['desk'.$i])]
						]) as $v){
							$tmp = [];
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
			echo json_encode($desktop == NULL ? [] : $desktop);
			break;
		//根据id获取图标
		case 'getMyAppById':
			$app = [];
			if(checkLogin()){
				switch($_POST['type']){
					case 'window':
					case 'widget':
						$rs = $db->get('tb_member_app', '*', [
							'AND' => [
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							]
						]);
						if($rs){
							if(!$db->has('tb_app', ['tbid' => $rs['realid']])){
								$app['error'] = 'ERROR_NOT_FOUND';
							}else{
								$app['type'] = $rs['type'];
								$app['appid'] = $rs['tbid'];
								$app['realappid'] = $rs['realid'];
								$app['name'] = $rs['name'];
								$app['icon'] = $rs['icon'];
								$app['width'] = $rs['width'];
								$app['height'] = $rs['height'];
								$app['url'] = $db->get('tb_app', 'url', ['tbid' => $rs['realid']]);
								if($rs['type'] == 'window'){
									$app['isresize'] = $rs['isresize'];
									$app['isopenmax'] = $rs['isopenmax'];
									$app['issetbar'] = $rs['issetbar'];
									$app['isflash'] = $rs['isflash'];
								}
							}
						}else{
							$app['error'] = 'ERROR_NOT_INSTALLED';
						}
						break;
					case 'pwindow':
					case 'pwidget':
					case 'folder':
					case 'file':
						$rs = $db->get('tb_member_app', '*', [
							'AND' => [
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							]
						]);
						if($rs){
							$app['type'] = $rs['type'];
							$app['appid'] = $rs['tbid'];
							$app['realappid'] = $rs['realid'];
							$app['name'] = $rs['name'];
							$app['icon'] = $rs['icon'];
							$app['width'] = $rs['width'];
							$app['height'] = $rs['height'];
							$app['url'] = $rs['url'];
							if($rs['type'] == 'pwindow'){
								$app['isresize'] = $rs['isresize'];
								$app['isopenmax'] = $rs['isopenmax'];
								$app['issetbar'] = $rs['issetbar'];
								$app['isflash'] = $rs['isflash'];
							}else if($rs['type'] == 'file'){
								$app['ext'] = $rs['ext'];
								$app['url'] = '';
							}
						}else{
							$app['error'] = 'ERROR_NOT_FOUND';
						}
						break;
				}
			}else{
				$rs = $db->query('SELECT GROUP_CONCAT(dock, \',\', desk1, \',\', desk2, \',\', desk3, \',\', desk4, \',\', desk5) as appid from tb_setting')->fetch();
				$appid = explode(',', $rs['appid']);
				if(in_array($_POST['id'], $appid)){
					$rs = $db->get('tb_app', '*', ['tbid' => $_POST['id']]);
					$app['type'] = $rs['type'];
					$app['appid'] = $rs['tbid'];
					$app['realappid'] = $rs['tbid'];
					$app['name'] = $rs['name'];
					$app['icon'] = $rs['icon'];
					$app['width'] = $rs['width'];
					$app['height'] = $rs['height'];
					$app['url'] = $rs['url'];
					if($rs['type'] == 'window'){
						$app['isresize'] = $rs['isresize'];
						$app['isopenmax'] = $rs['isopenmax'];
						$app['issetbar'] = $rs['issetbar'];
						$app['isflash'] = $rs['isflash'];
					}
				}else{
					$app['error'] = 'ERROR_NOT_FOUND';
				}
			}
			echo json_encode($app);
			break;
		case 'getAppidByRealappid':
			echo $db->get('tb_member_app', 'tbid', [
				'AND' => [
					'realid' => $_POST['id'],
					'member_id' => session('member_id')
				]
			]);
			break;
		//添加桌面图标
		case 'addMyApp':
			addApp([
				'type' => '',
				'id' => $_POST['id'],
				'desk' => $_POST['desk']
			]);
			echo json_encode(['status' => true]);
			break;
		//删除桌面图标
		case 'delMyApp':
			delApp($_POST['id']);
			echo json_encode(['status' => true]);
			break;
		//更新桌面图标
		case 'moveMyApp':
			switch($_POST['movetype']){
				case 'dock-folder':
					$dock = $db->get('tb_member', 'dock', ['tbid' => session('member_id')]);
					$dock_arr = explode(',', $dock);
					$key = array_search($_POST['id'], $dock_arr);
					unset($dock_arr[$key]);
					$db->update('tb_member', ['dock' => implode(',', $dock_arr)], ['tbid' => session('member_id')]);
					$db->update('tb_member_app', ['folder_id' => $_POST['to']], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
				case 'dock-dock':
					$dock = $db->get('tb_member', 'dock', ['tbid' => session('member_id')]);
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
						$db->update('tb_member', ['dock' => implode(',', $dock_arr)], ['tbid' => session('member_id')]);
					}
					break;
				case 'dock-desk':
					$rs = $db->get('tb_member', ['dock', 'desk'.$_POST['desk']], ['tbid' => session('member_id')]);
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
					$db->update('tb_member', [
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					], ['tbid' => session('member_id')]);
					break;
				case 'dock-otherdesk':
					$rs = $db->get('tb_member', ['dock', 'desk'.$_POST['todesk']], ['tbid' => session('member_id')]);
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$_POST['todesk']]);
					unset($dock_arr[$_POST['from']]);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $_POST['id'];
					}else{
						$desk_arr[] = $_POST['id'];
					}
					$db->update('tb_member', [
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['todesk'] => implode(',', $desk_arr)
					], ['tbid' => session('member_id')]);
					break;
				case 'desk-folder':
					$desk = $db->get('tb_member', 'desk'.$_POST['desk'], ['tbid' => session('member_id')]);
					$desk_arr = explode(',', $desk);
					$key = array_search($_POST['id'], $desk_arr);
					unset($desk_arr[$key]);
					$db->update('tb_member', ['desk'.$_POST['desk'] => implode(',', $desk_arr)], ['tbid' => session('member_id')]);
					$db->update('tb_member_app', ['folder_id' => $_POST['to']], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
				case 'desk-dock':
					$rs = $db->get('tb_member', ['dock', 'desk'.$_POST['desk']], ['tbid' => session('member_id')]);
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
					$db->update('tb_member', [
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					], ['tbid' => session('member_id')]);
					break;
				case 'desk-desk':
					$desk = $db->get('tb_member', 'desk'.$_POST['desk'], ['tbid' => session('member_id')]);
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
						$db->update('tb_member', ['desk'.$_POST['desk'] => implode(',', $desk_arr)], ['tbid' => session('member_id')]);
					}
					break;
				case 'desk-otherdesk':
					$rs = $db->get('tb_member', ['desk'.$_POST['fromdesk'], 'desk'.$_POST['todesk']], ['tbid' => session('member_id')]);
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
					$db->update('tb_member', [
						'desk'.$_POST['fromdesk'] => implode(',', $fromdesk_arr),
						'desk'.$_POST['todesk'] => implode(',', $todesk_arr)
					], ['tbid' => session('member_id')]);
					break;
				case 'folder-folder':
					$db->update('tb_member_app', ['folder_id' => $_POST['to']], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
				case 'folder-dock':
					$rs = $db->get('tb_member', ['dock', 'desk'.$_POST['desk']], ['tbid' => session('member_id')]);
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
					$db->update('tb_member', [
						'dock' => implode(',', $dock_arr),
						'desk'.$_POST['desk'] => implode(',', $desk_arr)
					], ['tbid' => session('member_id')]);
					$db->update('tb_member_app', ['folder_id' => 0], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
				case 'folder-desk':
					$desk = $db->get('tb_member', 'desk'.$_POST['desk'], ['tbid' => session('member_id')]);
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
					$db->update('tb_member', ['desk'.$_POST['desk'] => implode(',', $desk_arr)], ['tbid' => session('member_id')]);
					$db->update('tb_member_app', ['folder_id' => 0], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
				case 'folder-otherdesk':
					$todesk = $db->get('tb_member', 'desk'.$_POST['todesk'], ['tbid' => session('member_id')]);
					$todesk_arr = explode(',', $todesk);
					if($todesk_arr[0] == ''){
						$todesk_arr[0] = $_POST['id'];
					}else{
						$todesk_arr[] = $_POST['id'];
					}
					$db->update('tb_member', ['desk'.$_POST['todesk'] => implode(',', $todesk_arr)], ['tbid' => session('member_id')]);
					$db->update('tb_member_app', ['folder_id' => 0], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
			}
			echo json_encode(['status' => true]);
			break;
		//新建文件夹
		case 'addFolder':
			addApp([
				'type' => 'folder',
				'name' => $_POST['name'],
				'desk' => $_POST['desk']
			]);
			echo json_encode(['status' => true]);
			break;
		//文件夹重命名
		case 'updateFolder':
			$db->update('tb_member_app', ['name' => $_POST['name']], [
				'AND' => [
					'tbid' => $_POST['id'],
					'member_id' => session('member_id')
				]
			]);
			echo json_encode(['status' => true]);
			break;
	}
?>
