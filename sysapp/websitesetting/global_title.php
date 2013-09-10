<div class="title">
	<ul>
		<?php
			echo $global_title == 'index' ? '<li class="focus">网站设置</li>' : '<li><a href="index.php">网站设置</a></li>';
			echo $global_title == 'defaultset' ? '<li class="focus">默认设置</li>' : '<li><a href="defaultset.php">默认设置</a></li>';
		?>
	</ul>
</div>