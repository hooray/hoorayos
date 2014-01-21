<?php
	require('../../global.php');
	
	switch($_REQUEST['ac']){
		case 'getList':
			$where['issystem'] = 0;
			if($_POST['search_1'] != ''){
				$where['LIKE']['name'] = $_POST['search_1'];
			}
			echo $db->count('tb_app_category', $where).'<{|*|}>';
			$where['LIMIT'] = array((int)$_POST['from'], (int)$_POST['to']);
			$rs = $db->select('tb_app_category', '*', $where);
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
			$db->update('tb_app', array(
				'app_category_id' => 0
			), array(
				'app_category_id' => $_POST['categoryid']
			));
			$db->delete('tb_app_category', array(
				'tbid' => $_POST['categoryid']
			));
			break;
	}
?>