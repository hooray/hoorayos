<?php
	require('../../global.php');
		
	switch($ac){
		case 'getList':
			$orderby = 'dt desc limit '.$from.','.$to;
			$sqlwhere[] = 'member_id = '.session('member_id');
			if($search_1 != ''){
				$sqlwhere[] = 'name like "%'.$search_1.'%"';
			}
			if($search_2 != ''){
				$sqlwhere[] = 'kindid = '.$search_2;
			}
			if($search_3 != ''){
				$sqlwhere[] = 'type = "'.$search_3.'"';
			}
			$sqlwhere[] = 'verifytype = '.$search_4;
			$c = $db->select(0, 2, 'tb_app', 'tbid', $sqlwhere);
			echo $c.'<{|*|}>';
			$rs = $db->select(0, 0, 'tb_app', '*', $sqlwhere, $orderby);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px"><img src="../../'.$v['icon'].'" alt="'.$v['name'].'" class="appicon"><span class="appname">'.$v['name'].'</span></td>';
						echo '<td>'.($v['type'] == 'app' ? '窗口' : '挂件').'</td>';
						echo '<td>'.$apptype[$v['kindid']-1]['name'].'</td>';
						echo '<td>'.$v['usecount'].'</td>';
						echo '<td>';
							echo '<a href="javascript:openDetailIframe(\'myapp.edit.php?appid='.$v['tbid'].'\');" class="btn btn-mini btn-link">详情</a>';
						echo '</td>';
					echo '</tr>';
				}
			}
			break;
		case 'edit':
			$set = array(
				'icon = "'.$val_icon.'"',
				'name = "'.$val_name.'"',
				'kindid = '.$val_kindid,
				'url = "'.$val_url.'"',
				'width = '.$val_width,
				'height = '.$val_height,
				'isresize = '.$val_isresize,
				'isopenmax = '.$val_isopenmax,
				'issetbar = 1',
				'isflash = '.$val_isflash,
				'remark = "'.$val_remark.'"'
			);
			if($id == ''){
				$set[] = 'type = "'.$val_type.'"';
				$set[] = 'dt = now()';
				$set[] = 'verifytype = 0';
				$set[] = 'member_id = '.session('member_id');
				$db->insert(0, 0, 'tb_app', $set);
			}else{
				$set[] = 'verifytype = 2';
				$db->update(0, 0, 'tb_app', $set, 'and tbid = '.$id.' and member_id = '.session('member_id'));
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
	}
?>