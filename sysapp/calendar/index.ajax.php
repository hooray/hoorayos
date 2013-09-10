<?php
	require('../../global.php');
		
	switch($ac){
		case 'getCalendar':
			$start = date('Y-m-d H:i:s', $start);
			$end = date('Y-m-d H:i:s', $end + 86400);
			$sqlwhere = array(
				'(startdt <= "'.$end.'" && enddt >= "'.$start.'")',
				'member_id = '.session('member_id')
			);
			$rs = $db->select(0, 0, 'tb_calendar', '*', $sqlwhere);
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
			$rs = $db->select(0, 1, 'tb_calendar', '*', 'and tbid = '.$id.' and member_id = '.session('member_id'));
			if($rs != NULL){
				$rs['startdt'] = explode(' ', $rs['startdt']);
				$rs['startd'] = $rs['startdt'][0];
				$rs['startt'] = $rs['startdt'][1];
				$rs['enddt'] = explode(' ', $rs['enddt']);
				$rs['endd'] = $rs['enddt'][0];
				$rs['endt'] = $rs['enddt'][1];
				echo json_encode($rs);
			}
			break;
		case 'quick':
			switch($do){
				case 'add':
					$db->insert(0, 0, 'tb_calendar', array(
						"title = '$title'",
						"startdt = '$start'",
						"enddt = '$end'",
						"isallday = $isallday",
						"member_id = ".session('member_id')
					));
					break;
				case 'drop':
					$rs = $db->select(0, 1, 'tb_calendar', 'startdt, enddt', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					if($rs != NULL){
						$startdt = date('Y-m-d H:i:s', strtotime($rs['startdt']) + ($dayDelta*24*60*60 + $minuteDelta*60));
						$enddt = date('Y-m-d H:i:s', strtotime($rs['enddt']) + ($dayDelta*24*60*60 + $minuteDelta*60));
						$db->update(0, 0, 'tb_calendar', 'startdt = "'.$startdt.'", enddt = "'.$enddt.'"', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					}
					break;
				case 'resize':
					$rs = $db->select(0, 1, 'tb_calendar', 'enddt', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					if($rs != NULL){
						$enddt = date('Y-m-d H:i:s', strtotime($rs['enddt']) + ($dayDelta*24*60*60 + $minuteDelta*60));
						$db->update(0, 0, 'tb_calendar', 'enddt = "'.$enddt.'"', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					}
					break;
				case 'del':
					$db->delete(0, 0, 'tb_calendar', 'and tbid = '.$id.' and member_id = '.session('member_id'));
					break;
			}
			break;
		case 'edit':
			$set = array(
				'title = "'.$val_title.'"',
				'startdt = "'.$val_startd.' '.$val_startt.'"',
				'enddt = "'.$val_endd.' '.$val_endt.'"',
				"url = '$val_url'",
				"content = '$val_content'",
				"isallday = $val_isallday",
				"member_id = ".session('member_id')
			);
			if($id == ''){
				$db->insert(0, 0, 'tb_calendar', $set);
			}else{
				$db->update(0, 0, 'tb_calendar', $set, "and tbid = $id");
			}
			$cb['info'] = '';
			$cb['status'] = 'y';
			echo json_encode($cb);
			break;
	}
?>