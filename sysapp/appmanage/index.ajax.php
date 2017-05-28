<?php
	require('../../global.php');

	switch($_REQUEST['ac']){
		case 'getList':
			$appcategory = $db->select('tb_app_category', '*');
			foreach($appcategory as $ac){
				$category[$ac['tbid']] = $ac['name'];
			}
			$where = array();
			if($_GET['search'] != ''){
				$where['AND']['name[~]'] = $_GET['search'];
			}
			if($_GET['app_category_id'] != ''){
				$where['AND']['app_category_id'] = $_GET['app_category_id'];
			}
			if($_GET['type'] != ''){
				$where['AND']['type'] = $_GET['type'];
			}
			if($_GET['sort'] != '' && $_GET['order'] != ''){
	            $where['ORDER'][$_GET['sort']] = strtoupper($_GET['order']);
	        }
			$echo['total'] = $db->count('tb_app', '*', $where);
			$where['LIMIT'] = array($_GET['offset'], $_GET['limit']);
			$echo['rows'] = array();
			$row = $db->select('tb_app', '*', $where);
			if($row != NULL){
				foreach($row as $v){
					$tmp['icon'] = '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" class="appicon">';
					$tmp['name'] = $v['name'];
					$tmp['type'] = $v['type'] == 'window' ? '窗口' : '挂件';
					$tmp['app_category_id'] = $v['app_category_id'] == 0 ? '未分类' : $category[$v['app_category_id']];
					$tmp['usecount'] = $v['usecount'];
					$tmp['do'] = '';
					if($v['isrecommend'] == 1){
						$tmp['do'] .= ' <a href="javascript:;" class="btn btn-link btn-xs disabled">今日推荐</a> ';
					}else{
						$tmp['do'] .= ' <a href="javascript:;" class="btn btn-default btn-xs do-recommend" data-id="'.$v['tbid'].'">设为今日推荐</a> ';
					}
					$tmp['do'] .= ' <a href="javascript:openDetailIframe(\'detail.php?appid='.$v['tbid'].'\');" class="btn btn-primary btn-xs">编辑</a> ';
					$tmp['do'] .= ' <a href="javascript:;" class="btn btn-danger btn-xs do-del" data-id="'.$v['tbid'].'" data-name="'.$v['name'].'">删除</a> ';
					$echo['rows'][] = $tmp;
		            unset($tmp);
				}
			}
			echo json_encode($echo);
			break;
		case 'del':
			$db->delete('tb_app', array(
				'tbid' => $_POST['id']
			));
			break;
		case 'recommend':
			$db->update('tb_app', array(
				'isrecommend' => 0
			), array(
				'isrecommend' => 1
			));
			$db->update('tb_app', array(
				'isrecommend' => 1
			), array(
				'tbid' => $_POST['id']
			));
			break;
	}
?>
