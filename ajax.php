<?php
	require('global.php');
	
	//所有操作分两种类型：读和写
	//如 getWallpaper 是读操作，setWallpaper 是写操作
	//当遇到写操作时，则需要通过下面登录验证，未登录用户则中断之后的写操作，并输出错误信息
	$write_operating = array('setWallpaper', 'setDockPos', 'setAppXY', 'addMyApp', 'delMyApp', 'moveMyApp', 'updateMyApp', 'addFolder', 'updateFolder', 'updateAppStar');
	if(in_array($ac, $write_operating)){
		if(!checkLogin()){
			exit('ERROR_NOT_LOGGED_IN');
		}
	}
		
	switch($ac){
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
			$set = array(
				'wallpaperstate = '.$wpstate,
				'wallpapertype = "'.$wptype.'"'
			);
			switch($wpstate){
				case '0':
					$set = array(
						'wallpapertype = "'.$wptype.'"'
					);
					break;
				case '1':
				case '2':
					if($wp != ''){
						$set[] = 'wallpaper_id = '.$wp;
					}					
					break;
				case '3':
					if($wp != ''){
						$set[] = 'wallpaperwebsite = "'.$wp.'"';
					}
					break;
			}
			$db->update(0, 0, 'tb_member', $set, 'and tbid = '.session('member_id'));
			break;
		//更新应用码头位置
		case 'setDockPos':
			$db->update(0, 0, 'tb_member', 'dockpos = "'.$dock.'"', 'and tbid = '.session('member_id'));
			if($dock == 'none'){
				$rs = $db->select(0, 1, 'tb_member', 'dock, desk'.$desk, 'and tbid = '.session('member_id'));
				$dock_arr = $rs['dock'];
				$desk_arr = $rs['desk'.$desk];
				if($dock_arr != ''){
					if($desk_arr == ''){
						$desk_arr = $dock_arr;
					}else{
						$desk_arr = $desk_arr.','.$dock_arr;
					}
					$db->update(0, 0, 'tb_member', 'dock = "", desk'.$desk.' = "'.$desk_arr.'"', 'and tbid = '.session('member_id'));
				}
			}
			break;
		//更新图标排列方式
		case 'setAppXY':
			$db->update(0, 0, 'tb_member', 'appxy = "'.$appxy.'"', 'and tbid = '.session('member_id'));
			break;
		//更新图标显示尺寸
		case 'setAppSize':
			$db->update(0, 0, 'tb_member', 'appsize = "'.$appsize.'"', 'and tbid = '.session('member_id'));
			break;
		//更新默认桌面
		case 'setDesk':
			$db->update(0, 0, 'tb_member', 'desk = '.$desk, 'and tbid = '.session('member_id'));
			break;
		//获取桌面图标
		case 'getMyApp':
			$desktop['dock'] = array();
			for($i = 1; $i <= 5; $i++){
				$desktop['desk'.$i] = array();
			}
			$desktop['folder'] = array();
			$folderid = array();
			if(checkLogin()){
				$appid = $db->select(0, 1, 'tb_member', 'dock, desk1, desk2, desk3, desk4, desk5', 'and tbid = '.session('member_id'));
				if($appid['dock'] != ''){
					$rs = $db->select(0, 0, 'tb_member_app', '*', 'and tbid in('.$appid['dock'].')', 'field(tbid, '.$appid['dock'].')');
					if($rs != NULL){
						foreach($rs as $v){
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
						unset($data, $tmp);
					}
				}
				for($i = 1; $i <= 5; $i++){
					if($appid['desk'.$i] != ''){
						$rs = $db->select(0, 0, 'tb_member_app', '*', 'and tbid in('.$appid['desk'.$i].')', 'field(tbid, '.$appid['desk'.$i].')');
						if($rs != NULL){
							foreach($rs as $v){
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
							unset($data, $tmp);
						}
					}
				}
				if($folderid != NULL){
					foreach($folderid as $v){
						$tmp['appid'] = $v;
						$folderapps = $db->select(0, 0, 'tb_member_app', '*', 'and folder_id = '.$v.' and member_id = '.session('member_id'));
						$tmp['apps'] = array();
						if($folderapps != NULL){
							foreach($folderapps as $vv){
								$tmpp['type'] = $vv['type'];
								$tmpp['appid'] = $vv['tbid'];
								$tmpp['realappid'] = $vv['realid'];
								$tmpp['name'] = $vv['name'];
								$tmpp['icon'] = $vv['icon'];
								$tmp['apps'][] = $tmpp;
							}
						}
						$data[] = $tmp;
					}
					$desktop['folder'] = $data;
					unset($data, $tmp, $folderid);
				}
			}else{
				$appid = $db->select(0, 1, 'tb_setting', 'dock, desk1, desk2, desk3, desk4, desk5');
				if($appid['dock'] != ''){
					$rs = $db->select(0, 0, 'tb_app', '*', 'and tbid in('.$appid['dock'].')', 'field(tbid, '.$appid['dock'].')');
					if($rs != NULL){
						foreach($rs as $v){
							$tmp['type'] = $v['type'];
							$tmp['appid'] = $v['tbid'];
							$tmp['realappid'] = $v['tbid'];
							$tmp['name'] = $v['name'];
							$tmp['icon'] = $v['icon'];
							$data[] = $tmp;
						}
						$desktop['dock'] = $data;
						unset($data, $tmp);
					}
				}
				for($i = 1; $i <= 5; $i++){
					if($appid['desk'.$i] != ''){
						$rs = $db->select(0, 0, 'tb_app', '*', 'and tbid in('.$appid['desk'.$i].')', 'field(tbid, '.$appid['desk'.$i].')');
						if($rs != NULL){
							foreach($rs as $v){
								$tmp['type'] = $v['type'];
								$tmp['appid'] = $v['tbid'];
								$tmp['realappid'] = $v['tbid'];
								$tmp['name'] = $v['name'];
								$tmp['icon'] = $v['icon'];
								$data[] = $tmp;
							}
							$desktop['desk'.$i] = $data;
							unset($data, $tmp);
						}
					}
				}
			}
			echo json_encode($desktop);
			break;
		//根据id获取图标
		case 'getMyAppById':
			$app = array();
			if(checkLogin()){
				switch($type){
					case 'app':
					case 'widget':
						$rs = $db->select(0, 1, 'tb_member_app', '*', 'and realid = '.$id.' and member_id = '.session('member_id'));
						if($rs != NULL){
							$ishas = $db->select(0, 2, 'tb_app', '*', 'and tbid = '.$rs['realid']);
							if($ishas == 0){
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
							if($rs['type'] == 'app' || $rs['type'] == 'widget'){
								$realurl = $db->select(0, 1, 'tb_app', 'url', 'and tbid = '.$rs['realid']);
								$app['url'] = $realurl['url'];
							}else{
								$app['url'] = $rs['url'];
							}
						}else{
							$app['error'] = 'ERROR_NOT_INSTALLED';
						}
						break;
					case 'papp':
					case 'pwidget':
					case 'folder':
						$rs = $db->select(0, 1, 'tb_member_app', '*', 'and tbid = '.$id.' and member_id = '.session('member_id'));
						if($rs != NULL){
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
				$appid = $db->select(0, 1, 'tb_setting', 'dock, desk1, desk2, desk3, desk4, desk5');
				if($appid['dock'] != ''){
					$appids[] = $appid['dock'];
				}
				for($i = 1; $i <= 5; $i++){
					if($appid['desk'.$i] != ''){
						$appids[] = $appid['desk'.$i];
					}
				}
				if(in_array($id, explode(',', implode(',', $appids)))){
					$rs = $db->select(0, 1, 'tb_app', '*', 'and tbid = '.$id);
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
					$app['error'] = 'ERROR_NOT_INSTALLED';
				}
			}
			echo json_encode($app);
			break;
		//添加桌面图标
		case 'addMyApp':
			addApp(array(
				'type' => '',
				'id' => $id,
				'desk' => $desk
			));
			break;
		//删除桌面图标
		case 'delMyApp':
			delApp($id);
			break;
		//更新桌面图标
		case 'moveMyApp':
			switch($movetype){
				case 'dock-folder':
					$rs = $db->select(0, 1, 'tb_member', 'dock', 'and tbid = '.session('member_id'));
					$dock_arr = explode(',', $rs['dock']);
					$key = array_search($id, $dock_arr);
					unset($dock_arr[$key]);
					$db->update(0, 0, 'tb_member', 'dock = "'.implode(',', $dock_arr).'"', 'and tbid = '.session('member_id'));
					$db->update(0, 0, 'tb_member_app', 'folder_id = '.$to, 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
				case 'dock-dock':
					$rs = $db->select(0, 1, 'tb_member', 'dock', 'and tbid = '.session('member_id'));
					$dock_arr = explode(',', $rs['dock']);
					//判断传入的应用id和数据库里的id是否吻合
					if($dock_arr[$from] == $id){
						if($from > $to){
							for($i = $from; $i > $to; $i--){
								$dock_arr[$i] = $dock_arr[$i-1];
							}
							$dock_arr[$to] = $id;
						}else if($to > $from){
							for($i = $from; $i < $to; $i++){
								$dock_arr[$i] = $dock_arr[$i+1];
							}
							$dock_arr[$to] = $id;
						}
						$dock_arr = formatAppidArray($dock_arr);
						$db->update(0, 0, 'tb_member', 'dock = "'.implode(',', $dock_arr).'"', 'and tbid = '.session('member_id'));
					}
					break;
				case 'dock-desk':
					$rs = $db->select(0, 1, 'tb_member', 'dock, desk'.$desk, 'and tbid = '.session('member_id'));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$desk]);
					unset($dock_arr[$from]);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $id;
					}else{
						array_splice($desk_arr, $to, 0, $id);
					}
					$db->update(0, 0, 'tb_member', 'dock = "'.implode(',', $dock_arr).'", desk'.$desk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					break;
				case 'dock-otherdesk':
					$rs = $db->select(0, 1, 'tb_member', 'dock, desk'.$todesk, 'and tbid = '.session('member_id'));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$todesk]);
					unset($dock_arr[$from]);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $id;
					}else{
						$desk_arr[] = $id;
					}
					$db->update(0, 0, 'tb_member', 'dock = "'.implode(',', $dock_arr).'", desk'.$todesk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					break;
				case 'desk-folder':
					$rs1 = $db->select(0, 1, 'tb_member', 'desk'.$desk, 'and tbid = '.session('member_id'));
					$desk_arr = explode(',', $rs1['desk'.$desk]);
					$key = array_search($id, $desk_arr);
					unset($desk_arr[$key]);
					$db->update(0, 0, 'tb_member', 'desk'.$desk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					$db->update(0, 0, 'tb_member_app', 'folder_id = '.$to, 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
				case 'desk-dock':
					$rs = $db->select(0, 1, 'tb_member', 'dock, desk'.$desk, 'and tbid = '.session('member_id'));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$desk]);
					unset($desk_arr[$from]);
					if($dock_arr[0] == ''){
						$dock_arr[0] = $id;
					}else{
						array_splice($dock_arr, $to, 0, $id);						
					}
					if(count($dock_arr) > 7){
						$desk_arr[] = $dock_arr[7];
						unset($dock_arr[7]);
					}
					$db->update(0, 0, 'tb_member', 'dock = "'.implode(',', $dock_arr).'", desk'.$desk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					break;
				case 'desk-desk':
					$rs = $db->select(0, 1, 'tb_member', 'desk'.$desk, 'and tbid = '.session('member_id'));
					$desk_arr = explode(',', $rs['desk'.$desk]);
					//判断传入的应用id和数据库里的id是否吻合
					if($desk_arr[$from] == $id){
						if($from > $to){
							for($i = $from; $i > $to; $i--){
								$desk_arr[$i] = $desk_arr[$i-1];
							}
							$desk_arr[$to] = $id;
						}else if($to > $from){
							for($i = $from; $i < $to; $i++){
								$desk_arr[$i] = $desk_arr[$i+1];
							}
							$desk_arr[$to] = $id;
						}
						$desk_arr = formatAppidArray($desk_arr);
						$db->update(0, 0, 'tb_member', 'desk'.$desk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					}
					break;
				case 'desk-otherdesk':
					$rs = $db->select(0, 1, 'tb_member', 'desk'.$fromdesk.', desk'.$todesk, 'and tbid = '.session('member_id'));
					$fromdesk_arr = explode(',', $rs['desk'.$fromdesk]);
					$todesk_arr = explode(',', $rs['desk'.$todesk]);
					unset($fromdesk_arr[$from]);
					if($todesk_arr[0] == ''){
						$todesk_arr[0] = $id;
					}else{
						if($to != -1){
							array_splice($todesk_arr, $to, 0, $id);
						}else{
							$todesk_arr[] = $id;
						}
					}
					$db->update(0, 0, 'tb_member', 'desk'.$fromdesk.' = "'.implode(',', $fromdesk_arr).'", desk'.$todesk.' = "'.implode(',', $todesk_arr).'"', 'and tbid = '.session('member_id'));
					break;
				case 'folder-folder':
					$db->update(0, 0, 'tb_member_app', 'folder_id = '.$to, 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
				case 'folder-dock':
					$rs = $db->select(0, 1, 'tb_member', 'dock, desk'.$desk, 'and tbid = '.session('member_id'));
					$dock_arr = explode(',', $rs['dock']);
					$desk_arr = explode(',', $rs['desk'.$desk]);
					if($dock_arr[0] == ''){
						$dock_arr[0] = $id;
					}else{
						array_splice($dock_arr, $to, 0, $id);
					}
					if(count($dock_arr) > 7){
						$desk_arr[] = $dock_arr[7];
						unset($dock_arr[7]);
					}
					$db->update(0, 0, 'tb_member', 'dock = "'.implode(',', $dock_arr).'", desk'.$desk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					$db->update(0, 0, 'tb_member_app', 'folder_id = 0', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
				case 'folder-desk':
					$rs = $db->select(0, 1, 'tb_member', 'desk'.$desk, 'and tbid = '.session('member_id'));
					$desk_arr = explode(',', $rs['desk'.$desk]);
					if($desk_arr[0] == ''){
						$desk_arr[0] = $id;
					}else{
						array_splice($desk_arr, $to, 0, $id);
					}
					$db->update(0, 0, 'tb_member', 'desk'.$desk.' = "'.implode(',', $desk_arr).'"', 'and tbid = '.session('member_id'));
					$db->update(0, 0, 'tb_member_app', 'folder_id = 0', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
				case 'folder-otherdesk':
					$rs = $db->select(0, 1, 'tb_member', 'desk'.$todesk, 'and tbid = '.session('member_id'));
					$todesk_arr = explode(',', $rs['desk'.$todesk]);
					if($todesk_arr[0] == ''){
						$todesk_arr[0] = $id;
					}else{
						$todesk_arr[] = $id;
					}
					$db->update(0, 0, 'tb_member', 'desk'.$todesk.' = "'.implode(',', $todesk_arr).'"', 'and tbid = '.session('member_id'));
					$db->update(0, 0, 'tb_member_app', 'folder_id = 0', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
			}
			break;
		//新建文件夹
		case 'addFolder':
			addApp(array(
				'type' => 'folder',
				'icon' => $icon,
				'name' => $name,
				'desk' => $desk
			));
			break;
		//文件夹重命名
		case 'updateFolder':
			$db->update(0, 0, 'tb_member_app', 'icon = "'.$icon.'", name = "'.$name.'"', 'and tbid = '.$id.' and member_id = '.session('member_id'));
			break;
		//获取应用评分
		case 'getAppStar':
			$rs = $db->select(0, 1, 'tb_app', 'starnum', 'and tbid = '.$id);
			echo $rs['starnum'];
			break;
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
		case 'html5upload':
			$r = new stdClass();
			//文件名转码，防止中文出现乱码，最后输出时再转回来
			$file_array = explode('.', iconv('UTF-8', 'gb2312', $_FILES['xfile']['name']));
			//取出扩展名
			$extension = $file_array[count($file_array) - 1];
			unset($file_array[count($file_array) - 1]);
			//取出文件名
			$name = implode('.', $file_array);
			//拼装新文件名（含扩展名）
			$file = $name.'_'.sha1(@microtime().$_FILES['xfile']['name']).'.'.$extension;
			//验证文件是否合格
			if(in_array($extension, $uploadFileUnType)){
				$r->error = "上传文件类型系统不支持";
			}else if($_FILES['xfile']['size'] > ($uploadFileMaxSize * 1048576)){
				$r->error = "上传文件单个大小不能超过 $uploadFileMaxSize MB";
			}else{
				$icon = '';
				foreach($uploadFileType as $uft){
					if($uft['ext'] == $extension){
						$icon = $uft['icon'];
						break;
					}
				}
				if($icon == ''){
					$icon = 'img/ui/file_unknow.png';
				}
				//生成文件存放路径
				$dir = 'uploads/member/'.session('member_id').'/file/';
				if(!is_dir($dir)){
					//循环创建目录
					recursive_mkdir($dir);
				}
				//上传
				move_uploaded_file($_FILES['xfile']["tmp_name"], $dir.$file);
				
				$r->dir = $dir;
				$r->file = iconv('gb2312', 'UTF-8', $file);
				$r->name = iconv('gb2312', 'UTF-8', $name);
				$r->extension = iconv('gb2312', 'UTF-8', $extension);
				$r->icon = $icon;
			}
			echo json_encode($r);
			break;
	}
?>