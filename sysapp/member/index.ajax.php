<?php
	require('../../global.php');
		
	switch($_REQUEST['ac']){
		case 'getList':
			$where = array();
			if($_POST['search_1'] != ''){
				$where['AND']['username[~]'] = $_POST['search_1'];
			}
			if($_POST['search_2'] != ''){
				$where['type'] = $_POST['search_2'];
			}
			echo $db->count('tb_member', $where).'<{|*|}>';
			$where['LIMIT'] = array((int)$_POST['from'], (int)$_POST['to']);
			$rs = $db->select('tb_member', '*', $where);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px"><img src="../../'.getAvatar($v['tbid']).'" alt="'.$v['username'].'" class="membericon"><span class="membername">'.$v['username'].'</span></td>';
						echo '<td>'.($v['type'] == 1 ? '管理员' : '普通会员').'</td>';
						echo '<td><a href="javascript:openDetailIframe(\'detail.php?memberid='.$v['tbid'].'\');" class="btn btn-link">编辑</a><a href="javascript:;" class="btn btn-link do-del" memberid="'.$v['tbid'].'">删除</a></td>';
					echo '</tr>';
				}
			}
			break;
		case 'del':
			$db->delete('tb_member', array(
				'tbid' => $_POST['memberid']
			));
			break;
	}
?>