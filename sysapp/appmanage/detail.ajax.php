<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'edit':
			$_POST['val_issetbar'] = $_POST['val_kindid'] == 1 ? 0 : 1;
			$data = array(
				'icon' => $_POST['val_icon'],
				'name' => $_POST['val_name'],
				'app_category_id' => $_POST['val_app_category_id'],
				'url' => $_POST['val_url'],
				'width' => $_POST['val_width'],
				'height' => $_POST['val_height'],
				'isresize' => $_POST['val_isresize'],
				'isopenmax' => $_POST['val_isopenmax'],
				'issetbar' => $_POST['val_issetbar'],
				'isflash' => $_POST['val_isflash'],
				'remark' => $_POST['val_remark']
			);
			if($_POST['id'] == ''){
				$data['type'] = $_POST['val_type'];
				$data['dt'] = date('Y-m-d H:i:s');
				$data['verifytype'] = 1;
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
		case 'pass':
			$db->update('tb_app', array(
				'verifytype' => 1
			), array(
				'tbid' => $_POST['appid']
			));
			break;
		case 'unpass':
			$db->update('tb_app', array(
				'verifytype' => 3,
				'verifyinfo' => $_POST['info']
			), array(
				'tbid' => $_POST['appid']
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
	}
?>