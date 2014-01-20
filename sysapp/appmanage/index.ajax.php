<?php
	require('../../global.php');
		
	switch($_POST['ac']){
		case 'getList':
			$appcategory = $db->select('tb_app_category', '*');
			foreach($appcategory as $ac){
				$category[$ac['tbid']] = $ac['name'];
			}
			$where = array();
			if($_POST['search_1'] != ''){
				$where['LIKE']['name'] = $_POST['search_1'];
			}
			if($_POST['search_2'] != ''){
				$where['AND']['app_category_id'] = $_POST['search_2'];
			}
			if($_POST['search_3'] != ''){
				$where['AND']['type'] = $_POST['search_3'];
			}
			$where['AND']['verifytype'] = $_POST['search_4'] == 1 ? 1 : 2;
			echo $db->count('tb_app', $where).'<{|*|}>';
			$where['LIMIT'] = array((int)$_POST['from'], (int)$_POST['to']);
			$rs = $db->select('tb_app', '*', $where);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px"><img src="../../'.$v['icon'].'" alt="'.$v['name'].'" class="appicon"><span class="appname">'.$v['name'].'</span></td>';
						echo '<td>'.($v['type'] == 'window' ? '窗口' : '挂件').'</td>';
						echo '<td>'.($v['app_category_id'] == 0 ? '未分类' : $category[$v['app_category_id']]).'</td>';
						echo '<td>'.$v['usecount'].'</td>';
						echo '<td>';
							if($v['verifytype'] == 1){
								if($v['isrecommend'] == 1){
									echo '<a href="javascript:;" class="btn btn-mini btn-link">今日推荐</a>';
								}else{
									echo '<a href="javascript:;" class="btn btn-mini btn-link do-recommend" appid="'.$v['tbid'].'">设为今日推荐</a>';
								}
								echo '<a href="javascript:openDetailIframe(\'detail.php?appid='.$v['tbid'].'\');" class="btn btn-mini btn-link">编辑</a>';
							}else{
								echo '<a href="javascript:openDetailIframe(\'detail.php?appid='.$v['tbid'].'\');" class="btn btn-mini btn-link">查看详情</a>';
							}
							echo '<a href="javascript:;" class="btn btn-mini btn-link do-del" appid="'.$v['tbid'].'">删除</a>';
						echo '</td>';
					echo '</tr>';
				}
			}
			break;
		case 'del':
			$db->delete('tb_app', array(
				'tbid' => $_POST['appid']
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
				'tbid' => $_POST['appid']
			));
			break;
	}
?>