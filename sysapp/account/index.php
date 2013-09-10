<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	$member = $db->select(0, 1, 'tb_member', '*', 'and tbid = '.session('member_id'));
	$global_title = 'index';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>基本信息</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
</head>

<body>
<?php include('global_title.php'); ?>
<div class="input-label">
	<label class="label-text">用户名：</label>
	<div class="label-box form-inline"><?php echo $member['username']; ?></div>
</div>
<div class="input-label">
	<label class="label-text">注册时间：</label>
	<div class="label-box form-inline"><?php echo $member['regdt']; ?></div>
</div>
<div class="input-label">
	<label class="label-text">最近一次登录时间：</label>
	<div class="label-box form-inline">
		<?php echo $member['lastlogindt']; ?>
		<a href="security.php" class="btn btn-link">如果不是你登录的，请及时修改密码</a>
	</div>
</div>
<div class="input-label">
	<label class="label-text">最近一次登录IP：</label>
	<div class="label-box form-inline"><?php echo $member['lastloginip']; ?></div>
</div>
</body>
</html>