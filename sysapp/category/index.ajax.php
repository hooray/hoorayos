<?php
	require('../../global.php');

	switch($_REQUEST['ac']){
		case 'getList':
			$where['issystem'] = 0;
			if($_GET['search'] != ''){
	            $where['AND']['name[~]'] = $_GET['search'];
	        }
	        if($_GET['sort'] != '' && $_GET['order'] != ''){
	            $where['ORDER'][$_GET['sort']] = strtoupper($_GET['order']);
	        }
			$echo['total'] = $db->count('tb_app_category', '*', $where);
			$where['LIMIT'] = array($_GET['offset'], $_GET['limit']);
			$echo['rows'] = array();
			$row = $db->select('tb_app_category', '*', $where);
			if($row != NULL){
				foreach($row as $v){
					$tmp['name'] = $v['name'];
					$tmp['do'] = '
						<a href="javascript:openDetailIframe(\'detail.php?categoryid='.$v['tbid'].'\');" class="btn btn-primary btn-xs">编辑</a>
						<a href="javascript:;" class="btn btn-danger btn-xs do-del" data-id="'.$v['tbid'].'" data-name="'.$v['name'].'">删除</a>
					';
					$echo['rows'][] = $tmp;
		            unset($tmp);
				}
			}
			echo json_encode($echo);
			break;
		case 'del':
			$db->update('tb_app', array(
				'app_category_id' => 0
			), array(
				'app_category_id' => $_POST['id']
			));
			$db->delete('tb_app_category', array(
				'tbid' => $_POST['id']
			));
			break;
	}
?>
