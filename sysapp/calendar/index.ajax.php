<?php
	require('../../global.php');
		
	switch($_POST['ac']){
		case 'getCalendar':
			$rs = $db->select('tb_calendar', '*', array(
				'AND' => array(
					'startdt[<=]' => date('Y-m-d H:i:s', $_POST['end'] + 86400),
					'enddt[>=]' => date('Y-m-d H:i:s', $_POST['start']),
					'member_id' => session('member_id')
				)
			));
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
			$rs = $db->get('tb_calendar', '*', array(
				'AND' => array(
					'tbid' => $_POST['id'],
					'member_id' => session('member_id')
				)
			));
			if($rs){
				$rs['startdt'] = explode(' ', $rs['startdt']);
				$rs['enddt'] = explode(' ', $rs['enddt']);
				$rs['startd'] = $rs['startdt'][0];
				$rs['startt'] = $rs['startdt'][1];
				$rs['endd'] = $rs['enddt'][0];
				$rs['endt'] = $rs['enddt'][1];
				echo json_encode($rs);
			}
			break;
		case 'quick':
			switch($_POST['do']){
				case 'add':
					$db->insert('tb_calendar', array(
						'title' => $_POST['title'],
						'startdt' => $_POST['start'],
						'enddt' => $_POST['end'],
						'isallday' => $_POST['isallday'],
						'member_id' => session('member_id')
					));
					break;
				case 'drop':
					$rs = $db->get('tb_calendar', array('startdt', 'enddt'), array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					if($rs != NULL){
						$db->update('tb_calendar', array(
							'startdt' => date('Y-m-d H:i:s', strtotime($rs['startdt']) + ($_POST['dayDelta'] * 24 * 60 * 60 + $_POST['minuteDelta'] * 60)),
							'enddt' => date('Y-m-d H:i:s', strtotime($rs['enddt']) + ($_POST['dayDelta'] * 24 * 60 * 60 + $_POST['minuteDelta'] * 60))
						), array(
							'AND' => array(
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							)
						));
					}
					break;
				case 'resize':
					$enddt = $db->get('tb_calendar', 'enddt', array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					if($enddt){
						$enddt = date('Y-m-d H:i:s', strtotime($enddt) + $_POST['dayDelta'] * 24 * 60 * 60 + $_POST['minuteDelta'] * 60);
						$db->update('tb_calendar', array(
							'enddt' => $enddt
						), array(
							'AND' => array(
								'tbid' => $_POST['id'],
								'member_id' => session('member_id')
							)
						));
					}
					break;
				case 'del':
					$db->delete('tb_calendar', array(
						'AND' => array(
							'tbid' => $_POST['id'],
							'member_id' => session('member_id')
						)
					));
					break;
			}
			break;
		case 'edit':
			$data = array(
				'title' => $_POST['val_title'],
				'startdt' => $_POST['val_startd'].' '.$_POST['val_startt'],
				'enddt' => $_POST['val_endd'].' '.$_POST['val_endt'],
				'url' => $_POST['val_url'],
				'content' => $_POST['val_content'],
				'isallday' => $_POST['val_isallday'],
				'member_id' => session('member_id')
			);
			if($_POST['id'] == ''){
				$db->insert('tb_calendar', $data);
			}else{
				$db->update('tb_calendar', $data, array(
					'tbid' => $_POST['id']
				));
			}
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
	}
?>