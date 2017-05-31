<?php
	require('../../global.php');

	switch($_REQUEST['ac']){
		case 'getList':
			$where = array();
			if($_GET['search'] != ''){
				$where['AND']['username[~]'] = $_GET['search'];
			}
			if($_GET['type'] != ''){
				$where['AND']['type'] = $_GET['type'];
			}
			if($_GET['sort'] != '' && $_GET['order'] != ''){
	            $where['ORDER'][$_GET['sort']] = strtoupper($_GET['order']);
	        }
			$echo['total'] = $db->count('tb_member', '*', $where);
			$where['LIMIT'] = array($_GET['offset'], $_GET['limit']);
			$echo['rows'] = array();
			$row = $db->select('tb_member', '*', $where);
			if($row != NULL){
				foreach($row as $v){
					$tmp['avatar'] = '<img src="../../'.getAvatar($v['tbid'], 'n').'" alt="'.$v['username'].'" class="membericon">';
					$tmp['username'] = $v['username'];
					$tmp['type'] = $v['type'] == 1 ? '管理员' : '普通会员';
					$tmp['do'] = '
						<a href="javascript:openDetailIframe(\'detail.php?memberid='.$v['tbid'].'\');" class="btn btn-primary btn-xs">编辑</a>
						<a href="javascript:;" class="btn btn-danger btn-xs do-del" data-id="'.$v['tbid'].'" data-username="'.$v['username'].'">删除</a>
					';
					$echo['rows'][] = $tmp;
		            unset($tmp);
				}
			}
			echo json_encode($echo);
			break;
		case 'del':
			$db->delete('tb_member', array(
				'tbid' => $_POST['id']
			));
			break;
	}
?>