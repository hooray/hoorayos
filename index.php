<?php
	//引入公用文件
	require('global.php');

	cookie('fromsite', NULL);
	$setting = $db->get('tb_setting', '*');
	//检查是否登录
	if(!checkLogin()){
		//未登录用户的ID默认为0，session、cookie均要设置
		session('member_id', 0);
		cookie('memberID', 0, 3600 * 24 * 7);
		//检查cookie里用户信息是否存在
		if(cookie('userinfo') != NULL){
			$userinfo = json_decode(stripslashes(cookie('userinfo')), true);
			//用户信息存在并且开启下次自动登入，则进行登录操作
			if($userinfo['rememberMe'] == 1){
				$row = $db->get('tb_member', '*', [
					'AND' => [
						'username' => $userinfo['username'],
						'password' => sha1(authcode($userinfo['password'], 'DECODE'))
					]
				]);
				//检查登录是否成功
				if($row){
					session('member_id', $row['tbid']);
					cookie('memberID', $row['tbid'], time() + 3600 * 24 * 7);
					$db->update('tb_member', [
						'lastlogindt' => date('Y-m-d H:i:s'),
						'lastloginip' => getIp()
					], [
						'tbid' => $row['tbid']
					]);
				}
			}
		}
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<!--[if lte IE 8]>
	<meta http-equiv="refresh" content="0;url=update_your_browser.php" />
	<![endif]-->
	<meta charset="utf-8">
	<title><?php echo $setting['title']; ?></title>
	<meta name="description" content="<?php echo $setting['description']; ?>" />
	<meta name="keywords" content="<?php echo $setting['keywords']; ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="stylesheet" href="static/plugins/HoorayLibs/hooraylibs.css">
	<link rel="stylesheet" href="static/plugins/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="//at.alicdn.com/t/font_3qtd7uxnh21n61or.css">
	<link rel="stylesheet" href="static/plugins/artDialog-7.0.0/css/dialog.css">
	<link rel="stylesheet" href="static/plugins/sweetalert-1.1.1/dist/sweetalert.css">
	<link rel="stylesheet" href="libs/clicaptcha/css/captcha.css">
	<link rel="stylesheet" href="static/css/index.css">
	<link rel="stylesheet" href="static/css/skins/<?php echo getSkin(); ?>.css" id="window-skin">
	<script type="text/javascript">
	//cookie前缀，避免重名
	var cookie_prefix = '<?php echo $_CONFIG['COOKIE_PREFIX']; ?>';
	</script>
</head>
<body>
	<div class="loading"></div>
	<!-- 登录&注册 -->
	<div id="lrbox" <?php if($setting['isforcedlogin'] == 1 && !checkLogin()){ ?>style="top:0"<?php } ?> data-isforcedlogin="<?php echo $setting['isforcedlogin']; ?>">
		<div class="lrbox">
			<?php if($setting['isforcedlogin'] == 0){ ?><a href="javascript:;" class="back">取消登录</a><?php } ?>
			<div class="title"><?php echo $setting['title']; ?></div>
			<div class="loginbox">
				<div class="mask">
					<div class="mask_title">已有帐号点击登录</div>
				</div>
				<form action="login.ajax.php" method="post" id="loginForm" class="form">
					<input type="hidden" name="ac" value="login">
					<div class="avatar">
						<img src="static/img/avatar_120.jpg" id="avatar">
					</div>
					<div class="input_box">
						<input type="input" name="username" id="username" autocomplete="off" placeholder="请输入用户名" datatype="s6-18" nullmsg="请输入用户名" errormsg="用户名长度为6-18个字符">
						<div class="tip">
							<div class="text">
								<span class="arrow">◆</span>
								<p></p>
							</div>
						</div>
					</div>
					<div class="input_box">
						<input type="password" name="password" id="password" placeholder="请输入密码" datatype="*6-18" nullmsg="请输入密码" errormsg="密码长度在6~18位之间">
						<div class="tip">
							<div class="text">
								<span class="arrow">◆</span>
								<p></p>
							</div>
						</div>
					</div>
					<div class="label_box">
						<label><input type="checkbox" name="rememberMe" id="rememberMe" value="1">记住我，下次自动登录</label>
					</div>
					<div class="input_box">
						<button class="login_btn" id="submit_login_btn" type="submit">登录</button>
					</div>
					<?php if((QQ_AKEY && QQ_SKEY) || (WEIBO_AKEY && WEIBO_SKEY)){ ?>
					<div class="disanfangdenglu">
						<?php if(QQ_AKEY && QQ_SKEY){ ?>
							<a href="javascript:;" class="qq" data-type="qq" title="QQ登录"></a>
						<?php } ?>
						<?php if(WEIBO_AKEY && WEIBO_SKEY){ ?>
							<a href="javascript:;" class="weibo" data-type="weibo" title="新浪微博登录"></a>
						<?php } ?>
					</div>
					<?php } ?>
					<div class="disanfangdenglutip">
						<span class="fromsite"></span>帐号（<span class="fromsitename"></span>）登录成功，<br>请绑定你的 HoorayOS 账号。<a href="javascript:;" class="cancel">【取消】</a>
					</div>
				</form>
			</div>
			<div class="registerbox">
				<div class="mask">
					<div class="mask_title">立即注册</div>
				</div>
				<form action="login.ajax.php" method="post" id="registerForm" class="form">
					<input type="hidden" name="ac" value="register">
					<div class="maintitle">注册新用户</div>
					<div class="input_box">
						<input type="input" name="reg_username" id="reg_username" autocomplete="off" placeholder="请输入用户名" datatype="s6-18" ajaxurl="login.ajax.php?ac=checkUsername" nullmsg="请输入用户名" errormsg="用户名长度为6-18个字符">
						<div class="tip">
							<div class="text">
								<span class="arrow">◆</span>
								<p></p>
							</div>
						</div>
					</div>
					<div class="input_box">
						<input type="password" name="reg_password" id="reg_password" placeholder="请输入密码" datatype="*6-18" nullmsg="请输入密码" errormsg="密码长度在6~18位之间">
						<div class="tip">
							<div class="text">
								<span class="arrow">◆</span>
								<p></p>
							</div>
						</div>
					</div>
					<div class="input_box">
						<input type="password" name="reg_password2" id="reg_password2" placeholder="请确认密码" datatype="*6-18" recheck="reg_password" nullmsg="请再输入一次密码" errormsg="您两次输入的账号密码不一致">
						<div class="tip">
							<div class="text">
								<span class="arrow">◆</span>
								<p></p>
							</div>
						</div>
					</div>
					<div class="input_box">
						<input type="hidden" id="clicaptcha_info" name="clicaptcha_info">
						<button class="register_btn" id="submit_register_btn" type="submit">注册</button>
					</div>
					<div class="disanfangdenglutip">
						<span class="fromsite"></span>帐号（<span class="fromsitename"></span>）登录成功，<br>请绑定你的 HoorayOS 账号。<a href="javascript:;" class="cancel">【取消】</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- 桌面 -->
	<div id="desktop">
		<div id="desk">
			<div id="desk-1" class="desktop-container">
				<div class="desktop-apps-container"></div>
				<div class="scrollbar scrollbar-x"></div><div class="scrollbar scrollbar-y"></div>
			</div>
			<div id="desk-2" class="desktop-container">
				<div class="desktop-apps-container"></div>
				<div class="scrollbar scrollbar-x"></div><div class="scrollbar scrollbar-y"></div>
			</div>
			<div id="desk-3" class="desktop-container">
				<div class="desktop-apps-container"></div>
				<div class="scrollbar scrollbar-x"></div><div class="scrollbar scrollbar-y"></div>
			</div>
			<div id="desk-4" class="desktop-container">
				<div class="desktop-apps-container"></div>
				<div class="scrollbar scrollbar-x"></div><div class="scrollbar scrollbar-y"></div>
			</div>
			<div id="desk-5" class="desktop-container">
				<div class="desktop-apps-container"></div>
				<div class="scrollbar scrollbar-x"></div><div class="scrollbar scrollbar-y"></div>
			</div>
			<div id="dock-bar">
				<div id="dock-container">
					<div class="dock-middle">
						<div class="dock-applist"></div>
						<div class="dock-tools-container">
							<div class="dock-tools">
								<a href="javascript:;" class="dock-tool-setting" title="桌面设置">
									<i class="hrosfont hros-config"></i>
								</a>
								<a href="javascript:;" class="dock-tool-style" title="主题设置">
									<i class="hrosfont hros-style"></i>
								</a>
							</div>
							<div class="dock-tools">
								<a href="javascript:;" class="dock-tool-appmanage" title="全局视图，快捷键：Ctrl + ↑">
									<i class="hrosfont hros-grid"></i>
								</a>
								<a href="javascript:;" class="dock-tool-search" title="搜索，快捷键：Ctrl + F">
									<i class="hrosfont hros-search"></i>
								</a>
							</div>
							<div class="dock-startbtn">
								<a href="javascript:;" class="dock-tool-start" title="点击这里开始">
									<i class="hrosfont hros-box"></i>
								</a>
							</div>
						</div>
						<div class="dock-pagination">
							<a class="pagination pagination-1" href="javascript:;" index="1" title="切换至桌面1，快捷键：Ctrl + 1">
								<i class="hrosfont hros-dot"></i>
								<i class="hrosfont hros-num1"></i>
							</a>
							<a class="pagination pagination-2" href="javascript:;" index="2" title="切换至桌面2，快捷键：Ctrl + 2">
								<i class="hrosfont hros-dot"></i>
								<i class="hrosfont hros-num2"></i>
							</a>
							<a class="pagination pagination-3" href="javascript:;" index="3" title="切换至桌面3，快捷键：Ctrl + 3">
								<i class="hrosfont hros-dot"></i>
								<i class="hrosfont hros-num3"></i>
							</a>
							<a class="pagination pagination-4" href="javascript:;" index="4" title="切换至桌面4，快捷键：Ctrl + 4">
								<i class="hrosfont hros-dot"></i>
								<i class="hrosfont hros-num4"></i>
							</a>
							<a class="pagination pagination-5" href="javascript:;" index="5" title="切换至桌面5，快捷键：Ctrl + 5">
								<i class="hrosfont hros-dot"></i>
								<i class="hrosfont hros-num5"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="startmenu-container">
			<div class="startmenu-selfinfo">
				<a href="javascript:;" class="startmenu-feedback" title="反馈">
					<i class="fa fa-question"></i>
				</a>
				<a href="javascript:;" class="startmenu-lock" title="锁定，快捷键：Ctrl + L">
					<i class="fa fa-lock"></i>
				</a>
				<div class="startmenu-avatar"><img src="static/img/loading_24.gif"></div>
				<div class="startmenu-nick">
					<?php if(checkLogin()){ ?>
					<a href="javascript:;" title="编辑个人资料"><?php echo $db->get('tb_member', 'username', ['tbid' => session('member_id')]); ?></a>
					<?php }else{ ?>
					<a href="javascript:;">请登录</a>
					<?php } ?>
				</div>
			</div>
			<ul class="startmenu">
				<li><a href="javascript:;" class="about">关于 HoorayOS</a></li>
			</ul>
			<div class="startmenu-exit"><a href="javascript:;" title="注销当前用户"></a></div>
		</div>
		<div id="task-bar-bg1"></div>
		<div id="task-bar-bg2"></div>
		<div id="task-bar">
			<div id="task-prev"><a href="javascript:;" id="task-prev-btn" hidefocus="true"></a></div>
			<div id="task-content">
				<div id="task-content-inner"></div>
			</div>
			<div id="task-next"><a href="javascript:;" id="task-next-btn" hidefocus="true"></a></div>
		</div>
		<div id="search-bar">
			<input id="pageletSearchInput" class="mousetrap" placeholder="搜索应用...">
			<button type="button" id="pageletSearchButton" title="搜索"><i class="fa fa-search"></i></button>
		</div>
		<div id="search-suggest">
			<ul class="resultBox"></ul>
			<div class="resultList openAppMarket"><a href="javascript:;"><div>去应用市场搜搜...</div></a></div>
		</div>
	</div>
	<!-- 全局视图 -->
	<div id="appmanage">
		<a class="amg_close" href="javascript:;"></a>
		<div id="amg_dock_container"></div>
		<div id="amg_folder_container">
			<div class="folderItem">
				<div class="folder_bg folder_bg1"></div>
				<div class="folderOuter">
					<div class="folderInner" desk="1"></div>
					<div class="scrollBar"></div>
				</div>
			</div>
			<div class="folderItem">
				<div class="folder_bg folder_bg2"></div>
				<div class="folderOuter">
					<div class="folderInner" desk="2"></div>
					<div class="scrollBar"></div>
				</div>
			</div>
			<div class="folderItem">
				<div class="folder_bg folder_bg3"></div>
				<div class="folderOuter">
					<div class="folderInner" desk="3"></div>
					<div class="scrollBar"></div>
				</div>
			</div>
			<div class="folderItem">
				<div class="folder_bg folder_bg4"></div>
				<div class="folderOuter">
					<div class="folderInner" desk="4"></div>
					<div class="scrollBar"></div>
				</div>
			</div>
			<div class="folderItem">
				<div class="folder_bg folder_bg5"></div>
				<div class="folderOuter">
					<div class="folderInner" desk="5"></div>
					<div class="scrollBar"></div>
				</div>
			</div>
		</div>
	</div>
	<div id="copyright">
		<a href="javascript:;" class="close" title="关闭"></a>
		<div class="title">HoorayOS</div>
		<div class="body">
			<p>这是一款备受好评的 Web 桌面应用框架，你可以用它二次开发出类似 Q+Web 这类的桌面应用网站，也可以开发出适合各种项目的桌面管理系统。</p>
			<p>官网：<a href="http://hoorayos.com" target="_blank">http://hoorayos.com</a></p>
			<p>购买或定制请联系 QQ：<a href="http://wpa.qq.com/msgrd?v=3&uin=304327508&site=qq&menu=yes" target="_blank">304327508</a></p>
		</div>
	</div>
	<script src="static/plugins/jquery-2.2.4.min.js"></script>
	<script src="static/plugins/HoorayLibs/hooraylibs.js"></script>
	<script src="static/plugins/Validform_v5.3.2/Validform_v5.3.2_min.js"></script>
	<script src="libs/clicaptcha/clicaptcha.js"></script>
	<script src="static/plugins/Sugar-2.0.2/dist/sugar.min.js"></script>
	<script src="static/plugins/artDialog-7.0.0/dist/dialog-plus.js"></script>
	<script src="static/plugins/sweetalert-1.1.1/dist/sweetalert.min.js"></script>
	<!-- 通过js目录下的两个批处理文件，可以合并并压缩js代码 -->
	<script src="static/js/hros.core.js"></script>
	<script src="static/js/hros.app.js"></script>
	<script src="static/js/hros.appmanage.js"></script>
	<script src="static/js/hros.base.js"></script>
	<script src="static/js/hros.copyright.js"></script>
	<script src="static/js/hros.desktop.js"></script>
	<script src="static/js/hros.dock.js"></script>
	<script src="static/js/hros.folderView.js"></script>
	<script src="static/js/hros.grid.js"></script>
	<script src="static/js/hros.hotkey.js"></script>
	<script src="static/js/hros.lock.js"></script>
	<script src="static/js/hros.maskBox.js"></script>
	<script src="static/js/hros.popupMenu.js"></script>
	<script src="static/js/hros.searchBar.js"></script>
	<script src="static/js/hros.startMenu.js"></script>
	<script src="static/js/hros.taskBar.js"></script>
	<script src="static/js/hros.templates.js"></script>
	<script src="static/js/hros.wallpaper.js"></script>
	<script src="static/js/hros.widget.js"></script>
	<script src="static/js/hros.window.js"></script>
	<!-- 执行完毕后可将上面所有hros开头的js文件引用删除，然后去掉下面这句代码的注释即可 -->
	<!-- <script src="static/js/hros.min.js"></script> -->
	<script>
	var childWindow, interval;
	$(function(){
		var loginboxHeight = $('#lrbox .loginbox').outerHeight();
		var registerboxHeight = $('#lrbox .registerbox').outerHeight();
		$('#lrbox .loginbox').css('marginTop', (loginboxHeight / 2) * -1);
		$('#lrbox .registerbox').css('marginTop', (registerboxHeight / 2) * -1);
		var lrboxHeight = loginboxHeight > registerboxHeight ? loginboxHeight: registerboxHeight;
		$('#lrbox .lrbox').css({
			height: lrboxHeight,
			marginTop: (loginboxHeight / 2) * -1
		});
		$('#lrbox .back').on('click', function(){
			$('#lrbox').animate({
				top: '-200%'
			}, 1000);
		});
		changeTabindex();
		$('#lrbox .loginbox .mask').click(function(){
			changeTabindex('login');
		});
		$('#lrbox .registerbox .mask').click(function(){
			changeTabindex('register');
		});
		//初始化登录用户
		if(typeof Cookies.get(cookie_prefix + 'userinfo') !== 'undefined'){
			var userinfo = JSON.parse(Cookies.get(cookie_prefix + 'userinfo'));
			$('#avatar').attr('src', userinfo.avatar);
			$('#username').val(userinfo.username);
		}
		//表单登录初始化
		var loginForm = $('#loginForm').Validform({
			sbtnSubmit: '#submit_login_btn',
			postonce: false,
			sshowAllError: true,
			tipSweep: false,
			//msg：提示信息;
			//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
			//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
			tiptype: function(msg, o){
				if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
					var B = o.obj.parent('.input_box').children('.tip');
					var T = B.find('p');
					if(o.type == 2){
						B.hide();
						T.text('');
					}else{
						B.show();
						T.text(msg);
					}
				}
			},
			ajaxPost: true,
			beforeSubmit: function(){
				$('#submit_login_btn').addClass('disabled').prop('disabled', true);
			},
			callback: function(data){
				$('#submit_login_btn').removeClass('disabled').prop('disabled', false);
				if(data.status == 'y'){
					location.reload();
				}else{
					if(data.info == 'ERROR_OPENID_IS_USED'){
						swal({
							type: 'warning',
							title: '温馨提示',
							text: '该账号已经绑定过' + $('.disanfangdenglutip span').text() + '账号<br />请更换其它账号，或者取消绑定，直接登录',
							html: true,
							timer: 2500,
							showConfirmButton: false
						});
					}else{
						swal({
							type: 'error',
							title: '登录失败',
							text: '请检查用户名或密码是否正确',
							timer: 2000,
							showConfirmButton: false
						});
					}
				}
			}
		});
		//表单注册初始化
		var registerForm = $('#registerForm').Validform({
			btnSubmit: '#submit_register_btn',
			postonce: false,
			showAllError: true,
			tipSweep: false,
			//msg：提示信息;
			//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
			//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
			tiptype: function(msg, o){
				if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
					var B = o.obj.parent('.input_box').children('.tip');
					var T = B.find('p');
					if(o.type == 2){
						B.hide();
						T.text('');
					}else{
						B.show();
						T.text(msg);
					}
				}
			},
			ajaxPost: true,
			beforeSubmit: function(){
				if(!$('#clicaptcha_info').clicaptchaCheck()){
					$('#clicaptcha_info').clicaptcha({
						src: 'libs/clicaptcha/clicaptcha.php',
						success_tip: '验证成功，正在注册！',
						callback: function(){
							$('#submit_register_btn').addClass('disabled').prop('disabled', true);
							registerForm.submitForm();
						}
					});
					return false;
				}
			},
			callback: function(data){
				$('#clicaptcha_info').clicaptchaReset();
				$('#submit_register_btn').removeClass('disabled').prop('disabled', false);
				registerForm.resetStatus();
				if(data.status == 'y'){
					$('#avatar').attr('src', 'static/img/avatar_120.jpg');
					$('#username').val(data.info);
					$('#password').val('');
					$('#rememberMe').prop('checked', false);
					$('#reg_username, #reg_password, #reg_password2').val('');
					changeTabindex('login');
					swal({
						type: 'success',
						title: '注册成功',
						timer: 2000,
						showConfirmButton: false
					});
				}else{
					swal({
						type: 'error',
						title: '注册失败',
						text: data.info,
						timer: 2000,
						showConfirmButton: false
					});
				}
			}
		});
		$('.disanfangdenglu a').click(function(){
			checkUserLogin();
			childWindow = dialog({
				title: '第三方登录授权',
				zIndex: 10000,
				padding: 0,
				width: 600,
				height: 500,
				url: 'connect/' + $(this).data('type') + '/redirect.php'
			}).showModal();
		});
		$('.disanfangdenglutip .cancel').click(function(){
			Cookies.remove(cookie_prefix + 'fromsite');
			$('.disanfangdenglutip').hide();
			$('.disanfangdenglu').show();
		});
	});
	function changeTabindex(mode){
		$('#username, #password, #submit_login_btn, #reg_username, #reg_password, #reg_password2, #submit_register_btn').attr('tabindex', '-1');
		var loginbox = $('#lrbox .loginbox');
		var registerbox = $('#lrbox .registerbox');
		var loginboxMarginLeftOnShow = (loginbox.outerWidth() / 2) * -1;
		var loginboxMarginLeftOnMove = loginbox.outerWidth() * -1;
		var loginboxMarginLeftOnHide = (loginbox.outerWidth() / 2) * -1 - 80;
		var registerboxMarginRightOnShow = (registerbox.outerWidth() / 2) * -1;
		var registerboxMarginRightOnMove = registerbox.outerWidth() * -1;
		var registerboxMarginRightOnHide = (registerbox.outerWidth() / 2) * -1 - 80;
		var onShowTime = 200, onMoveTime = 250;
		var onShowScale = 1, onMoveScale = 0.9, onHideScale = 0.8;
		switch(mode){
			case 'login':
				//登录面板动画
				loginbox.transition({
					scale: onMoveScale,
					marginLeft: loginboxMarginLeftOnMove
				}, onMoveTime, 'linear', function(){
					loginbox.css('zIndex', 2);
				}).transition({
					scale: onShowScale,
					marginLeft: loginboxMarginLeftOnShow
				}, onShowTime, 'linear');
				loginbox.children('.mask').transition({
					opacity: 0
				}, onShowTime + onMoveTime, function(){
					loginbox.children('.mask').hide();
				});
				loginbox.children('.form').transition({
					opacity: 1
				}, onShowTime + onMoveTime, function(){
					$('#username').attr('tabindex', 1);
					$('#password').attr('tabindex', 2);
					$('#submit_login_btn').attr('tabindex', 3);
				});
				//注册面板动画
				registerbox.transition({
					scale: onMoveScale,
					marginRight: registerboxMarginRightOnMove
				}, onMoveTime, 'linear', function(){
					registerbox.css('zIndex', 1);
				}).transition({
					scale: onHideScale,
					marginRight: registerboxMarginRightOnHide
				}, onShowTime, 'linear');
				registerbox.children('.form').transition({
					opacity: 0
				}, onShowTime + onMoveTime);
				registerbox.children('.mask').show().transition({
					opacity: 1
				}, onShowTime + onMoveTime);
				break;
			case 'register':
				//登录面板动画
				loginbox.transition({
					scale: onMoveScale,
					marginLeft: loginboxMarginLeftOnMove
				}, onMoveTime, 'linear', function(){
					loginbox.css('zIndex', 1);
				}).transition({
					scale: onHideScale,
					marginLeft: loginboxMarginLeftOnHide
				}, onShowTime, 'linear');
				loginbox.children('.form').transition({
					opacity: 0
				}, onShowTime + onMoveTime);
				loginbox.children('.mask').show().transition({
					opacity: 1
				}, onShowTime + onMoveTime);
				//注册面板动画
				registerbox.transition({
					scale: onMoveScale,
					marginRight: registerboxMarginRightOnMove
				}, onMoveTime, 'linear', function(){
					registerbox.css('zIndex', 2);
				}).transition({
					scale: onShowScale,
					marginRight: registerboxMarginRightOnShow
				}, onShowTime, 'linear');
				registerbox.children('.mask').transition({
					opacity: 0
				}, onShowTime + onMoveTime, function(){
					registerbox.children('.mask').hide();
				});
				registerbox.children('.form').transition({
					opacity: 1
				}, onShowTime + onMoveTime, function(){
					$('#reg_username').attr('tabindex', 1);
					$('#reg_password').attr('tabindex', 2);
					$('#reg_password2').attr('tabindex', 3);
					$('#submit_register_btn').attr('tabindex', 4);
				});
				break;
			default:
				$('#username').attr('tabindex', 1);
				$('#password').attr('tabindex', 2);
				$('#submit_login_btn').attr('tabindex', 3);
				loginbox.css({
					zIndex: 2,
					scale: onShowScale,
					marginLeft: loginboxMarginLeftOnShow
				});
				loginbox.children('.mask').css('opacity', 0).hide();
				registerbox.css({
					zIndex: 1,
					scale: onHideScale,
					marginRight: registerboxMarginRightOnHide
				});
				registerbox.children('.form').css('opacity', 0);
		}
	}
	function checkUserLogin(){
		Cookies.remove(cookie_prefix + 'fromsite');
		interval = setInterval(function(){
			getLoginCookie();
		}, 500);
	}
	function getLoginCookie(){
		if(Cookies.get(cookie_prefix + 'fromsite')){
			childWindow.remove();
			window.clearInterval(interval);
			//验证该三方登录账号是否已绑定过本地账号，有则直接登录，否则执行绑定账号流程
			$.ajax({
				url: 'login.ajax.php',
				data: 'ac=3login',
				success: function(msg){
					if(msg == 'ERROR_LACK_OF_DATA'){
						swal({
							type: 'error',
							title: '未知错误',
							text: '建议重启浏览器后重新操作',
							timer: 2000,
							showConfirmButton: false
						});
					}else if(msg == 'ERROR_NOT_BIND'){
						var title = '';
						switch(Cookies.get(cookie_prefix + 'fromsite')){
							case 'qq': title = 'QQ'; break;
							case 'weibo': title = '新浪微博'; break;
							default: return false;
						}
						$('.disanfangdenglu').hide();
						$('.disanfangdenglutip').show();
						$('.disanfangdenglutip').children('.fromsite').text(title);
						$('.disanfangdenglutip').children('.fromsitename').text(Cookies.get(cookie_prefix + 'fromsitename'));
					}else{
						location.reload();
					}
				}
			});
		}
	}
	</script>
	<script>
	$(function(){
		//IE下禁止选中
		document.body.onselectstart = document.body.ondrag = function(){
			return false;
		}
		//隐藏加载遮罩层
		$('.loading').hide();
		if($('#lrbox').data('isforcedlogin') == 0 || Cookies.get(cookie_prefix + 'memberID') != 0){
			$('#desktop').show();
			//初始化一些桌面信息
			<?php
				$w = explode('<{|}>', getWallpaper());
			?>
			HROS.CONFIG.wallpaperState = <?php echo $w[0]; ?>;
			<?php
				switch($w[0]){
					case 1:
					case 2:
			?>
			HROS.CONFIG.wallpaper = '<?php echo $w[1]; ?>';
			HROS.CONFIG.wallpaperType = '<?php echo $w[2]; ?>';
			HROS.CONFIG.wallpaperWidth = <?php echo $w[3]; ?>;
			HROS.CONFIG.wallpaperHeight = <?php echo $w[4]; ?>;
			<?php
						break;
					case 3:
			?>
			HROS.CONFIG.wallpaper = '<?php echo $w[1]; ?>';
			<?php
						break;
				}
			?>
			HROS.CONFIG.dockPos = '<?php echo getDockPos(); ?>';
			HROS.CONFIG.appXY = '<?php echo getAppXY(); ?>';
			HROS.CONFIG.appSize = <?php echo getAppSize(); ?>;
			HROS.CONFIG.appVerticalSpacing = <?php echo getAppVerticalSpacing(); ?>;
			HROS.CONFIG.appHorizontalSpacing = <?php echo getAppHorizontalSpacing(); ?>;
			HROS.CONFIG.desk = <?php echo getDesk(); ?>;
			//加载桌面
			HROS.base.init();
		}
	});
	</script>
</body>
</html>