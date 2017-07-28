<?php
	require('../global.php');
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>啊，出错了</title>
	<link rel="stylesheet" href="../static/plugins/HoorayLibs/hooraylibs.css">
	<link rel="stylesheet" href="../static/css/sys.css">
</head>
<body>
	<script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
	<script src="../static/plugins/HoorayLibs/hooraylibs.js"></script>
	<script type="text/javascript">
	$(function(){
		<?php if($_GET['code'] == $errorcode['noLogin']){ ?>
			window.parent.HROS.base.loginDialog();
		<?php }elseif($_GET['code'] == $errorcode['noAdmin']){ ?>
			window.parent.ZENG.msgbox.show("对不起，您不是管理员！", 1, 2000);
		<?php }elseif($_GET['code'] == $errorcode['noPermissions']){ ?>
			window.parent.ZENG.msgbox.show("对不起，您没有权限操作！", 1, 2000);
		<?php } ?>
	});
	</script>
</body>
</html>