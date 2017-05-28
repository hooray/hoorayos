<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'edit':
			$db->update('tb_member_app', array(
				'icon' => $_POST['val_icon'],
				'name' => $_POST['val_name'],
				'width' => $_POST['val_width'],
				'height' => $_POST['val_height'],
				'isresize' => $_POST['val_isresize'],
				'isopenmax' => $_POST['val_isopenmax'],
				'isflash' => $_POST['val_isflash']
			), array(
				'AND' => array(
					'tbid' => $_POST['id'],
					'member_id' => session('member_id')
				)
			));
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'uploadImg':
			include('libs/Uploader.class.php');
			$config = array(
				'pathFormat' => 'uploads/member/'.session('member_id').'/shortcut/{yyyy}{mm}{dd}/{time}{rand:6}', //保存路径
				'allowFiles' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
				'maxSize' => 2048000 //文件大小限制，单位B
			);
			$up = new Uploader('file', $config);
			$info = $up->getFileInfo();
			echo json_encode($info);
			break;
	}
?>