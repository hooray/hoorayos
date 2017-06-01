/*
**  应用窗口
*/
HROS.window = (function(){
	return {
		init: function(){
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
						HROS.popupMenu.app(e, $(this));
						break;
					case 'pwindow':
					case 'pwidget':
						HROS.popupMenu.papp(e, $(this));
						break;
				}
				return false;
			});
		},
		/*
		**  创建窗口
		**  自定义窗口：HROS.window.createTemp({title,url,width,height,resize,isflash});
		**      后面参数依次为：标题、地址、宽、高、是否可拉伸、是否打开默认最大化、是否为flash
		**      示例：HROS.window.createTemp({title:"百度",url:"http://www.baidu.com",width:800,height:400,isresize:false,isopenmax:false,isflash:false});
		*/
		createTemp: function(obj){
			var type = 'window', appid = obj.appid == null ? Date.parse(new Date()): obj.appid;
			//判断窗口是否已打开
			var iswindowopen = false;
			$('#task-content-inner .task-item').each(function(){
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
					$('#task-content-inner').append(HROS.template.task({
						'type': options.type,
						'id': 't_' + options.appid,
						'appid': options.appid,
						'realappid': options.appid,
						'title': options.title,
						'imgsrc': options.imgsrc
					}));
					HROS.taskBar.resize();
					//新增窗口
					var windowData = {
						'top': ($(window).height() - options.height) / 2 <= 0 ? 0: ($(window).height() - options.height) / 2,
						'left': ($(window).width() - options.width) / 2 <= 0 ? 0: ($(window).width() - options.width) / 2,
						'emptyW': $(window).width() - options.width,
						'emptyH': $(window).height() - options.height,
						'width': options.width,
						'height': options.height,
						'zIndex': HROS.CONFIG.windowIndexid,
						'type': options.type,
						'id': 'w_' + options.appid,
						'appid': options.appid,
						'realappid': options.appid,
						'title': options.title,
						'url': options.url,
						'imgsrc': options.imgsrc,
						'isresize': options.isresize,
						'isopenmax': options.isopenmax,
						'istitlebar': options.isresize,
						'istitlebarFullscreen': options.isresize ? window.fullScreenApi.supportsFullScreen == true ? true: false: false,
						'isflash': options.isflash
					};
					$('#desk').append(HROS.template.window(windowData));
					$(windowId).data('info', windowData).css({
						opacity: 0,
						scale: 1.1
					}).transition({
						opacity: 1,
						scale: 1
					}, 200);
					HROS.CONFIG.windowIndexid += 1;
					HROS.window.show2top(options.appid);
				}
				nextDo({
					type: type,
					appid: appid,
					realappid: appid,
					imgsrc: 'static/img/default_icon.png',
					title: obj.title,
					url: obj.url,
					width: obj.width,
					height: obj.height,
					isresize: typeof(obj.isresize) == 'undefined' ? false: obj.isresize,
					isopenmax: typeof(obj.isopenmax) == 'undefined' ? false: obj.isopenmax,
					isflash: typeof(obj.isflash) == 'undefined' ? true: obj.isflash
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
		create: function(appid, type, realappid){
			var type = type == null ? 'window': type;
			//判断窗口是否已打开
			var iswindowopen = false;
			$('#task-content-inner .task-item').each(function(){
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
					var top = ($(window).height() - options.height) / 2 <= 0 ? 0: ($(window).height() - options.height) / 2;
					var left = ($(window).width() - options.width) / 2 <= 0 ? 0: ($(window).width() - options.width) / 2;
					switch(options.type){
						case 'window':
						case 'pwindow':
							//新增任务栏
							$('#task-content-inner').append(HROS.template.task({
								'type': options.type,
								'id': 't_' + options.appid,
								'appid': options.appid,
								'realappid': options.realappid,
								'title': options.title,
								'imgsrc': options.imgsrc
							}));
							HROS.taskBar.resize();
							//新增窗口
							var windowData = {
								'top': top,
								'left': left,
								'emptyW': $(window).width() - options.width,
								'emptyH': $(window).height() - options.height,
								'width': options.width,
								'height': options.height,
								'zIndex': HROS.CONFIG.windowIndexid,
								'type': options.type,
								'id': 'w_' + options.appid,
								'appid': options.appid,
								'realappid': options.realappid,
								'title': options.title,
								'url': options.url,
								'imgsrc': options.imgsrc,
								'isresize': options.isresize == 1 ? true: false,
								'isopenmax': options.isresize == 1 ? options.isopenmax == 1 ? true: false: false,
								'istitlebar': options.isresize == 1 ? true: false,
								'istitlebarFullscreen': options.isresize == 1 ? window.fullScreenApi.supportsFullScreen == true ? true: false: false,
								'isflash': options.isflash == 1 ? true: false
							};
							$('#desk').append(HROS.template.window(windowData));
							$(windowId).data('info', windowData).css({
								opacity: 0,
								scale: 1.1
							}).transition({
								opacity: 1,
								scale: 1
							}, 200);
							HROS.CONFIG.windowIndexid += 1;
							HROS.window.show2top(options.appid);
							break;
						case 'folder':
							//新增任务栏
							$('#task-content-inner').append(HROS.template.task({
								'type': options.type,
								'id': 't_' + options.appid,
								'appid': options.appid,
								'realappid': options.realappid,
								'title': options.title,
								'imgsrc': options.imgsrc
							}));
							HROS.taskBar.resize();
							//新增窗口
							var folderData = {
								'top': top,
								'left': left,
								'emptyW': $(window).width() - options.width,
								'emptyH': $(window).height() - options.height,
								'width': options.width,
								'height': options.height,
								'zIndex': HROS.CONFIG.windowIndexid,
								'type': options.type,
								'id': 'w_' + options.appid,
								'appid': options.appid,
								'realappid': options.realappid,
								'title': options.title,
								'imgsrc': options.imgsrc
							};
							$('#desk').append(HROS.template.folder(folderData));
							$(windowId).data('info', folderData).css({
								opacity: 0,
								scale: 1.1
							}).transition({
								opacity: 1,
								scale: 1
							}, 200);
							HROS.CONFIG.windowIndexid += 1;
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
									folder_append += HROS.template.app({
										'title': this.name,
										'type': this.type,
										'id': 'd_' + this.appid,
										'appid': this.appid,
										'realappid': this.realappid,
										'imgsrc': this.icon,
										'appsize': 48
									});
								});
								$(windowId).find('.folder_body').append(folder_append);
							}
							HROS.window.show2top(options.appid);
							break;
						case 'file':
							var fileData = {
								'top': top,
								'left': left,
								'emptyW': $(window).width() - options.width,
								'emptyH': $(window).height() - options.height,
								'width': options.width,
								'height': options.height,
								'zIndex': HROS.CONFIG.windowIndexid,
								'type': options.type,
								'id': 'w_' + options.appid,
								'appid': options.appid,
								'realappid': options.realappid,
								'title': options.title,
								'imgsrc': options.imgsrc
							};
							$('body').append(HROS.template.fileDownload(fileData));
							break;
					}
				}
				$.ajax({
					data: {
						ac: 'getMyAppById',
						id: appid,
						type: type
					}
				}).done(function(app){
					if(app != null){
						if(app['error'] == 'ERROR_NOT_FOUND'){
							swal({
								type: 'error',
								title: '应用不存在，建议删除',
								timer: 2000,
								showConfirmButton: false
							});
						}else if(app['error'] == 'ERROR_NOT_INSTALLED'){
							HROS.window.createTemp({
								appid: 'hoorayos-yysc',
								title: '应用市场',
								url: 'sysapp/appmarket/index.php?id=' + (realappid == null ? $('#d_' + appid).attr('realappid'): realappid),
								width: 800,
								height: 484,
								isflash: false,
								refresh: true
							});
						}else{
							nextDo({
								type: app['type'],
								appid: app['appid'],
								realappid: app['realappid'],
								title: app['name'],
								imgsrc: app['icon'],
								url: app['url'],
								width: app['width'],
								height: app['height'],
								isresize: app['isresize'],
								isopenmax: app['isopenmax'],
								isflash: app['isflash']
							});
						}
					}else{
						swal({
							type: 'error',
							title: '应用加载失败',
							timer: 2000,
							showConfirmButton: false
						});
					}
					$('#d_' + appid).attr('opening', 0);
				});
			}
		},
		setPos: function(){
			$('#desk .window-container').each(function(){
				var windowdata = $(this).data('info');
				var currentW = $(window).width() - $(this).width();
				var currentH = $(window).height() - $(this).height();
				var left = windowdata['left'] / windowdata['emptyW'] * currentW >= currentW ? currentW: windowdata['left'] / windowdata['emptyW'] * currentW;
				left = left <= 0 ? 0: left;
				var top = windowdata['top'] / windowdata['emptyH'] * currentH >= currentH ? currentH: windowdata['top'] / windowdata['emptyH'] * currentH;
				top = top <= 0 ? 0: top;
				if($(this).attr('state') != 'hide'){
					$(this).stop(true, false).animate({
						'left': left,
						'top': top
					}, 500, function(){
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
		close: function(appid){
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId).css({
				opacity: 1,
				scale: 1
			}).transition({
				opacity: 0,
				scale: 1.1
			}, 200, function(){
				$(this).removeData('info').html('').remove();
			});
			$('#task-content-inner ' + taskId).html('').remove();
			//当所有显示在桌面上的窗口都不处于最大化状态时，则去掉任务栏min-zIndex样式
			var f = true;
			$('#desk .window-container').each(function(){
				if($(this).attr('state') == 'show' && $(this).attr('ismax') == 1){
					f = false;
				}
			});
			if(f){
				$('#task-bar').removeClass('min-zIndex');
			}
			HROS.taskBar.resize();
		},
		closeAll: function(){
			$('#desk .window-container').each(function(){
				HROS.window.close($(this).attr('appid'));
			});
		},
		hide: function(appid){
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId).css({
				opacity: 1,
				scale: 1,
				y: 0
			}).transition({
				opacity: 0,
				scale: 0.9,
				y: 50
			}, 200, function(){
				$(this).css('left', -10000).attr('state', 'hide');
			});
			$('#task-content-inner ' + taskId).removeClass('task-item-current');
			if($(windowId).attr('ismax') == 1){
				$('#task-bar').removeClass('min-zIndex');
			}
		},
		hideAll: function(){
			$('#task-content-inner .task-item').removeClass('task-item-current');
			$('#desk-' + HROS.CONFIG.desk).nextAll('div.window-container').css('left', -10000).attr('state', 'hide');
		},
		max: function(appid){
			HROS.window.show2top(appid);
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			$(windowId + ' .title-handle .ha-max').hide().next(".ha-revert").show();
			$(windowId).addClass('window-maximize').attr('ismax',1).animate({
				width: '100%',
				height: '100%',
				top: 0,
				left: 0
			}, 200);
			$('#task-bar').addClass('min-zIndex');
		},
		revert: function(appid){
			HROS.window.show2top(appid);
			var windowId = '#w_' + appid, taskId = '#t_' + appid, windowdata = $(windowId).data('info');
			$(windowId + ' .title-handle .ha-revert').hide().prev('.ha-max').show();
			$(windowId).removeClass('window-maximize').attr('ismax',0).animate({
				width: windowdata['width'],
				height: windowdata['height'],
				left: windowdata['left'],
				top: windowdata['top']
			}, 500);
			$('#task-bar').removeClass('min-zIndex');
		},
		refresh: function(appid){
			HROS.window.show2top(appid);
			var windowId = '#w_' + appid, taskId = '#t_' + appid;
			//判断是应用窗口，还是文件夹窗口
			if($(windowId + '_iframe').length != 0){
				$(windowId + '_iframe').attr('src', $(windowId + '_iframe').attr('src'));
			}else{
				HROS.window.updateFolder(appid);
			}
		},
		show2top: function(appid, isAnimate){
			isAnimate = isAnimate == null ? false: isAnimate;
			var windowId = '#w_' + appid, taskId = '#t_' + appid, windowdata = $(windowId).data('info');
			var arr = [];
			function show(){
				HROS.window.show2under();
				//改变当前任务栏样式
				$('#task-content-inner ' + taskId).addClass('task-item-current');
				if($(windowId).attr('ismax') == 1){
					$('#task-bar').addClass('min-zIndex');
				}
				//改变当前窗口样式
				$(windowId).addClass('window-current').css({
					zIndex: HROS.CONFIG.windowIndexid,
					left: windowdata['left'],
					top: windowdata['top']
				});
				//如果窗口最小化前是最大化状态的，则坐标位置设为0
				if($(windowId).attr('ismax') == 1){
					$(windowId).css({
						left: 0,
						top: 0
					});
				}
				//改变当前窗口遮罩层样式
				$(windowId + ' .window-mask').hide();
				//改变当前iframe显示
				$(windowId + ' iframe').show();
				HROS.CONFIG.windowIndexid += 1;
				if($(windowId).attr('state') == 'hide'){
					$(windowId).css({
						opacity: 0,
						scale: 0.9,
						y: 50
					}).transition({
						opacity: 1,
						scale: 1,
						y: 0
					}, 200, function(){
						$(this).attr('state', 'show');
					});
				}
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
							id: $(this).attr('id'),
							direction: direction, //移动方向
							distance: distance //移动距离
						});
					}
				});
				//开始移动
				var delayTime = 0;
				for(var i = 0; i < arr.length; i++){
					var baseLeft = $('#' + arr[i].id).offset().left, baseTop = $('#' + arr[i].id).offset().top;
					if(arr[i].direction == 'left'){
						$('#' + arr[i].id).delay(delayTime).animate({
							left: baseLeft - arr[i].distance
						}, 300).animate({
							left: baseLeft
						}, 300);
					}else if(arr[i].direction == 'right'){
						$('#' + arr[i].id).delay(delayTime).animate({
							left: baseLeft + arr[i].distance
						}, 300).animate({
							left: baseLeft
						}, 300);
					}else if(arr[i].direction == 'top'){
						$('#' + arr[i].id).delay(delayTime).animate({
							top: baseTop - arr[i].distance
						}, 300).animate({
							top: baseTop
						}, 300);
					}else if(arr[i].direction == 'bottom'){
						$('#' + arr[i].id).delay(delayTime).animate({
							top: baseTop + arr[i].distance
						}, 300).animate({
							top: baseTop
						}, 300);
					}
					delayTime += 100;
				}
				setTimeout(show, delayTime + 100);
			}else{
				show();
			}
		},
		show2under: function(){
			//改变任务栏样式
			$('#task-content-inner .task-item').removeClass('task-item-current');
			//改变窗口样式
			$('#desk .window-container').removeClass('window-current');
			//改变窗口遮罩层样式
			$('#desk .window-container .window-mask').show();
			//改变iframe显示
			$('#desk .window-container-flash iframe').hide();
		},
		updateFolder: function(appid){
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
					folder_append += HROS.template.app({
						'top': 0,
						'left': 0,
						'title': sc[i]['name'],
						'type': sc[i]['type'],
						'id': 'd_' + sc[i]['appid'],
						'appid': sc[i]['appid'],
						'realappid': sc[i]['realappid'],
						'imgsrc': sc[i]['icon'],
						'appsize': 48
					});
				}
				$(windowId).find('.folder_body').html('').append(folder_append).on('contextmenu', '.appbtn', function(e){
					$('.popup-menu').hide();
					$('.quick_view_container').remove();
					HROS.popupMenu.app(e, $(this));
					return false;
				});
			}
		},
		handle: function(){
			$('#desk').on('mousedown', '.window-container .title-bar .title-handle a, .window-container .set-bar a', function(e){
				e.preventDefault();
				e.stopPropagation();
			});
			$('#desk').on('dblclick', '.window-container .title-bar .title', function(e){
				console.log(1);
				var obj = $(this).parents('.window-container');
				//判断当前窗口是否已经是最大化
				if(obj.find('.ha-max').is(':hidden')){
					obj.find('.ha-revert').click();
				}else{
					obj.find('.ha-max').click();
				}
			}).on('click', '.window-container .set-bar', function(){
				var obj = $(this).parents('.window-container');
				HROS.window.show2top(obj.attr('appid'));
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
						appid: 'hoorayos-yysc',
						title: '应用市场',
						url: 'sysapp/appmarket/index.php?id=' + obj.attr('realappid'),
						width: 800,
						height: 484,
						isflash: false,
						refresh: true
					});
				}else{
					swal({
						type: 'warning',
						title: '温馨提示',
						text: '该应用没有任何详细介绍',
						timer: 2000,
						showConfirmButton: false
					});
				}
			}).on('contextmenu', '.window-container', function(){
				$('.popup-menu').hide();
				$('.quick_view_container').remove();
				return false;
			});
		},
		move: function(){
			$('#desk').on('mousedown', '.window-container .title-bar, .window-container .set-bar', function(e){
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
						width: windowdata['width'],
						height: windowdata['height'],
						left: e.clientX - x,
						top: e.clientY - y <= 10 ? 0: e.clientY - y >= lay.height()-30 ? lay.height()-30: e.clientY - y
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
		resize: function(obj){
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
					var _x = e.clientX <= 10 ? 0: e.clientX >= (lay.width() - 12) ? (lay.width() - 2): e.clientX;
					var _y = e.clientY <= 10 ? 0: e.clientY >= (lay.height() - 12) ? lay.height(): e.clientY;
					switch(resizeobj.attr('resize')){
						case 't':
							h + y - _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height: h + y - _y,
								top: _y
							}) : obj.css({
								height: HROS.CONFIG.windowMinHeight
							});
							break;
						case 'r':
							w - x + _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width: w - x + _x
							}) : obj.css({
								width: HROS.CONFIG.windowMinWidth
							});
							break;
						case 'b':
							h - y + _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height: h - y + _y
							}) : obj.css({
								height: HROS.CONFIG.windowMinHeight
							});
							break;
						case 'l':
							w + x - _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width: w + x - _x,
								left: _x
							}) : obj.css({
								width: HROS.CONFIG.windowMinWidth
							});
							break;
						case 'rt':
							h + y - _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height: h + y - _y,
								top: _y
							}) : obj.css({
								height: HROS.CONFIG.windowMinHeight
							});
							w - x + _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width: w - x + _x
							}) : obj.css({
								width: HROS.CONFIG.windowMinWidth
							});
							break;
						case 'rb':
							w - x + _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width: w - x + _x
							}) : obj.css({
								width: HROS.CONFIG.windowMinWidth
							});
							h - y + _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height: h - y + _y
							}) : obj.css({
								height: HROS.CONFIG.windowMinHeight
							});
							break;
						case 'lt':
							w + x - _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width: w + x - _x,
								left: _x
							}) : obj.css({
								width: HROS.CONFIG.windowMinWidth
							});
							h + y - _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height: h + y - _y,
								top: _y
							}) : obj.css({
								height: HROS.CONFIG.windowMinHeight
							});
							break;
						case 'lb':
							w + x - _x > HROS.CONFIG.windowMinWidth ? obj.css({
								width: w + x - _x,
								left: _x
							}) : obj.css({
								width: HROS.CONFIG.windowMinWidth
							});
							h - y + _y > HROS.CONFIG.windowMinHeight ? obj.css({
								height: h - y + _y
							}) : obj.css({
								height: HROS.CONFIG.windowMinHeight
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