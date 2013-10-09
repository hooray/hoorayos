<?php
	require('../../global.php');
	
	switch($ac){
		case 'edit':
			$val_issetbar = $val_kindid == 1 ? 0 : 1;
			$set = array(
				'icon = "'.$val_icon.'"',
				'name = "'.$val_name.'"',
				'kindid = '.(int)$val_kindid,
				'url = "'.$val_url.'"',
				'width = '.(int)$val_width,
				'height = '.(int)$val_height,
				'isresize = '.(int)$val_isresize,
				'isopenmax = '.(int)$val_isopenmax,
				'issetbar = '.(int)$val_issetbar,
				'isflash = '.(int)$val_isflash,
				'remark = "'.$val_remark.'"'
			);
			if($id == ''){
				$set[] = 'type = "'.$val_type.'"';
				$set[] = 'dt = now()';
				$set[] = 'verifytype = 1';
				$db->insert(0, 0, 'tb_app', $set);
			}else{
				$db->update(0, 0, 'tb_app', $set, 'and tbid = '.(int)$id);
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'uploadImg':
			include('libs/Uploader.class.php');
			$config = array(
				'savePath' => 'uploads/shortcut/', //保存路径
				'allowFiles' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
				'maxSize' => 1000 //文件大小限制，单位KB
			);
			$up = new Uploader('xfile', $config);
			$info = $up->getFileInfo();
			echo '{"url":"'.$info['url'].'","fileType":"'.$info['type'].'","original":"'.$info['originalName'].'","state":"'.$info['state'].'"}';
			break;
		case 'pass':
			$db->update(0, 0, 'tb_app', 'verifytype = 1', 'and tbid = '.(int)$appid);
			break;
		case 'unpass':
			$db->update(0, 0, 'tb_app', 'verifytype = 3, verifyinfo = "'.$info.'"', 'and tbid = '.(int)$appid);
			break;
	}
?>