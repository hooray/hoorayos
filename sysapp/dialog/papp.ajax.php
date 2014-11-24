<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'edit':
			$data = array(
				'type' => $_POST['val_type'],
				'icon' => $_POST['val_icon'],
				'name' => $_POST['val_name'],
				'url' => $_POST['val_url'],
				'width' => $_POST['val_width'],
				'height' => $_POST['val_height'],
				'isresize' => $_POST['val_isresize'],
				'isopenmax' => $_POST['val_isopenmax'],
				'isflash' => $_POST['val_isflash']
			);
			if($_POST['id'] == ''){
				$data['desk'] = $_POST['desk'];
				addApp($data);
			}else{
				$db->update('tb_member_app', $data, array(
					'AND' => array(
						'tbid' => $_POST['id'],
						'member_id' => session('member_id')
					)
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
				'pathFormat' => 'uploads/member/'.session('member_id').'/shortcut/{yyyy}{mm}{dd}/{time}{rand:6}', //保存路径
				'allowFiles' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
				'maxSize' => 2048000 //文件大小限制，单位B
			);
			$up = new Uploader('file', $config);
			$info = $up->getFileInfo();
			echo '{"url":"'.$info['url'].'","fileType":"'.$info['type'].'","original":"'.$info['originalName'].'","state":"'.$info['state'].'"}';
			break;
	}
?>