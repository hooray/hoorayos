<?php
	require('../../global.php');
	include('libs/Uploader.class.php');
	$fileType = array_keys($uploadFileType);
	foreach($fileType as &$v){
		$v = '.'.$v;
	}
	$config = array(
		'savePath' => 'uploads/member/'.session('member_id').'/file/', //保存路径
		'allowFiles' => $fileType, //文件允许格式
		'maxSize' => $uploadFileMaxSize * 1024 //文件大小限制，单位KB
	);
	$up = new Uploader('file', $config);
	$info = $up->getFileInfo();
	echo json_encode($info);
	//echo '{"originalName":"'.$info['originalName'].'","name":"'.$info['name'].'","url":"'.$info['url'].'","size":"'.$info['size'].'","type":"'.$info['type'].'","state":"'.$info['state'].'"}';
?>