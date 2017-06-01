<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'edit':
			$data = array(
				'icon' => $_POST['val_icon'],
				'name' => $_POST['val_name'],
				'app_category_id' => $_POST['val_app_category_id'],
				'url' => $_POST['val_url'],
				'width' => $_POST['val_width'],
				'height' => $_POST['val_height'],
				'isresize' => isset($_POST['val_isresize']) ? 1 : 0,
				'isopenmax' => isset($_POST['val_isopenmax']) ? 1 : 0,
				'isflash' => isset($_POST['val_isflash']) ? 1 : 0,
				'remark' => $_POST['val_remark']
			);
			if($_POST['id'] == ''){
				$data['type'] = $_POST['val_type'];
				$data['dt'] = date('Y-m-d H:i:s');
				$db->insert('tb_app', $data);
			}else{
				$db->update('tb_app', $data, array(
					'tbid' => $_POST['id']
				));
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'uploadImg':
			include('libs/Uploader.class.php');
			$config = array(
				'pathFormat' => 'uploads/shortcut/{yyyy}{mm}{dd}/{time}{rand:6}', //保存路径
				'allowFiles' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
				'maxSize' => 2048000 //文件大小限制，单位B
			);
			$up = new Uploader('file', $config);
			$info = $up->getFileInfo();
			echo json_encode($info);
			break;
	}
?>