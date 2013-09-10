<?php
	require('../../global.php');
	
	switch($ac){
		case 'getList':
			$orderby = 'tbid desc limit '.$from.','.$to;
			if($search_1 != ''){
				$sqlwhere[] = 'name like "%'.$search_1.'%"';
			}
			$c = $db->select(0, 2, 'tb_permission', 'tbid', $sqlwhere);
			echo $c.'<{|*|}>';
			$rs = $db->select(0, 0, 'tb_permission', '*', $sqlwhere, $orderby);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px">'.$v['name'].'</td>';
						echo '<td><a href="javascript:openDetailIframe(\'detail.php?permissionid='.$v['tbid'].'\');" class="btn btn-mini btn-link">编辑</a><a href="javascript:;" class="btn btn-mini btn-link do-del" permissionid="'.$v['tbid'].'">删除</a></td>';
					echo '</tr>';
				}
			}
			break;
		case 'del':
			$db->delete(0, 0, 'tb_permission', 'and tbid = '.$permissionid);
			break;
	}
?>