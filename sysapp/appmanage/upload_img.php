<?php
	require('../../global.php');

	include('libs/Uploader.class.php');
	$config = array(
		'savePath' => 'uploads/shortcut/', //保存路径
		'allowFiles' => array('.jpg', '.jpeg', '.png', '.gif', '.bmp'), //文件允许格式
		'maxSize' => 1000 //文件大小限制，单位KB
	);
	$up = new Uploader('xfile', $config);
	$info = $up->getFileInfo();
	echo '{"url":"'.$info['url'].'","fileType":"'.$info['type'].'","original":"'.$info['originalName'].'","state":"'.$info['state'].'"}';
?>