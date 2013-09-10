<?php
	require('../../global.php');
		
	switch($ac){
		case 'getList':
			$orderby = 'dt desc limit '.$from.','.$to;
			if($search_1 != ''){
				$sqlwhere[] = 'name like "%'.$search_1.'%"';
			}
			if($search_2 != ''){
				$sqlwhere[] = 'kindid = '.$search_2;
			}
			if($search_3 != ''){
				$sqlwhere[] = 'type = "'.$search_3.'"';
			}
			if($search_4 == 1){
				$sqlwhere[] = 'verifytype = 1';
			}else{
				$sqlwhere[] = 'verifytype = 2';
			}
			$c = $db->select(0, 2, 'tb_app', 'tbid', $sqlwhere);
			echo $c.'<{|*|}>';
			$rs = $db->select(0, 0, 'tb_app', '*', $sqlwhere, $orderby);
			if($rs != NULL){
				foreach($rs as $v){
					echo '<tr class="list-bd">';
						echo '<td style="text-align:left;padding-left:15px"><img src="../../'.$v['icon'].'" alt="'.$v['name'].'" class="appicon"><span class="appname">'.$v['name'].'</span></td>';
						echo '<td>'.($v['type'] == 'app' ? '窗口' : '挂件').'</td>';
						echo '<td>'.$apptype[$v['kindid']-1]['name'].'</td>';
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
			$db->delete(0, 0, 'tb_app', 'and tbid='.$appid);
			break;
		case 'recommend':
			$db->update(0, 0, 'tb_app', 'isrecommend = 0');
			$db->update(0, 0, 'tb_app', 'isrecommend = 1', 'and tbid = '.$appid);
			break;
	}
?>