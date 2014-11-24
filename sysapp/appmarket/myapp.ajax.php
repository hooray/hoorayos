<?php
	require('../../global.php');
		
	switch($_REQUEST['ac']){
		case 'getList':
			foreach($db->select('tb_app_category', '*') as $ac){
				$category[$ac['tbid']] = $ac['name'];
			}
			$where['AND']['member_id'] = session('member_id');
			if($_POST['search_1'] != ''){
				$where['LIKE']['name'] = $_POST['search_1'];
			}
			if($_POST['search_2'] != ''){
				$where['AND']['app_category_id'] = $_POST['search_2'];
			}
			if($_POST['search_3'] != ''){
				$where['AND']['type'] = $_POST['search_3'];
			}
			$where['AND']['verifytype'] = $_POST['search_4'];
			echo $db->count('tb_app', $where).'<{|*|}>';
			$where['LIMIT'] = array((int)$_POST['from'], (int)$_POST['to']);
			$rs = $db->select('tb_app', '*', $where);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px"><img src="../../'.$v['icon'].'" alt="'.$v['name'].'" class="appicon"><span class="appname">'.$v['name'].'</span></td>';
						echo '<td>'.($v['type'] == 'window' ? '窗口' : '挂件').'</td>';
						echo '<td>'.($v['app_category_id'] == 0 ? '未分类' : $category[$v['app_category_id']]).'</td>';
						echo '<td>'.$v['usecount'].'</td>';
						echo '<td>';
							echo '<a href="javascript:openDetailIframe(\'myapp.edit.php?appid='.$v['tbid'].'\');" class="btn btn-mini btn-link">详情</a>';
						echo '</td>';
					echo '</tr>';
				}
			}
			break;
		case 'edit':
			$data = array(
				'icon' => $_POST['val_icon'],
				'name' => $_POST['val_name'],
				'app_category_id' => $_POST['val_app_category_id'],
				'url' => $_POST['val_url'],
				'width' => $_POST['val_width'],
				'height' => $_POST['val_height'],
				'isresize' => $_POST['val_isresize'],
				'isopenmax' => $_POST['val_isopenmax'],
				'issetbar' => 1,
				'isflash' => $_POST['val_isflash'],
				'remark' => $_POST['val_remark']
			);
			if($_POST['id'] == ''){
				$data['type'] = $_POST['val_type'];
				$data['dt'] = date('Y-m-d H:i:s');
				$data['verifytype'] = 0;
				$data['member_id'] = session('member_id');
				$db->insert('tb_app', $data);
			}else{
				$data['verifytype'] = 2;
				$db->update('tb_app', $data, array(
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
		case 'del':
			$db->delete('tb_app', array(
				'AND' => array(
					'tbid' => $_POST['appid'],
					'member_id' =>session('member_id')
				)
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
			echo '{"url":"'.$info['url'].'","fileType":"'.$info['type'].'","original":"'.$info['originalName'].'","state":"'.$info['state'].'"}';
			break;
	}
?>