<?php
	require('../../global.php');
	
	switch($ac){
		case 'uploadImg':
			//先验证图片是否超过6张，否则不允许上传
			$imgCount = $db->select(0, 2, 'tb_pwallpaper', '*', 'and member_id = '.session('member_id'));
			if($imgCount < 6){
				include('libs/Uploader.class.php');
				$config = array(
					"savePath" => 'uploads/member/'.session('member_id').'/wallpaper/', //保存路径
					"allowFiles" => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
					"maxSize" => 1000 //文件大小限制，单位KB
				);
				$up = new Uploader('xfile', $config);
				$info = $up->getFileInfo();
				//如果上传成功，则进行缩略图上传
				if($info["state"] == 'SUCCESS'){
					//获取原图宽高
					$source = file_get_contents($info['url']);
					$image = imagecreatefromstring($source);
					$w = imagesx($image);
					$h = imagesy($image);
					//缩略图路径
					$str_array = explode('/', $info['url']);
					$str_array[count($str_array) - 1] = 's_'.$str_array[count($str_array) - 1];
					$surl = implode('/', $str_array);
					//缩略图上传
					imageResize($source, $surl, 150, 105);
					//上传完毕后，添加数据库记录
					$tbid = $db->insert(0, 2, 'tb_pwallpaper', 'url="'.$info['url'].'", width='.$w.', height='.$h.', member_id = '.session('member_id'));
				}
				echo '{"tbid":"'.$tbid.'","surl":"'.$surl.'","url":"'.$info['url'].'","fileType":"'.$info['type'].'","original":"'.$info['originalName'].'","state":"'.$info['state'].'"}';
			}else{
				echo '{"state":"您已经上传满6张壁纸，可以删除之后再进行上传"}';
			}
			break;
		case 'del':
			$db->delete(0, 0, 'tb_pwallpaper', 'and tbid = '.$id.' and member_id = '.session('member_id'));
			break;
	}
?>