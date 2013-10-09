<?php
	require('../../global.php');
	
	switch($ac){
		case 'edit':
			$set = array(
				'icon = "'.$val_icon.'"',
				'name = "'.$val_name.'"',
				'width = '.(int)$val_width,
				'height = '.(int)$val_height,
				'isresize = '.(int)$val_isresize,
				'isopenmax = '.(int)$val_isopenmax,
				'isflash = '.(int)$val_isflash
			);
			$db->update(0, 0, 'tb_member_app', $set, 'and tbid = '.(int)$id.' and member_id = '.session('member_id'));
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'uploadImg':
			include('libs/Uploader.class.php');
			$config = array(
				'savePath' => 'uploads/'.session('member_id').'/shortcut/', //保存路径
				'allowFiles' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
				'maxSize' => 1000 //文件大小限制，单位KB
			);
			$up = new Uploader('xfile', $config);
			$info = $up->getFileInfo();
			echo '{"url":"'.$info['url'].'","fileType":"'.$info['type'].'","original":"'.$info['originalName'].'","state":"'.$info['state'].'"}';
			break;
	}
?>