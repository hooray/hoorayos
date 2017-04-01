<ul class="nav nav-tabs nav-title-bar">
    <li <?php if($global_title == 'index'){ ?>class="active"<?php } ?>><a <?php if($global_title != 'index'){ ?>href="index.php"<?php } ?>>基本信息</a></li>
    <li <?php if($global_title == 'avatar'){ ?>class="active"<?php } ?>><a <?php if($global_title != 'avatar'){ ?>href="avatar.php"<?php } ?>>修改头像</a></li>
    <li <?php if($global_title == 'security'){ ?>class="active"<?php } ?>><a <?php if($global_title != 'security'){ ?>href="security.php"<?php } ?>>账号安全</a></li>
</ul>