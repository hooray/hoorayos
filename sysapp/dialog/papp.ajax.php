<?php
	require('../../global.php');
	
	switch($_POST['ac']){
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
			if($id == ''){
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
	}
?>