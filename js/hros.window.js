/*
**  应用窗口
*/
HROS.window = (function(){
	return {
		init : function(){
			//窗口上各个按钮
			HROS.window.handle();
			//窗口移动
			HROS.window.move();
			//窗口拉伸
			HROS.window.resize();
			//绑定窗口遮罩层点击事件
			$('#desk').on('click', '.window-container .window-mask, .window-container .folder_body', function(){
				HROS.window.show2top($(this).parents('.window-container').attr('appid'), true);
			});
			//屏蔽窗口右键
			$('#desk').on('contextmenu', '.window-container', function(){
				return false;
			});
			//绑定文件夹内应用点击事件
			$('#desk').on('click', '.folder_body li', function(){
				return false;
			}).on('contextmenu', '.folder_body .appbtn', function(e){
				$('.popup-menu').hide();
				$('.quick_view_container').remove();
				switch($(this).attr('type')){
					case 'window':
					case 'widget':
						var popupmenu = HROS.popupMenu.app($(this));
						break;
					case 'pwindow':
					case 'pwidget':
						var popupmenu = HROS.popupMenu.papp($(this));
						break;
				}
				var l = ($(window).width() - e.clientX) < popupmenu.width() ? (e.clientX - popupmenu.width()) : e.clientX;
				var t = ($(window).height() - e.clientY) < popupmenu.height() ? (e.clientY - popupmenu.height()) : e.clientY;
				popupmenu.css({
					left : l,
					top : t
				}).show();
				return false;
			});
		},
		/*
		**  创建窗口
		**  自定义窗口：HROS.window.createTemp({title,url,width,height,resize,isflash});
		**      后面参数依次为：标题、地址、宽、高、是否可拉伸、是否打开默认最大化、是否为flash
		**      示例：HROS.window.createTemp({title:"百度",url:"http://www.baidu.com",width:800,height:400,isresize:false,isopenmax:false,isflash:false});
		*/
		createTemp : function(obj){
			var type = 'window', appid = obj.appid == null ? Date.parse(new Date()) : obj.appid;
			//判断窗口是否已打开
			var iswindowopen = false;
			$('#task-content-inner a.task-item').each(function(){
				if($(this).attr('appid') == appid){
					iswindowopen = true;
					HROS.window.show2top($(this).attr('appid'));
					return false;
				}
			});
			//如果没有打开，则进行创建
			if(!iswindowopen){
				function nextDo(options){
					var windowId = '#w_' + options.appid;
					//新增任务栏
					$('#task-content-inner').prepend(taskTemp({
						'type' : options.type,
						'id' : 't_' + options.appid,
						'appid' : options.appid,
						'realappid' : options.appid,
						'title' : options.title,
						'imgsrc' : options.imgsrc
					}));
					HROS.taskbar.resize();
					//新增窗口
					TEMP.windowTemp = {
						'top' : ($(window).height() - options.height) / 2 <= 0 ? 0 : ($(window).height() - options.height) / 2,
						'left' : ($(window).width() - options.width) / 2 <= 0 ? 0 : ($(window).width() - options.width) / 2,
						'emptyW' : $(window).width() - options.width,
						'emptyH' : $(window).height() - options.height,
						'width' : options.width,
						'height' : options.height,
						'zIndex' : HROS.CONFIG.windowIndexid,
						'type' : options.type,
						'id' : 'w_' + options.appid,
						'appid' : options.appid,
						'realappid' : options.appid,
						'title' : options.title,
						'url' : options.url,
						'imgsrc' : options.imgsrc,
						'isresize' : options.isresize,
						'isopenmax' : options.isopenmax,
						'istitlebar' : options.isresize,
						'istitlebarFullscreen' : options.isresize ? window.fullScreenApi.supportsFullScreen == true ? true : false : false,
						'issetbar' : options.issetbar,
						'isflash' : options.isflash
					};
					$('#desk').append(windowTemp(TEMP.windowTemp));
					$(windowId).data('info', TEMP.windowTemp);
					HROS.CONFIG.windowIndexid += 1;
					HROS.window.setPos(false);
					//iframe加载完毕后，隐藏loading遮罩层
					$(windowId + ' iframe').load(function(){
						$(windowId + ' .window-frame').children('div').eq(1).fadeOut();
					});
					HROS.window.show2top(options.appid);
				}
				nextDo({
					type : type,
					appid : appid,
					realappid : appid,
					imgsrc : 'img/ui/default_icon.png',
					title : obj.title,
					url : obj.url,
					width : obj.width,
					height : obj.height,
					isresize : typeof(obj.isresize) == 'undefined' ? false : obj.isresize,
					isopenmax : typeof(obj.isopenmax) == 'undefined' ? false : obj.isopenmax,
					issetbar : false,
					isflash : typeof(obj.isflash) == 'undefined' ? true : obj.isflash
				});
			}else{
				//如果设置强制刷新
				if(obj.refresh){
					var windowId = '#w_' + appid;
					$(windowId).find('iframe').attr('src', obj.url);
				}
			}
		},
		/*
		**  创建窗口
		**  系统窗口：HROS.window.create(appid, [type]);
		**      示例：HROS.window.create(12);
		*/
		create : function(appid, type){
			var type = type == null ? 'window' : type;
			//判断窗口是否已打开
			var iswindowopen = false;
			$('#task-content-inner a.task-item').each(function(){
				if($(this).attr('appid') == appid){
					iswindowopen = true;
					HROS.window.show2top(appid);
					return false;
				}
			});
			//如果没有打开，则进行创建
			if(!iswindowopen && $('#d_' + appid).attr('opening') != 1){
				$('#d_' + appid).attr('opening', 1);
				function nextDo(options){
					var windowId = '#w_' + options.appid;
					var top = ($(window).height() - options.height) / 2 <= 0 ? 0 : ($(window).height() - options.height) / 2;
					var left = ($(window).width() - options.width) / 2 <= 0 ? 0 : ($(window).width() - options.width) / 2;
					switch(options.type){
						case 'window':
						case 'pwindow':
							//新增任务栏
							$('#task-content-inner').prepend(taskTemp({
								'type' : options.type,
								'id' : 't_' + options.appid,
								'appid' : options.appid,
								'realappid' : options.realappid,
								'title' : options.title,
								'imgsrc' : options.imgsrc
							}));
							HROS.taskbar.resize();
							//新增窗口
							TEMP.windowTemp = {
								'top' : top,
								'left' : left,
								'emptyW' : $(window).width() - options.width,
								'emptyH' : $(window).height() - options.height,
								'width' : options.width,
								'height' : options.height,
								'zIndex' : HROS.CONFIG.windowIndexid,
								'type' : options.type,
								'id' : 'w_' + options.appid,
								'appid' : options.appid,
								'realappid' : options.realappid,
								'title' : options.title,
								'url' : options.url,
								'imgsrc' : options.imgsrc,
								'isresize' : options.isresize == 1 ? true : false,
								'isopenmax' : options.isresize == 1 ? options.isopenmax == 1 ? true : false : false,
								'istitlebar' : options.isresize == 1 ? true : false,
								'istitlebarFullscreen' : options.isresize == 1 ? window.fullScreenApi.supportsFullScreen == true ? true : false : false,
								'issetbar' : options.issetbar == 1 ? true : false,
								'isflash' : options.isflash == 1 ? true : false
							};
							$('#desk').append(windowTemp(TEMP.windowTemp));
							$(windowId).data('info', TEMP.windowTemp);
							HROS.CONFIG.windowIndexid += 1;
							HROS.window.setPos(false);
							//iframe加载完毕后，隐藏loading遮罩层
							$(windowId + ' iframe').load(function(){
								$(windowId + ' .window-frame').children('div').fadeOut();
							});
							HROS.window.show2top(options.appid);
							break;
						case 'folder':
							//新增任务栏
							$('#task-content-inner').prepend(taskTemp({
								'type' : options.type,
								'id' : 't_' + options.appid,
								'appid' : options.appid,
								'realappid' : options.realappid,
								'title' : options.title,
								'imgsrc' : options.imgsrc
							}));
							HROS.taskbar.resize();
							//新增窗口
							TEMP.folderWindowTemp = {
								'top' : top,
								'left' : left,
								'emptyW' : $(window).width() - options.width,
								'emptyH' : $(window).height() - options.height,
								'width' : options.width,
								'height' : options.height,
								'zIndex' : HROS.CONFIG.windowIndexid,
								'type' : options.type,
								'id' : 'w_' + options.appid,
								'appid' : options.appid,
								'realappid' : options.realappid,
								'title' : options.title,
								'imgsrc' : options.imgsrc
							};
							$('#desk').append(folderWindowTemp(TEMP.folderWindowTemp));
							$(windowId).data('info', TEMP.folderWindowTemp);
							HROS.CONFIG.windowIndexid += 1;
							HROS.window.setPos(false);
							//载入文件夹内容
							var sc = '';
							$(HROS.VAR.folder).each(function(){
								if(this.appid == options.appid){
									sc = this.apps;
									return false;
								}
							});
							if(sc != ''){
								var folder_append = '';
								$(sc).each(function(){
									folder_append += appbtnTemp({
										'title' : this.name,
										'type' : this.type,
										'id' : 'd_' + this.appid,
										'appid' : this.appid,
										'realappid' : this.realappid,
										'imgsrc' : this.icon
									});
								});
								$(windowId).find('.folder_body').append(folder_append);
							}
							HROS.window.show2top(options.appid);
							break;
						case 'file':
							TEMP.fileWindowTemp = {
								'top' : top,
								'left' : left,
								'emptyW' : $(window).width() - options.width,
								'emptyH' : $(window).height() - options.height,
								'width' : options.width,
								'height' : options.height,
								'zIndex' : HROS.CONFIG.windowIndexid,
								'type' : options.type,
								'id' : 'w_' + options.appid,
								'appid' : options.appid,
								'realappid' : options.realappid,
								'title' : options.title,
								'imgsrc' : options.imgsrc
							};
							$('body').append(fileDownloadTemp(TEMP.fileWindowTemp));
							break;
					}
				}
				ZENG.msgbox.show('应用正在加载中，请耐心等待...', 6, 100000);
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=getMyAppById&id=' + appid + '&type=' + type,
					dataType : 'json'
				}).done(function(app){
					ZENG.msgbox._hide();
					if(app != null){
						if(app['error'] == 'ERROR_NOT_FOUND'){
							ZENG.msgbox.show('应用不存在，建议删除', 5, 2000);
						}else if(app['error'] == 'ERROR_NOT_INSTALLED'){
							HROS.window.createTemp({
								appid : 'hoorayos-yysc',
								title : '应用市场',
								url : 'sysapp/appmarket/index.php?id=' + $('#d_' + appid).attr('realappid'),
								width : 800,
								height : 484,
								isflash : false,
								refresh : true
							});
						}else{
							nextDo({
								type : app['type'],
								appid : app['appid'],
								realappid : app['realappid'],
								title : app['name'],
								imgsrc : app['icon'],
								url : app['url'],
								width : app['width'],
								height : app['height'],
								isresize : app['isresize'],
								isopenmax : app['isopenmax'],
								issetbar : app['issetbar'],
								isflash : app['isflash']
							});
						}
					}else{
						ZENG.msgbox.show('数据拉取失败', 5, 2000);
					}
					$('#d_' + appid).attr('opening', 0);
				});
			}
		},
		setPos : function(isAnimate){
			isAnimate = isAnimate == null ? true : isAnimate;
			$('#desk .window-container').each(function(){
				var windowdata = $(this).data('info');
				var currentW = $(window).width() - $(this).width();
				var currentH = $(window).height() - $(this).height();
				var left = windowdata['left'] / windowdata['emptyW'] * currentW >= currentW ? currentW : windowdata['left'] / windowdata['emptyW'] * currentW;
				left = left <= 0 ? 0 : left;
				var top = windowdata['top'] / windowdata['emptyH'] * currentH >= currentH ? currentH : windowdata['top'] / windowdata['emptyH'] * currentH;
				top = top <= 0 ? 0 : top;
				if($(this).attr('state') != 'hide'){
					$(this).stop(true, false).animate({
						'left' : left,
						'top' : top
					}, isAnimate ? 500 : 0, function(){
						windowdata['left'] = left;
						windowdata['top'] = top;
						windowdata['emptyW'] = $(window).width() - $(this).width();
						windowdata['emptyH'] = $(window).height() - $(this).height();
					});
				}else{
					windowdata['left'] = left;
					windowdata['top'] = top;
					windowdata['emptyW'] = $(window).width() - $(this).width();
					windowdata['emptyH'] = $(window).height() - $(this).height();
				}
			});
		},
		close : function(appid){
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId).removeData('info').html('').remove();
			$('#task-content-inner ' + taskId).html('').remove();
			$('#task-content-inner').css('width', $('#task-content-inner .task-item').length * 114);
			$('#task-bar, #nav-bar').removeClass('min-zIndex');
			HROS.taskbar.resize();
		},
		closeAll : function(){
			$('#desk .window-container').each(function(){
				HROS.window.close($(this).attr('appid'));
			});
		},
		hide : function(appid){
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId).css('left', -10000).attr('state', 'hide');
			$('#task-content-inner ' + taskId).removeClass('task-item-current');
			if($(windowId).attr('ismax') == 1){
				$('#task-bar, #nav-bar').removeClass('min-zIndex');
			}
		},
		hideAll : function(){
			$('#task-content-inner a.task-item').removeClass('task-item-current');
			$('#desk-' + HROS.CONFIG.desk).nextAll('div.window-container').css('left', -10000).attr('state', 'hide');
		},
		max : function(appid){
			HROS.window.show2top(appid);
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId + ' .title-handle .ha-max').hide().next(".ha-revert").show();
			$(windowId).addClass('window-maximize').attr('ismax',1).animate({
				width : '100%',
				height : '100%',
				top : 0,
				left : 0
			}, 200);
			$('#task-bar, #nav-bar').addClass('min-zIndex');
		},
		revert : function(appid){
			HROS.window.show2top(appid);
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId + ' .title-handle .ha-revert').hide().prev('.ha-max').show();
			var obj = $(windowId), windowdata = obj.data('info');
			obj.removeClass('window-maximize').attr('ismax',0).animate({
				width : windowdata['width'],
				height : windowdata['height'],
				left : windowdata['left'],
				top : windowdata['top']
			}, 500);
			$('#task-bar, #nav-bar').removeClass('min-zIndex');
		},
		refresh : function(appid){
			HROS.window.show2top(appid);
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			//判断是应用窗口，还是文件夹窗口
			if($(windowId + '_iframe').length != 0){
				$(windowId + '_iframe').attr('src', $(windowId + '_iframe').attr('src'));
			}else{
				HROS.window.updateFolder(appid);
			}
		},
		show2top : function(appid, isAnimate){
			isAnimate = isAnimate == null ? false : isAnimate;
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			var windowdata = $(windowId).data('info');
			var arr = [];
			function show(){
				HROS.window.show2under();
				//改变当前任务栏样式
				$('#task-content-inner ' + taskId).addClass('task-item-current');
				if($(windowId).attr('ismax') == 1){
					$('#task-bar, #nav-bar').addClass('min-zIndex');
				}
				//改变当前窗口样式
				$(windowId).addClass('window-current').css({
					'z-index' : HROS.CONFIG.windowIndexid,
					'left' : windowdata['left'],
					'top' : windowdata['top']
				}).attr('state', 'show');
				//如果窗口最小化前是最大化状态的，则坐标位置设为0
				if($(windowId).attr('ismax') == 1){
					$(windowId).css({
						'left' : 0,
						'top' : 0
					});
				}
				//改变当前窗口遮罩层样式
				$(windowId + ' .window-mask').hide();
				//改变当前iframe显示
				$(windowId + ' iframe').show();
				HROS.CONFIG.windowIndexid += 1;
			}
			if(isAnimate){
				var baseStartX = $(windowId).offset().left, baseEndX = baseStartX + $(windowId).width();
				var baseStartY = $(windowId).offset().top, baseEndY = baseStartY + $(windowId).height();
				var baseCenterX = baseStartX + ($(windowId).width() / 2), baseCenterY = baseStartY + ($(windowId).height() / 2);
				var baseZIndex = parseInt($(windowId).css('zIndex'));
				$('#desk .window-container:not(' + windowId + ')').each(function(){
					var thisStartX = $(this).offset().left, thisEndX = thisStartX + $(this).width();
					var thisStartY = $(this).offset().top, thisEndY = thisStartY + $(this).height();
					var thisCenterX = thisStartX + ($(this).width() / 2), thisCenterY = thisStartY + ($(this).height() / 2);
					var thisZIndex = parseInt($(this).css('zIndex'));
					var flag = '';
					if(thisZIndex > baseZIndex){
						//  常规情况，只要有一个角处于区域内，则可以判断窗口有覆盖
						//   _______            _______        _______    _______
						//  |    ___|___    ___|       |   ___|___    |  |       |___
						//  |   |       |  |   |       |  |       |   |  |       |   |
						//  |___|       |  |   |_______|  |       |___|  |_______|   |
						//      |_______|  |_______|      |_______|          |_______|
						if(
							(thisStartX >= baseStartX && thisStartX <= baseEndX && thisStartY >= baseStartY && thisStartY <= baseEndY)
							||
							(thisStartX >= baseStartX && thisStartX <= baseEndX && thisEndY >= baseStartY && thisEndY <= baseEndY)
							||
							(thisEndX >= baseStartX && thisEndX <= baseEndX && thisStartY >= baseStartY && thisStartY <= baseEndY)
							||
							(thisEndX >= baseStartX && thisEndX <= baseEndX && thisEndY >= baseStartY && thisEndY <= baseEndY)
						){
							flag = 'x';
						}
						//  非常规情况
						//       _______    _______          _____
						//   ___|       |  |       |___    _|     |___
						//  |   |       |  |       |   |  | |     |   |
						//  |___|       |  |       |___|  |_|     |___|
						//      |_______|  |_______|        |_____|
						if(
							(thisStartX >= baseStartX && thisStartX <= baseEndX && thisStartY < baseStartY && thisEndY > baseEndY)
							||
							(thisEndX >= baseStartX && thisEndX <= baseEndX && thisStartY < baseStartY && thisEndY > baseEndY)
						){
							flag = 'x';
						}
						//      _____       ___________      _____
						//   __|_____|__   |           |   _|_____|___
						//  |           |  |           |  |           |
						//  |           |  |___________|  |___________|
						//  |___________|     |_____|       |_____|
						if(
							(thisStartY >= baseStartY && thisStartY <= baseEndY && thisStartX < baseStartX && thisEndX > baseEndX)
							||
							(thisEndY >= baseStartY && thisEndY <= baseEndY && thisStartX < baseStartX && thisEndX > baseEndX)
						){
							flag = 'y';
						}
						//  两个角处于区域内，另外两种情况不用处理，因为这两种情况下，被移动的窗口是需要进行上下滑动，而非左右
						//      _____       ___________
						//   __|     |__   |   _____   |
						//  |  |     |  |  |  |     |  |
						//  |  |_____|  |  |__|     |__|
						//  |___________|     |_____|
						if(
							(thisStartX >= baseStartX && thisStartX <= baseEndX && thisEndY >= baseStartY && thisEndY <= baseEndY)
							&&
							(thisEndX >= baseStartX && thisEndX <= baseEndX && thisEndY >= baseStartY && thisEndY <= baseEndY)
							||
							(thisStartX >= baseStartX && thisStartX <= baseEndX && thisStartY >= baseStartY && thisStartY <= baseEndY)
							&&
							(thisEndX >= baseStartX && thisEndX <= baseEndX && thisStartY >= baseStartY && thisStartY <= baseEndY)
						){
							flag = 'y';
						}
					}
					if(flag != ''){
						var direction, distance;
						if(flag == 'x'){
							if(thisCenterX > baseCenterX){
								direction = 'right';
								distance = baseEndX - thisStartX + 30;
							}else{
								direction = 'left';
								distance = thisEndX - baseStartX + 30;
							}
						}else{
							if(thisCenterY > baseCenterY){
								direction = 'bottom';
								distance = baseEndY - thisStartY + 30;
							}else{
								direction = 'top';
								distance = thisEndY - baseStartY + 30;
							}
						}
						arr.push({
							id : $(this).attr('id'),
							direction : direction, //移动方向
							distance : distance //移动距离
						});
					}
				});
				//开始移动
				var delayTime = 0;
				for(var i = 0; i < arr.length; i++){
					var baseLeft = $('#' + arr[i].id).offset().left, baseTop = $('#' + arr[i].id).offset().top;
					if(arr[i].direction == 'left'){
						$('#' + arr[i].id).delay(delayTime).animate({
							left : baseLeft - arr[i].distance
						}, 300).animate({
							left : baseLeft
						}, 300);
					}else if(arr[i].direction == 'right'){
						$('#' + arr[i].id).delay(delayTime).animate({
							left : baseLeft + arr[i].distance
						}, 300).animate({
							left : baseLeft
						}, 300);
					}else if(arr[i].direction == 'top'){
						$('#' + arr[i].id).delay(delayTime).animate({
							top : baseTop - arr[i].distance
						}, 300).animate({
							top : baseTop
						}, 300);
					}else if(arr[i].direction == 'bottom'){
						$('#' + arr[i].id).delay(delayTime).animate({
							top : baseTop + arr[i].distance
						}, 300).animate({
							top : baseTop
						}, 300);
					}
					delayTime += 100;
				}
				setTimeout(show, delayTime + 100);
			}else{
				show();
			}
		},
		show2under : function(){
			//改变任务栏样式
			$('#task-content-inner a.task-item').removeClass('task-item-current');
			//改变窗口样式
			$('#desk .window-container').removeClass('window-current');
			//改变窗口遮罩层样式
			$('#desk .window-container .window-mask').show();
			//改变iframe显示
			$('#desk .window-container-flash iframe').hide();
		},
		updateFolder : function(appid){
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			var sc = '';
			$(HROS.VAR.folder).each(function(){
				if(this.appid == appid){
					sc = this.apps;
					return false;
				}
			});
			if(sc != null){
				var folder_append = '';
				for(var i = 0; i < sc.length; i++){
					folder_append += appbtnTemp({
						'top' : 0,
						'left' : 0,
						'title' : sc[i]['name'],
						'type' : sc[i]['type'],
						'id' : 'd_' + sc[i]['appid'],
						'appid' : sc[i]['appid'],
						'realappid' : sc[i]['realappid'],
						'imgsrc' : sc[i]['icon']
					});
				}
				$(windowId).find('.folder_body').html('').append(folder_append).on('contextmenu', '.appbtn', function(e){
					$('.popup-menu').hide();
					$('.quick_view_container').remove();
					TEMP.AppRight = HROS.popupMenu.app($(this));
					var l = ($(window).width() - e.clientX) < TEMP.AppRight.width() ? (e.clientX - TEMP.AppRight.width()) : e.clientX;
					var t = ($(window).height() - e.clientY) < TEMP.AppRight.height() ? (e.clientY - TEMP.AppRight.height()) : e.clientY;
					TEMP.AppRight.css({
						left : l,
						top : t
					}).show();
					return false;
				});
			}
		},
		handle : function(){
			$('#desk').on('mousedown', '.window-container .title-bar .title-handle a', function(e){
				e.preventDefault();
				e.stopPropagation();
			});
			$('#desk').on('dblclick', '.window-container .title-bar', function(e){
				var obj = $(this).parents('.window-container');
				//判断当前窗口是否已经是最大化
				if(obj.find('.ha-max').is(':hidden')){
					obj.find('.ha-revert').click();
				}else{
					obj.find('.ha-max').click();
				}
			}).on('click', '.window-container .ha-hide', function(){
				var obj = $(this).parents('.window-container');
				HROS.window.hide(obj.attr('appid'));
			}).on('click', '.window-container .ha-max', function(){
				var obj = $(this).parents('.window-container');
				HROS.window.max(obj.attr('appid'));
			}).on('click', '.window-container .ha-revert', function(){
				var obj = $(this).parents('.window-container');
				HROS.window.revert(obj.attr('appid'));
			}).on('click', '.window-container .ha-fullscreen', function(){
				var obj = $(this).parents('.window-container');
				window.fullScreenApi.requestFullScreen(document.getElementById(obj.find('iframe').attr('id')));
			}).on('click', '.window-container .ha-close', function(){
				var obj = $(this).parents('.window-container');
				HROS.window.close(obj.attr('appid'));
			}).on('click', '.window-container .refresh', function(){
				var obj = $(this).parents('.window-container');
				HROS.window.refresh(obj.attr('appid'));
			}).on('click', '.window-container .detail', function(){
				var obj = $(this).parents('.window-container');
				if(obj.attr('realappid') !== 0){
					HROS.window.createTemp({
						appid : 'hoorayos-yysc',
						title : '应用市场',
						url : 'sysapp/appmarket/index.php?id=' + obj.attr('realappid'),
						width : 800,
						height : 484,
						isflash : false,
						refresh : true
					});
				}else{
					ZENG.msgbox.show('对不起，该应用没有任何详细介绍', 1, 2000);
				}
			}).on('click', '.window-container .star', function(){
				var obj = $(this).parents('.window-container');
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=getAppStar&id=' + obj.data('info').realappid
				}).done(function(point){
					$.dialog({
						title : '给“' + obj.data('info').title + '”打分',
						width : 250,
						id : 'star',
						content : starDialogTemp({
							'realappid' : obj.data('info').realappid,
							'point' : point,
							'realpoint' : point * 20
						})
					});
				});
			}).on('click', '.window-container .share', function(){
				var obj = $(this).parents('.window-container');
				$.dialog({
					title : '分享应用',
					width : 370,
					id : 'share',
					content : shareDialogTemp({
						'sinaweiboAppkey' : HROS.CONFIG.sinaweiboAppkey == '' ? '1197457869' : HROS.CONFIG.sinaweiboAppkey,
						'tweiboAppkey' : HROS.CONFIG.tweiboAppkey == '' ? '801356816' : HROS.CONFIG.tweiboAppkey,
						'title' : '我正在使用 %23HoorayOS%23 中的 %23' + obj.data('info').title + '%23 应用，很不错哦，推荐你也来试试！',
						'url' : HROS.CONFIG.website + '?run=' + obj.data('info').realappid + '%26type=app'
					})
				});
			}).on('contextmenu', '.window-container', function(){
				$('.popup-menu').hide();
				$('.quick_view_container').remove();
				return false;
			});
		},
		move : function(){
			$('#desk').on('mousedown', '.window-container .title-bar', function(e){
				var obj = $(this).parents('.window-container');
				if(obj.attr('ismax') == 1){
					return false;
				}
				HROS.window.show2top(obj.attr('appid'));
				var windowdata = obj.data('info');
				var x = e.clientX - obj.offset().left;
				var y = e.clientY - obj.offset().top;
				var lay;
				//绑定鼠标移动事件
				$(document).on('mousemove', function(e){
					lay = HROS.maskBox.desk();
					lay.show();
					//强制把右上角还原按钮隐藏，最大化按钮显示
					obj.find('.ha-revert').hide().prev('.ha-max').show();
					obj.css({
						width : windowdata['width'],
						height : windowdata['height'],
						left : e.clientX - x,
						top : e.clientY - y <= 10 ? 0 : e.clientY - y >= lay.height()-30 ? lay.height()-30 : e.clientY - y
					});
					obj.data('info').left = obj.offset().left;
					obj.data('info').top = obj.offset().top;
				}).on('mouseup', function(){
					$(this).off('mousemove').off('mouseup');
					if(typeof(lay) !== 'undefined'){
						lay.hide();
					}
				});
			});
		},
		resize : function(obj){
			$('#desk').on('mousedown', '.window-container .window-resize', function(e){
				var obj = $(this).parents('.window-container');
				var resizeobj = $(this);
				var lay;
				var x = e.clientX;
				var y = e.clientY;
				var w = obj.width();
				var h = obj.height();
				$(document).on('mousemove', function(e){
					lay = HROS.maskBox.desk();
					lay.show();
					//当拖动到屏幕边缘时，自动贴屏
					var _x = e.clientX <= 10 ? 0 : e.clientX >= (lay.width() - 12) ? (lay.width() - 2) : e.clientX;
					var _y = e.clientY <= 10 ? 0 : e.clientY >= (lay.height() - 12) ? lay.height() : e.clientY;
					switch(resizeobj.attr('resize')){
						case 't':
							h + y - _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height : h + y - _y,
								top : _y
							}) : obj.css({
								height : HROS.CONFIG.windowMinHeight
							});
							break;
						case 'r':
							w - x + _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width : w - x + _x
							}) : obj.css({
								width : HROS.CONFIG.windowMinWidth
							});
							break;
						case 'b':
							h - y + _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height : h - y + _y
							}) : obj.css({
								height : HROS.CONFIG.windowMinHeight
							});
							break;
						case 'l':
							w + x - _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width : w + x - _x,
								left : _x
							}) : obj.css({
								width : HROS.CONFIG.windowMinWidth
							});
							break;
						case 'rt':
							h + y - _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height : h + y - _y,
								top : _y
							}) : obj.css({
								height : HROS.CONFIG.windowMinHeight
							});
							w - x + _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width : w - x + _x
							}) : obj.css({
								width : HROS.CONFIG.windowMinWidth
							});
							break;
						case 'rb':
							w - x + _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width : w - x + _x
							}) : obj.css({
								width : HROS.CONFIG.windowMinWidth
							});
							h - y + _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height : h - y + _y
							}) : obj.css({
								height : HROS.CONFIG.windowMinHeight
							});
							break;
						case 'lt':
							w + x - _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width : w + x - _x,
								left : _x
							}) : obj.css({
								width : HROS.CONFIG.windowMinWidth
							});
							h + y - _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height : h + y - _y,
								top : _y
							}) : obj.css({
								height : HROS.CONFIG.windowMinHeight
							});
							break;
						case 'lb':
							w + x - _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width : w + x - _x,
								left : _x
							}) : obj.css({
								width : HROS.CONFIG.windowMinWidth
							});
							h - y + _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height : h - y + _y
							}) : obj.css({
								height : HROS.CONFIG.windowMinHeight
							});
							break;
					}
				}).on('mouseup',function(){
					if(typeof(lay) !== 'undefined'){
						lay.hide();
					}
					obj.data('info').width = obj.width();
					obj.data('info').height = obj.height();
					obj.data('info').left = obj.offset().left;
					obj.data('info').top = obj.offset().top;
					obj.data('info').emptyW = $(window).width() - obj.width();
					obj.data('info').emptyH = $(window).height() - obj.height();
					$(this).off('mousemove').off('mouseup');
				});
			});
		}
	}
})();