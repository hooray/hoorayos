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
		'maxSize' => $uploadFileSize * 1024 //文件大小限制，单位KB
	);
	$up = new Uploader('file', $config);
	$info = $up->getFileInfo();
	if($info['state'] == 'SUCCESS'){
		$name = path_info($info['originalName']);
		$data = array(
			'type' => 'file',
			'icon' => $uploadFileType[$name['extension']],
			'name' => $name['filename'],
			'url' => $info['url'],
			'ext' => $name['extension'],
			'size' => $info['size'],
			'desk' => $_POST['desk']
		);
		addApp($data);
	}
	echo json_encode($info);
?>