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
			if($_POST['search_3'] != ''){
				$where['LIKE']['name'] = $_POST['search_3'];
			}
			$where['AND']['verifytype'] = 1;
			echo $db->count('tb_app', $where).'<{|*|}>';
			switch($_POST['search_2']){
				case '1':
					$where['ORDER'] = 'dt DESC';
					break;
				case '2':
					$where['ORDER'] = 'usecount DESC';
					break;
				case '3':
					$where['ORDER'] = 'starnum DESC';
					break;
			}
			$where['LIMIT'] = array((int)$_POST['from'], (int)$_POST['to']);
			$rs = $db->select('tb_app', '*', $where);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<li><a href="javascript:openDetailIframe2(\'detail.php?id='.$v['tbid'].'\');"><img src="../../'.$v['icon'].'"></a><a href="javascript:openDetailIframe2(\'detail.php?id='.$v['tbid'].'\');" class="app-name">'.$v['name'].'</a><span class="app-desc">'.$v['remark'].'</span><span class="star-box"><i style="width:'.($v['starnum'] * 20).'%;"></i></span><span class="star-num">'.(is_int($v['starnum']) || $v['starnum'] == 0 ? (int)$v['starnum'] : sprintf('%.1f', $v['starnum'])).'</span><span class="app-stat">'.$v['usecount'].' 人正在使用</span>';
					if(in_array($v['tbid'], $myapplist)){
						if($_POST['search_1'] == -2){
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-run-s" style="right:35px" title="打开应用">打开应用</a>';
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-remove-s" style="right:10px" title="删除应用">删除应用</a>';
						}else{
							echo '<a href="javascript:;" app_id="'.$myapplist2[$v['tbid']].'" real_app_id="'.$v['tbid'].'" app_type="'.$v['type'].'" class="btn-run-s" title="打开应用">打开应用</a>';
						}
					}else{
						echo '<a href="javascript:;" real_app_id="'.$v['tbid'].'" class="btn-add-s" title="添加应用">添加应用</a>';
					}
					echo '</li>';
				}
			}
			break;
	}
?>