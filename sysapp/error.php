<?php
	require('../global.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>啊，出错了</title>
<link rel="stylesheet" href="../img/ui/globle.css">
<link rel="stylesheet" href="../js/HoorayLibs/hooraylibs.css">
<link rel="stylesheet" href="../img/ui/sys.css">
</head>

<body>
<script src="../js/jquery-1.8.3.min.js"></script>
<script src="../js/HoorayLibs/hooraylibs.js"></script>
<script type="text/javascript">
$(function(){
	<?php if($code == $errorcode['noLogin']){ ?>
		window.top.HROS.CONFIG.memberID = 0;
		window.top.$.dialog({
			title: '温馨提示',
			icon: 'warning',
			content: '您尚未登录，为了更好的操作，是否登录？',
			ok: function(){
				window.top.HROS.base.login();
			}
		});
	<?php }elseif($code == $errorcode['noAdmin']){ ?>
		window.top.ZENG.msgbox.show("对不起，您不是管理员！", 1, 2000);
	<?php }elseif($code == $errorcode['noPermissions']){ ?>
		window.top.ZENG.msgbox.show("对不起，您没有权限操作！", 1, 2000);
	<?php } ?>
});
</script>
</body>
</html>