<?php
	require('../../global.php');
		
	switch($_REQUEST['ac']){
		case 'getCalendar':
			$rs = $db->select('tb_calendar', '*', [
				'AND' => [
					'startdt[<=]' => date('Y-m-d H:i:s', $_POST['end'] + 86400),
					'enddt[>=]' => date('Y-m-d H:i:s', $_POST['start']),
					'member_id' => session('member_id')
				]
			]);
			$arr = array();
			foreach($rs as $v){
				$tmp['id'] = $v['tbid'];
				$tmp['title'] = $v['title'];
				$tmp['start'] = $v['startdt'];
				$tmp['end'] = $v['enddt'];
				if($v['url'] != ''){
					$tmp['url'] = $v['url'];
				}
				$tmp['allDay'] = $v['isallday'] == 1 ? true : false;
				$arr[] = $tmp;
			}
			echo json_encode($arr);
			break;
		case 'getDate':
			$rs = $db->get('tb_calendar', '*', [
				'AND' => [
					'tbid' => $_POST['id'],
					'member_id' => session('member_id')
				]
			]);
			echo json_encode($rs);
			break;
		case 'quick':
			switch($_POST['do']){
				case 'add':
					$db->insert('tb_calendar', [
						'title' => $_POST['title'],
						'startdt' => $_POST['start'],
						'enddt' => $_POST['end'],
						'isallday' => $_POST['isallday'],
						'member_id' => session('member_id')
					]);
					break;
				case 'drop':
					$rs = $db->get('tb_calendar', ['startdt', 'enddt'], [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					if($rs != NULL){
						$db->update('tb_calendar', [
							'startdt' => date('Y-m-d H:i:s', strtotime($rs['startdt']) + ($_POST['dayDelta'] / 1000 + $_POST['minuteDelta'] / 1000)),
							'enddt' => date('Y-m-d H:i:s', strtotime($rs['enddt']) + ($_POST['dayDelta'] / 1000 + $_POST['minuteDelta'] / 1000))
						], [
							'AND' => [
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							]
						]);
					}
					break;
				case 'resize':
					$enddt = $db->get('tb_calendar', 'enddt', [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					if($enddt){
						$enddt = date('Y-m-d H:i:s', strtotime($enddt) + ($_POST['dayDelta'] / 1000 + $_POST['minuteDelta'] / 1000));
						$db->update('tb_calendar', [
							'enddt' => $enddt
						], [
							'AND' => [
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							]
						]);
					}
					break;
				case 'del':
					$db->delete('tb_calendar', [
						'AND' => [
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						]
					]);
					break;
			}
			break;
		case 'edit':
			$data = [
				'title' => $_POST['val_title'],
				'startdt' => $_POST['val_startdt'],
				'enddt' => $_POST['val_enddt'],
				'url' => $_POST['val_url'],
				'content' => $_POST['val_content'],
				'isallday' => $_POST['val_isallday'],
				'member_id' => session('member_id')
			];
			if($_POST['id'] == ''){
				$db->insert('tb_calendar', $data);
			}else{
				$db->update('tb_calendar', $data, [
					'tbid' => $_POST['id']
				]);
			}
			echo json_encode([
				'info' => '',
				'status' => 'y'
			]);
			break;
	}
?>