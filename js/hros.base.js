/*
**  一个不属于其他模块的模块
*/
HROS.base = (function(){
	return {
		/*
		**	系统初始化
		*/
		init : function(){
			//配置artDialog全局默认参数
			(function(config){
				config['lock'] = true;
				config['fixed'] = true;
				config['resize'] = false;
				config['background'] = '#000';
				config['opacity'] = 0.5;
			})($.dialog.defaults);
			//更新当前用户ID
			HROS.CONFIG.memberID = $.cookie(cookie_prefix + 'memberID');
			//阻止弹出浏览器默认右键菜单
			$('body').on('contextmenu', function(){
				return false;
			});
			//版权信息初始化并显示
			HROS.copyright.init();
			//用于判断网页是否缩放
			HROS.zoom.init();
			//桌面(容器)初始化
			HROS.deskTop.init();
			//初始化壁纸
			HROS.wallpaper.init();
			//初始化搜索栏
			HROS.searchbar.init();
			//初始化开始菜单
			HROS.startmenu.init();
			//初始化任务栏
			HROS.taskbar.init();
			/*
			**      当dockPos为top时          当dockPos为left时         当dockPos为right时
			**  -----------------------   -----------------------   -----------------------
			**  | o o o         dock  |   | o | o               |   | o               | o |
			**  -----------------------   | o | o               |   | o               | o |
			**  | o o                 |   | o | o               |   | o               | o |
			**  | o +                 |   |   | o               |   | o               |   |
			**  | o             desk  |   |   | o         desk  |   | o         desk  |   |
			**  | o                   |   |   | +               |   | +               |   |
			**  -----------------------   -----------------------   -----------------------
			**  因为desk区域的尺寸和定位受dock位置的影响，所以加载应用前必须先定位好dock的位置
			*/
			//初始化应用码头
			HROS.dock.init();
			//初始化桌面应用
			HROS.app.init();
			//初始化widget模块
			HROS.widget.init();
			//初始化窗口模块
			HROS.window.init();
			//初始化文件夹预览
			HROS.folderView.init();
			//初始化全局视图
			HROS.appmanage.init();
			//初始化右键菜单
			HROS.popupMenu.init();
			//初始化锁屏
			HROS.lock.init();
			//初始化快捷键
			HROS.hotkey.init();
			//页面加载后运行
			HROS.base.run();
			//绑定ajax全局验证
			$(document).ajaxSuccess(function(event, xhr, settings){
				if($.trim(xhr.responseText) == 'ERROR_NOT_LOGGED_IN'){
					HROS.CONFIG.memberID = 0;
					$.dialog({
						title : '温馨提示',
						icon : 'warning',
						content : '系统检测到您尚未登录，为了更好的操作，是否登录？',
						ok : function(){
							HROS.base.login();
						}
					});
				}
			});
		},
		login : function(){
			$('#lrbox').animate({
				top : 0
			}, 500, function(){
				changeTabindex('login');
			});
		},
		logout : function(){
			$.ajax({
				type : 'POST',
				url : 'login.ajax.php',
				data : 'ac=logout'
			}).done(function(){
				location.reload();
			});
		},
		checkLogin : function(){
			return HROS.CONFIG.memberID != 0 ? true : false;
		},
		setSkin : function(skin, callback){
			function styleOnload(node, callback) {
				// for IE6-9 and Opera
				if(node.attachEvent){
					node.attachEvent('onload', callback);
					// NOTICE:
					// 1. "onload" will be fired in IE6-9 when the file is 404, but in
					// this situation, Opera does nothing, so fallback to timeout.
					// 2. "onerror" doesn't fire in any browsers!
				}
				// polling for Firefox, Chrome, Safari
				else{
					setTimeout(function(){
						poll(node, callback);
					}, 0); // for cache
				}
			}
			function poll(node, callback) {
				if(callback.isCalled){
					return;
				}
				var isLoaded = false;
				//webkit
				if(/webkit/i.test(navigator.userAgent)){
					if (node['sheet']) {
						isLoaded = true;
					}
				}
				// for Firefox
				else if(node['sheet']){
					try{
						if (node['sheet'].cssRules) {
							isLoaded = true;
						}
					}catch(ex){
						// NS_ERROR_DOM_SECURITY_ERR
						if(ex.code === 1000){
							isLoaded = true;
						}
					}
				}
				if(isLoaded){
					// give time to render.
					setTimeout(function() {
						callback();
					}, 1);
				}else{
					setTimeout(function() {
						poll(node, callback);
					}, 1);
				}
			}					
			//将原样式修改id，并载入新样式
			$('#window-skin').attr('id', 'window-skin-ready2remove');
			var css = document.createElement('link');
			css.rel = 'stylesheet';
			css.href = 'img/skins/' + skin + '.css?' + version;
			css.id = 'window-skin';
			document.getElementsByTagName('head')[0].appendChild(css);
			//新样式载入完毕后清空原样式
			//方法为参考seajs源码并改编，文章地址：http://www.blogjava.net/Hafeyang/archive/2011/10/08/360183.html
			styleOnload(css, function(){
				$('#window-skin-ready2remove').remove();
				HROS.CONFIG.skin = skin;
				callback && callback();
			});
		},
		help : function(){
			if(!$.browser.msie || ($.browser.msie && $.browser.version < 9)){
				$('body').append(helpTemp);
				//IE6,7,8基本就告别新手帮助了
				$('#step1').show();
				$('.close').on('click', function(){
					$('#help').remove();
				});
				$('.next').on('click', function(){
					var obj = $(this).parents('.step');
					var step = obj.attr('step');
					obj.hide();
					$('#step' + (parseInt(step) + 1)).show();
				});
				$('.over').on('click', function(){
					$('#help').remove();
				});
			}
		},
		run : function(){
			var url = location.search;
			var request = new Object();
			if(url.indexOf("?") != -1){
				var str = url.substr(1);
				strs = str.split("&");
				for(var i = 0; i < strs.length; i++){
					request[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
				}
			}
			if(typeof(request['run']) != 'undefined' && typeof(request['type']) != 'undefined'){
				if(HROS.base.checkLogin()){
					$.ajax({
						type : 'POST',
						url : ajaxUrl,
						data : 'ac=getAppidByRealappid&id=' + request['run']
					}).done(function(appid){
						if(request['type'] == 'app'){
							HROS.window.create(appid);
						}else{
							//判断挂件是否存在cookie中，因为如果存在则自动会启动
							if(!HROS.widget.checkCookie(appid, request['type'])){
								HROS.widget.create(appid);
							}
						}
					});
				}else{
					HROS.window.createTemp({
						appid : 'hoorayos-yysc',
						title : '应用市场',
						url : 'sysapp/appmarket/index.php?id=' + request['run'],
						width : 800,
						height : 484,
						isflash : false,
						refresh : true
					});
				}
			}
		}
	}
})();