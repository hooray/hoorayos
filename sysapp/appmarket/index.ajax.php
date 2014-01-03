<?php
	require('../../global.php');
	
	switch($ac){
		case 'getList':
			$myapplist = array(0);
			$myapplist2 = array();
			foreach($db->select(0, 0, 'tb_member_app', 'tbid, realid', 'and member_id = '.session('member_id')) as $value){
				if($value['realid'] != ''){
					$myapplist[] = $value['realid'];
					$myapplist2[$value['realid']] = $value['tbid'];
				}
			}
			$mytype = $db->select(0, 1, 'tb_member', 'type', 'and tbid = '.session('member_id'));
			if($mytype['type'] != 1){
				$category = $db->select(0, 1, 'tb_app_category', 'group_concat(tbid) as tbids', 'and issystem = 0');
				$sqlwhere[] = 'app_category_id in('.$category['tbids'].')';
			}
			if((int)$search_1 != 0){
				if((int)$search_1 == -1){
					$sqlwhere[] = 'type = "widget"';
				}else if((int)$search_1 == -2){
					if($myapplist != NULL){
						$sqlwhere[] = 'tbid in('.implode(',', $myapplist).')';
					}
				}else{
					$sqlwhere[] = 'app_category_id = '.(int)$search_1;
				}
			}
			if(trim($search_3) != ''){
				$sqlwhere[] = 'name like "%'.trim($search_3).'%"';
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
			$orderby .= ' limit '.(int)$from.','.(int)$to;
			$c = $db->select(0, 2, 'tb_app', 'tbid', $sqlwhere);
			echo $c.'<{|*|}>';
			$rs = $db->select(0, 0, 'tb_app', '*', $sqlwhere, $orderby);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<li><a href="javascript:openDetailIframe2(\'detail.php?id='.$v['tbid'].'\');"><img src="../../'.$v['icon'].'"></a><a href="javascript:openDetailIframe2(\'detail.php?id='.$v['tbid'].'\');" class="app-name">'.$v['name'].'</a><span class="app-desc">'.$v['remark'].'</span><span class="star-box"><i style="width:'.($v['starnum'] * 20).'%;"></i></span><span class="star-num">'.(is_int($v['starnum']) || $v['starnum'] == 0 ? (int)$v['starnum'] : sprintf('%.1f', $v['starnum'])).'</span><span class="app-stat">'.$v['usecount'].' 人正在使用</span>';
					if(in_array($v['tbid'], $myapplist)){
						if($search_1 == -2){
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-run-s" style="right:35px" title="打开应用">打开应用</a>';
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-remove-s" style="right:10px" title="删除应用">删除应用</a>';
						}else{
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-run-s" title="打开应用">打开应用</a>';
						}
					}else{
						echo '<a href="javascript:;" real_app_id="'.$v['tbid'].'" class="btn-add-s" title="添加应用">添加应用</a>';
					}
					echo '</li>';
				}
			}
			break;
	}
?>