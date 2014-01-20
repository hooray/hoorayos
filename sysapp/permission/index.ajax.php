<?php
	require('../../global.php');
	
	switch($_POST['ac']){
		case 'getList':
			$where = array();
			if($_POST['search_1'] != ''){
				$where['LIKE']['name'] = $_POST['search_1'];
			}
			echo $db->count('tb_permission', $where).'<{|*|}>';
			$where['LIMIT'] = array((int)$_POST['from'], (int)$_POST['to']);
			$rs = $db->select('tb_permission', '*', $where);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td>'.$v['tbid'].'</td>';
						echo '<td style="text-align:left;padding-left:15px">'.$v['name'].'</td>';
						echo '<td><a href="javascript:openDetailIframe(\'detail.php?permissionid='.$v['tbid'].'\');" class="btn btn-mini btn-link">编辑</a><a href="javascript:;" class="btn btn-mini btn-link do-del" permissionid="'.$v['tbid'].'">删除</a></td>';
					echo '</tr>';
				}
			}
			break;
		case 'del':
			$db->delete('tb_permission', array(
				'tbid' => $_POST['permissionid']
			));
			break;
	}
?>