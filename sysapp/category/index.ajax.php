<?php
	require('../../global.php');
	
	switch($ac){
		case 'getList':
			$orderby = 'tbid desc limit '.(int)$from.','.(int)$to;
			if($search_1 != ''){
				$sqlwhere[] = 'name like "%'.$search_1.'%"';
			}
			$c = $db->select(0, 2, 'tb_app_category', 'tbid', $sqlwhere);
			echo $c.'<{|*|}>';
			$rs = $db->select(0, 0, 'tb_app_category', '*', $sqlwhere, $orderby);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px">'.$v['name'].'</td>';
						echo '<td><a href="javascript:openDetailIframe(\'detail.php?categoryid='.$v['tbid'].'\');" class="btn btn-mini btn-link">编辑</a><a href="javascript:;" class="btn btn-mini btn-link do-del" categoryid="'.$v['tbid'].'">删除</a></td>';
					echo '</tr>';
				}
			}
			break;
		case 'del':
			$db->update(0, 0, 'tb_app', 'app_category_id = 0', 'and app_category_id = '.(int)$categoryid);
			$db->delete(0, 0, 'tb_app_category', 'and tbid = '.(int)$categoryid);
			break;
	}
?>