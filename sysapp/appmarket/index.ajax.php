<?php
	require('../../global.php');
	
	switch($ac){
		case 'getList':
			$mytype = $db->select(0, 1, 'tb_member', 'type', 'and tbid = '.session('member_id'));
			$myapplist = array();
			$myapplist2 = array();
			foreach($db->select(0, 0, 'tb_member_app', 'tbid, realid', 'and member_id = '.session('member_id')) as $value){
				if($value['realid'] != ''){
					$myapplist[] = $value['realid'];
					$myapplist2[$value['realid']] = $value['tbid'];
				}
			}
			if($search_1 == -1){
				if($myapplist != NULL){
					$sqlwhere[] = 'tbid in('.implode(',', $myapplist).')';
				}
			}else{
				if($search_1 != 0){
					if($search_1 == 1 && $mytype['type'] == 1){
						$sqlwhere[] = 'kindid = '.$search_1;
					}else{
						$sqlwhere[] = 'kindid = '.$search_1;
					}
				}else if($search_1 == 0 && $mytype['type'] == 0){
					$sqlwhere[] = 'kindid != 1';
				}
			}
			if($search_3 != ''){
				$sqlwhere[] = 'name like "%'.$search_3.'%"';
			}
			$sqlwhere[] = 'verifytype = 1';
			switch($search_2){
				case '1':
					$orderby = 'dt desc';
					break;
				case '2':
					$orderby = 'usecount desc';
					break;
				case '3':
					$orderby = 'starnum desc';
					break;
			}
			$orderby .= ' limit '.$from.','.$to;
			$c = $db->select(0, 2, 'tb_app', 'tbid', $sqlwhere);
			echo $c.'<{|*|}>';
			$rs = $db->select(0, 0, 'tb_app', '*', $sqlwhere, $orderby);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<li><a href="javascript:openDetailIframe2(\'detail.php?id='.$v['tbid'].'\');"><img src="../../'.$v['icon'].'"></a><a href="javascript:openDetailIframe2(\'detail.php?id='.$v['tbid'].'\');"><span class="app-name">'.$v['name'].'</span></a><span class="app-desc">'.$v['remark'].'</span><span class="star-box"><i style="width:'.($v['starnum']*20).'%;"></i></span><span class="star-num">'.floor($v['starnum']).'</span><span class="app-stat">'.strip_tags($v['usecount']).' 人正在使用</span>';
					if(in_array($v['tbid'], $myapplist)){
						if($search_1 == -1){
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-run-s" style="right:35px">打开应用</a>';
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-remove-s" style="right:10px">删除应用</a>';
						}else{
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-run-s">打开应用</a>';
						}
					}else{
						echo '<a href="javascript:;" real_app_id="'.$v['tbid'].'" class="btn-add-s">添加应用</a>';
					}
					echo '</li>';
				}
			}
			break;
	}
?>