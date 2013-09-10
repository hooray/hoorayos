<div class="title">
	<ul>
		<?php
			echo $global_title == 'index' ? '<li class="focus">基本信息</li>' : '<li><a href="index.php">基本信息</a></li>';
			echo $global_title == 'avatar' ? '<li class="focus">修改头像</li>' : '<li><a href="avatar.php">修改头像</a></li>';
			echo $global_title == 'bind' ? '<li class="focus">社区绑定</li>' : '<li><a href="bind.php">社区绑定</a></li>';
			echo $global_title == 'security' ? '<li class="focus">账号安全</li>' : '<li><a href="security.php">账号安全</a></li>';
		?>
	</ul>
</div>
