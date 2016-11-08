<?php
	require('../../global.php');

	switch($_REQUEST['ac']){
		case 'getList':
			$where = array();
			if($_GET['search'] != ''){
	            $where['AND']['name[~]'] = $_GET['search'];
	        }
			if($_GET['sort'] != '' && $_GET['order'] != ''){
	            $where['ORDER'][$_GET['sort']] = strtoupper($_GET['order']);
	        }
			$echo['total'] = $db->count('tb_permission', '*', $where);
			$where['LIMIT'] = array($_GET['offset'], $_GET['limit']);
			$echo['rows'] = array();
			$row = $db->select('tb_permission', '*', $where);
			if($row != NULL){
				foreach($row as $v){
					$tmp['name'] = $v['name'];
					$tmp['do'] = '
						<a href="javascript:openDetailIframe(\'detail.php?permissionid='.$v['tbid'].'\');" class="btn btn-primary btn-xs">编辑</a>
						<a href="javascript:;" class="btn btn-danger btn-xs do-del" data-id="'.$v['tbid'].'" data-name="'.$v['name'].'">删除</a>
					';
					$echo['rows'][] = $tmp;
		            unset($tmp);
				}
			}
			echo json_encode($echo);
			break;
		case 'del':
			$db->delete('tb_permission', array(
				'tbid' => $_POST['id']
			));
			break;
	}
?>
