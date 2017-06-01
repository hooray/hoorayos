<?php
	require('../../global.php');

	switch($_REQUEST['ac']){
		case 'getList':
			$myapplist = $db->select('tb_member_app', 'realid', array(
				'member_id' => session('member_id')
			));
			$myapplist2 = array();
			foreach($db->select('tb_member_app', array('tbid', 'realid'), array(
				'AND' => array(
					'member_id' => session('member_id'),
					'realid[!]' => null
				)
			)) as $value){
				$myapplist2[$value['realid']] = $value['tbid'];
			}
			$where = array();
			if(!checkLogin() || !checkAdmin()){
				$where['AND']['app_category_id'] = $db->select('tb_app_category', 'tbid', array(
					'issystem' => 0
				));
			}
			if($_POST['search_1'] != 0){
				//查询挂件应用
				if($_POST['search_1'] == -1){
					$where['AND']['type'] = 'widget';
				}
				//查询已安装的应用
				else if($_POST['search_1'] == -2){
					if($myapplist != NULL){
						$where['AND']['tbid'] = $myapplist;
					}else{
						$where['AND']['tbid'] = 0;
					}
				}
				//根据所选应用分类查询应用
				else{
					$where['AND']['app_category_id'] = $_POST['search_1'];
					//如果是系统分类，则只显示可添加的系统应用
					if($db->get('tb_app_category', 'issystem', array(
						'tbid' => $_POST['search_1']
					))){
						if(checkAdmin()){
							$permission_id = $db->get('tb_member', 'permission_id', array(
								'tbid' => session('member_id')
							));
							if($permission_id != ''){
								$apps_id = $db->get('tb_permission', 'apps_id', array(
									'tbid' => $permission_id
								));
								if($apps_id != ''){
									$where['AND']['tbid'] = explode(',', $apps_id);
								}
							}else{
								$where['AND']['tbid'] = NULL;
							}
						}else{
							$where['AND']['tbid'] = NULL;
						}
					}
				}
			}
			if($_POST['search_2'] != ''){
				$where['AND']['name[~]'] = $_POST['search_2'];
			}
			echo $db->count('tb_app', $where).'<{|*|}>';
			$where['LIMIT'] = [(int)$_POST['from'], (int)$_POST['to']];
			$rs = $db->select('tb_app', '*', $where);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<div class="app">';
						echo '<img src="../../'.$v['icon'].'">';
						echo '<div class="title">'.$v['name'].'</div>';
						echo '<div class="btns">';
							echo '<div class="btn-group">';
								if(in_array($v['tbid'], $myapplist)){
									if($_POST['search_1'] == -2){
										echo '<button type="button" class="btn btn-default btn-xs btn-run" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" title="打开应用"><i class="fa fa-share"></i> 打开应用</button>';
										echo '<button type="button" class="btn btn-danger btn-xs btn-remove" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" title="删除应用"><i class="fa fa-remove"></i></button>';
									}else{
										echo '<button type="button" class="btn btn-default btn-xs btn-run" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" title="打开应用"><i class="fa fa-share"></i> 打开应用</button>';
									}
								}else{
									echo '<button type="button" class="btn btn-primary btn-xs btn-add" real_app_id="'.$v['tbid'].'" title="添加应用"><i class="fa fa-plus"></i> 添加应用</button>';
								}
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}
			}
			break;
	}
?>