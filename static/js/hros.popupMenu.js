/*
**  右键菜单
*/
HROS.popupMenu = (function(){
	return {
		init: function(){
			$('.popup-menu').on('contextmenu', function(){
				return false;
			});
			//动态控制多级菜单的显示位置
			$('body').on('mouseenter', '.popup-menu li', function(){
				if($(this).children('.popup-menu').length == 1){
					$(this).children('a').addClass('focus');
					$(this).children('.popup-menu').show();
					if($(this).parents('.popup-menu').offset().left + $(this).parents('.popup-menu').outerWidth() * 2 + 10 < $(window).width()){
						$(this).children('.popup-menu').css({
							left: $(this).parents('.popup-menu').outerWidth() - 2,
							top: 0
						});
					}else{
						$(this).children('.popup-menu').css({
							left: $(this).parents('.popup-menu').outerWidth() * -1,
							top: 0
						});
					}
					if($(this).children('.popup-menu').offset().top + $(this).children('.popup-menu').height() + 10 > $(window).height()){
						$(this).children('.popup-menu').css({
							top: $(window).height() - $(this).children('.popup-menu').height() - $(this).children('.popup-menu').offset().top - 10
						});
					}
				}
			}).on('mouseleave', '.popup-menu li', function(){
				$(this).children('a').removeClass('focus');
				$(this).children('.popup-menu').hide();
			});
		},
		/*
		**  应用右键
		*/
		app: function(obj){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.app){
				HROS.popupMenuCache.app = $(
					'<div class="popup-menu app-menu"><ul>'+
						'<li><a menu="open" href="javascript:;"><i class="fa fa-fw fa-external-link"></i>打开</a></li>'+
						'<li class="separator"></li>'+
						'<li>'+
							'<a menu="move" href="javascript:;"><i class="fa fa-fw fa-share"></i>移动到<i class="fa fa-caret-right arrow"></i></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="moveto" desk="1" href="javascript:;"><i class="fa fa-fw fa-check"></i>桌面1</a></li>'+
								'<li><a menu="moveto" desk="2" href="javascript:;"><i class="fa fa-fw fa-check"></i>桌面2</a></li>'+
								'<li><a menu="moveto" desk="3" href="javascript:;"><i class="fa fa-fw fa-check"></i>桌面3</a></li>'+
								'<li><a menu="moveto" desk="4" href="javascript:;"><i class="fa fa-fw fa-check"></i>桌面4</a></li>'+
								'<li><a menu="moveto" desk="5" href="javascript:;"><i class="fa fa-fw fa-check"></i>桌面5</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="edit" href="javascript:;"><i class="fa fa-fw fa-pencil"></i>编辑</a></li>'+
						'<li><a menu="del" href="javascript:;"><i class="fa fa-fw fa-trash"></i>卸载</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.app);
			}
			$('.app-menu a[menu="moveto"]').removeClass('disabled').children('i.fa-check').hide();
			if(obj.parent().hasClass('desktop-apps-container')){
				$('.app-menu a[menu="moveto"]').each(function(){
					if($(this).attr('desk') == HROS.CONFIG.desk){
						$(this).addClass('disabled').children('i.fa-check').show();
					}
				});
			}
			//绑定事件
			$('.app-menu a[menu="moveto"]:not(.disabled)').off('click').on('click', function(){
				var id = obj.attr('appid'),
				from = obj.index(),
				to = 99999,
				todesk = $(this).attr('desk'),
				fromdesk = HROS.CONFIG.desk,
				fromfolderid = obj.parents('.folder-window').attr('appid') || obj.parents('.quick_view_container').attr('appid');
				if(HROS.base.checkLogin()){
					if(!HROS.app.checkIsMoving()){
						var rtn = false;
						if(obj.parent().hasClass('dock-applist')){
							if(HROS.app.dataDockToOtherdesk(id, from, todesk)){
								$.ajax({
									data: {
										ac: 'moveMyApp',
										movetype: 'dock-otherdesk',
										id: id,
										from: from,
										todesk: todesk
									}
								}).done(function(responseText){
									HROS.VAR.isAppMoving = false;
								});
							}
						}else if(obj.parent().hasClass('desktop-apps-container')){
							if(HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk)){
								$.ajax({
									data: {
										ac: 'moveMyApp',
										movetype: 'desk-otherdesk',
										id: id,
										from: from,
										to: to,
										todesk: todesk,
										fromdesk: fromdesk
									}
								}).done(function(responseText){
									HROS.VAR.isAppMoving = false;
								});
							}
						}else{
							if(HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid)){
								$.ajax({
									data: {
										ac: 'moveMyApp',
										movetype: 'folder-otherdesk',
										id: id,
										from: from,
										todesk: todesk,
										fromfolderid: fromfolderid
									}
								}).done(function(responseText){
									HROS.VAR.isAppMoving = false;
								});
							}
						}
					}
				}else{
					if(obj.parent().hasClass('dock-applist')){
						HROS.app.dataDockToOtherdesk(id, from, todesk);
					}else if(obj.parent().hasClass('desktop-apps-container')){
						HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk);
					}else{
						HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid);
					}
				}
				$('.popup-menu').hide();
			});
			$('.app-menu a[menu="open"]').off('click').on('click', function(){
				switch(obj.attr('type')){
					case 'window':
						HROS.window.create(obj.attr('appid'), obj.attr('type'));
						break;
					case 'widget':
						HROS.widget.create(obj.attr('appid'), obj.attr('type'));
						break;
				}
				$('.popup-menu').hide();
			});
			$('.app-menu a[menu="edit"]').off('click').on('click', function(){
				if(HROS.base.checkLogin()){
					dialog({
						id: 'editdialog',
						title: '编辑应用“' + obj.children('span').text() + '”',
						url: 'sysapp/dialog/app.php?id=' + obj.attr('appid'),
						padding: 0,
						width: 770,
						height: 450
					}).showModal();
				}else{
					HROS.base.login();
				}
				$('.popup-menu').hide();
			});
			$('.app-menu a[menu="del"]').off('click').on('click', function(){
				HROS.app.dataDeleteByAppid(obj.attr('appid'));
				HROS.widget.removeCookie(obj.attr('realappid'), obj.attr('type'));
				HROS.app.remove(obj.attr('appid'), function(){
					obj.find('img, span').show().animate({
						opacity: 'toggle',
						width: 0,
						height: 0
					}, 500, function(){
						obj.remove();
						HROS.deskTop.resize();
					});
				});
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.app;
		},
		papp: function(obj){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.papp){
				HROS.popupMenuCache.papp = $(
					'<div class="popup-menu papp-menu"><ul>'+
						'<li><a menu="open" href="javascript:;"><i class="fa fa-fw fa-external-link"></i>打开</a></li>'+
						'<li class="separator"></li>'+
						'<li>'+
							'<a menu="move" href="javascript:;"><i class="fa fa-fw fa-share"></i>移动到<i class="fa fa-caret-right arrow"></i></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="moveto" desk="1" href="javascript:;">桌面1</a></li>'+
								'<li><a menu="moveto" desk="2" href="javascript:;">桌面2</a></li>'+
								'<li><a menu="moveto" desk="3" href="javascript:;">桌面3</a></li>'+
								'<li><a menu="moveto" desk="4" href="javascript:;">桌面4</a></li>'+
								'<li><a menu="moveto" desk="5" href="javascript:;">桌面5</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="edit" href="javascript:;"><i class="fa fa-fw fa-pencil"></i>编辑</a></li>'+
						'<li><a menu="del" href="javascript:;"><i class="fa fa-fw fa-trash"></i>删除</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.papp);
			}
			$('.papp-menu a[menu="moveto"]').removeClass('disabled');
			if(obj.parent().hasClass('desktop-apps-container')){
				$('.papp-menu a[menu="moveto"]').each(function(){
					if($(this).attr('desk') == HROS.CONFIG.desk){
						$(this).addClass('disabled');
					}
				});
			}
			//绑定事件
			$('.papp-menu a[menu="moveto"]:not(.disabled)').off('click').on('click', function(){
				var id = obj.attr('appid'),
				from = obj.index(),
				to = 99999,
				todesk = $(this).attr('desk'),
				fromdesk = HROS.CONFIG.desk,
				fromfolderid = obj.parents('.folder-window').attr('appid') || obj.parents('.quick_view_container').attr('appid');
				if(HROS.base.checkLogin()){
					var rtn = false;
					if(obj.parent().hasClass('dock-applist')){
						if(HROS.app.dataDockToOtherdesk(id, from, todesk)){
							$.ajax({
								data: {
									ac: 'moveMyApp',
									movetype: 'dock-otherdesk',
									id: id,
									from: from,
									todesk: todesk
								}
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else if(obj.parent().hasClass('desktop-apps-container')){
						if(HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk)){
							$.ajax({
								data: {
									ac: 'moveMyApp',
									movetype: 'desk-otherdesk',
									id: id,
									from: from,
									to: to,
									todesk: todesk,
									fromdesk: fromdesk
								}
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else{
						if(HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid)){
							$.ajax({
								data: {
									ac: 'moveMyApp',
									movetype: 'folder-otherdesk',
									id: id,
									from: from,
									todesk: todesk,
									fromfolderid: fromfolderid
								}
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}
				}else{
					if(obj.parent().hasClass('dock-applist')){
						HROS.app.dataDockToOtherdesk(id, from, todesk);
					}else if(obj.parent().hasClass('desktop-apps-container')){
						HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk);
					}else{
						HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid);
					}
				}
				$('.popup-menu').hide();
			});
			$('.papp-menu a[menu="open"]').off('click').on('click', function(){
				switch(obj.attr('type')){
					case 'pwindow':
						HROS.window.create(obj.attr('appid'), obj.attr('type'));
						break;
					case 'pwidget':
						HROS.widget.create(obj.attr('appid'), obj.attr('type'));
						break;
				}
				$('.popup-menu').hide();
			});
			$('.papp-menu a[menu="edit"]').off('click').on('click', function(){
				if(HROS.base.checkLogin()){
					dialog({
						id: 'editdialog',
						title: '编辑私人应用“' + obj.children('span').text() + '”',
						url: 'sysapp/dialog/papp.php?id=' + obj.attr('appid'),
						padding: 0,
						width: 770,
						height: 450
					}).showModal();
				}else{
					HROS.base.login();
				}
				$('.popup-menu').hide();
			});
			$('.papp-menu a[menu="del"]').off('click').on('click', function(){
				HROS.app.dataDeleteByAppid(obj.attr('appid'));
				HROS.widget.removeCookie(obj.attr('realappid'), obj.attr('type'));
				HROS.app.remove(obj.attr('appid'), function(){
					obj.find('img, span').show().animate({
						opacity: 'toggle',
						width: 0,
						height: 0
					}, 500, function(){
						obj.remove();
						HROS.deskTop.resize();
					});
				});
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.papp;
		},
		/*
		**  文件夹右键
		*/
		folder: function(obj){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.folder){
				HROS.popupMenuCache.folder = $(
					'<div class="popup-menu folder-menu"><ul>'+
						'<li><a menu="view" href="javascript:;"><i class="fa fa-fw fa-eye"></i>预览</a></li>'+
						'<li><a menu="open" href="javascript:;"><i class="fa fa-fw fa-folder-open-o"></i>打开</a></li>'+
						'<li class="separator"></li>'+
						'<li>'+
							'<a menu="move" href="javascript:;"><i class="fa fa-fw fa-share"></i>移动到<i class="fa fa-caret-right arrow"></i></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="moveto" desk="1" href="javascript:;">桌面1</a></li>'+
								'<li><a menu="moveto" desk="2" href="javascript:;">桌面2</a></li>'+
								'<li><a menu="moveto" desk="3" href="javascript:;">桌面3</a></li>'+
								'<li><a menu="moveto" desk="4" href="javascript:;">桌面4</a></li>'+
								'<li><a menu="moveto" desk="5" href="javascript:;">桌面5</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="rename" href="javascript:;"><i class="fa fa-fw fa-pencil"></i>重命名</a></li>'+
						'<li><a menu="del" href="javascript:;"><i class="fa fa-fw fa-trash"></i>删除</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.folder);
			}
			$('.folder-menu a[menu="moveto"]').removeClass('disabled');
			if(obj.parent().hasClass('desktop-apps-container')){
				$('.folder-menu a[menu="moveto"]').each(function(){
					if($(this).attr('desk') == HROS.CONFIG.desk){
						$(this).addClass('disabled');
					}
				});
			}
			//绑定事件
			$('.folder-menu a[menu="view"]').off('click').on('click', function(){
				HROS.folderView.get(obj);
				$('.popup-menu').hide();
			});
			$('.folder-menu a[menu="open"]').off('click').on('click', function(){
				HROS.window.create(obj.attr('appid'), obj.attr('type'));
				$('.popup-menu').hide();
			});
			$('.folder-menu a[menu="moveto"]:not(.disabled)').off('click').on('click', function(){
				var id = obj.attr('appid'),
				from = obj.index(),
				to = 99999,
				todesk = $(this).attr('desk'),
				fromdesk = HROS.CONFIG.desk,
				fromfolderid = obj.parents('.folder-window').attr('appid') || obj.parents('.quick_view_container').attr('appid');
				if(HROS.base.checkLogin()){
					var rtn = false;
					if(obj.parent().hasClass('dock-applist')){
						if(HROS.app.dataDockToOtherdesk(id, from, todesk)){
							$.ajax({
								data: {
									ac: 'moveMyApp',
									movetype: 'dock-otherdesk',
									id: id,
									from: from,
									todesk: todesk
								}
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else if(obj.parent().hasClass('desktop-apps-container')){
						if(HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk)){
							$.ajax({
								data: {
									ac: 'moveMyApp',
									movetype: 'desk-otherdesk',
									id: id,
									from: from,
									to: to,
									todesk: todesk,
									fromdesk: fromdesk
								}
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else{
						if(HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid)){
							$.ajax({
								data: {
									ac: 'moveMyApp',
									movetype: 'folder-otherdesk',
									id: id,
									from: from,
									todesk: todesk,
									fromfolderid: fromfolderid
								}
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}
				}else{
					if(obj.parent().hasClass('dock-applist')){
						HROS.app.dataDockToOtherdesk(id, from, todesk);
					}else if(obj.parent().hasClass('desktop-apps-container')){
						HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk);
					}else{
						HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid);
					}
				}
				$('.popup-menu').hide();
			});
			$('.folder-menu a[menu="rename"]').off('click').on('click', function(){
				if(HROS.base.checkLogin()){
					swal({
						type: 'input',
						title: '重命名“' + obj.find('span').text() + '”文件夹',
						showCancelButton: true,
						closeOnConfirm: false,
						confirmButtonText: '修改',
						cancelButtonText: '取消',
						animation: 'slide-from-top',
						inputPlaceholder: '请输入文件夹名称',
						inputValue: obj.find('span').text()
					}, function(inputValue){
						if(inputValue === false){
							return false;
						}
						if(inputValue === ''){
							swal.showInputError('文件夹名称不能为空');
							return false;
						}
						$.ajax({
							data: {
								ac: 'updateFolder',
								name: inputValue,
								id: obj.attr('appid')
							}
						}).done(function(responseText){
							HROS.app.get();
							swal({
								type: 'success',
								title: '修改成功'
							});
						});
					});
				}else{
					HROS.base.login();
				}
				$('.popup-menu').hide();
			});
			$('.folder-menu a[menu="del"]').off('click').on('click', function(){
				swal({
					type: 'warning',
					title: '删除“' + obj.find('span').text() + '”文件夹',
					text: '删除文件夹的同时会删除文件夹内所有应用，确认要删除么？',
					showCancelButton: true,
					confirmButtonText: '确认删除',
					cancelButtonText: '我点错了'
				}, function(){
					HROS.app.remove(obj.attr('appid'), function(){
						HROS.app.dataDeleteByAppid(obj.attr('appid'));
						obj.find('img, span').show().animate({
							opacity: 'toggle',
							width: 0,
							height: 0
						}, 500, function(){
							obj.remove();
							HROS.deskTop.resize();
						});
					});
				});
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.folder;
		},
		/*
		**  文件右键
		*/
		file: function(obj){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.file){
				HROS.popupMenuCache.file = $(
					'<div class="popup-menu file-menu"><ul>'+
						'<li><a menu="download" href="javascript:;"><i class="fa fa-fw fa-cloud-download"></i>下载</a></li>'+
						'<li class="separator"></li>'+
						'<li><a menu="del" href="javascript:;"><i class="fa fa-fw fa-trash"></i>删除</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.file);
			}
			//绑定事件
			$('.file-menu a[menu="download"]').off('click').on('click', function(){
				$('body').append(HROS.template.fileDownload({
					appid: obj.attr('appid')
				}));
				$('.popup-menu').hide();
			});
			$('.file-menu a[menu="del"]').off('click').on('click', function(){
				HROS.app.dataDeleteByAppid(obj.attr('appid'));
				HROS.app.remove(obj.attr('appid'), function(){
					obj.find('img, span').show().animate({
						opacity: 'toggle',
						width: 0,
						height: 0
					}, 500, function(){
						obj.remove();
						HROS.deskTop.resize();
					});
				});
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.file;
		},
		/*
		**  应用码头右键
		*/
		dock: function(){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.dock){
				HROS.popupMenuCache.dock = $(
					'<div class="popup-menu dock-menu"><ul>'+
						'<li><a menu="dockPos" pos="top" href="javascript:;"><i class="fa fa-fw fa-check"></i>向上停靠</a></li>'+
						'<li><a menu="dockPos" pos="left" href="javascript:;"><i class="fa fa-fw fa-check"></i>向左停靠</a></li>'+
						'<li><a menu="dockPos" pos="right" href="javascript:;"><i class="fa fa-fw fa-check"></i>向右停靠</a></li>'+
						'<li class="separator"></li>'+
						'<li><a menu="dockPos" pos="none" href="javascript:;"><i class="fa fa-fw fa-eye-slash"></i>隐藏</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.dock);
				//绑定事件
				$('.dock-menu a[menu="dockPos"]').on('click', function(){
					if($(this).attr('pos') == 'none'){
						if(Cookies.get(cookie_prefix + 'isfirsthidedock' + HROS.CONFIG.memberID) == null){
							Cookies.set(cookie_prefix + 'isfirsthidedock' + HROS.CONFIG.memberID, 1);
							swal({
								type: 'warning',
								title: '温馨提示',
								text: '如果应用码头存在应用，隐藏后会将应用转移到当前桌面<br>若需要再次开启，可在桌面空白处点击右键<br>进入「 桌面设置 」里开启',
								html: true,
								confirmButtonText: '我知道了'
							}, function(){
								HROS.dock.updatePos('none');
							});
						}else{
							HROS.dock.updatePos('none');
						}
					}else{
						HROS.dock.updatePos($(this).attr('pos'));
					}
					$('.popup-menu').hide();
				});
			}
			$('.dock-menu a[menu="dockPos"]').each(function(){
				$(this).children('i.fa-check').hide();
				if($(this).attr('pos') == HROS.CONFIG.dockPos){
					$(this).children('i.fa-check').show();
				}
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.dock;
		},
		/*
		**  任务栏右键
		*/
		task: function(obj){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.task){
				HROS.popupMenuCache.task = $(
					'<div class="popup-menu task-menu"><ul>'+
						'<li><a menu="show" href="javascript:;"><i class="fa fa-fw fa-caret-up"></i>还原</a></li>'+
						'<li><a menu="hide" href="javascript:;"><i class="fa fa-fw fa-caret-down"></i>最小化</a></li>'+
						'<li class="separator"></li>'+
						'<li><a menu="close" href="javascript:;"><i class="fa fa-fw fa-close"></i>关闭</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.task);
			}
			if($('#w_' + obj.attr('appid')).attr('state') == 'hide'){
				$('.task-menu a[menu="show"]').parent().show();
				$('.task-menu a[menu="hide"]').parent().hide();
			}else{
				$('.task-menu a[menu="show"]').parent().hide();
				$('.task-menu a[menu="hide"]').parent().show();
			}
			//绑定事件
			$('.task-menu a[menu="show"]').off('click').on('click', function(){
				HROS.window.show2top(obj.attr('appid'));
				$('.popup-menu').hide();
			});
			$('.task-menu a[menu="hide"]').off('click').on('click', function(){
				HROS.window.hide(obj.attr('appid'));
				$('.popup-menu').hide();
			});
			$('.task-menu a[menu="close"]').off('click').on('click', function(){
				HROS.window.close(obj.attr('appid'));
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.task;
		},
		/*
		**  桌面右键
		*/
		desk: function(){
			HROS.window.show2under();
			if(!HROS.popupMenuCache.desk){
				HROS.popupMenuCache.desk = $(
					'<div class="popup-menu desk-menu"><ul>'+
						'<li><a menu="hideall" href="javascript:;"><i class="fa fa-fw fa-desktop"></i>显示桌面</a></li>'+
						'<li><a menu="closeall" href="javascript:;"><i class="fa fa-fw fa-close"></i>关闭所有窗口</a></li>'+
						'<li class="separator"></li>'+
						'<li>'+
							'<a href="javascript:;"><i class="fa fa-fw fa-folder-o"></i>新建<i class="fa fa-caret-right arrow"></i></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="addfolder" href="javascript:;"><i class="fa fa-fw fa-folder-o"></i>新建文件夹</a></li>'+
								'<li><a menu="addpapp" href="javascript:;"><i class="fa fa-fw fa-edge"></i>新建私人应用</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="uploadfile" href="javascript:;"><i class="fa fa-fw fa-cloud-upload"></i>上传文件</a></li>'+
						'<li class="separator"></li>'+
						'<li><a menu="themes" href="javascript:;"><i class="fa fa-fw fa-photo"></i>主题设置</a></li>'+
						'<li><a menu="setting" href="javascript:;"><i class="fa fa-fw fa-cog"></i>桌面设置</a></li>'+
						'<li>'+
							'<a href="javascript:;"><i class="fa fa-fw fa-th"></i>图标设置<i class="fa fa-caret-right arrow"></i></a>'+
							'<div class="popup-menu"><ul>'+
								'<li>'+
									'<a href="javascript:;"><i class="fa fa-fw fa-th"></i>排列<i class="fa fa-caret-right arrow"></i></a>'+
									'<div class="popup-menu"><ul>'+
										'<li><a menu="orderby" orderby="x" href="javascript:;"><i class="fa fa-fw fa-check"></i>横向排列</a></li>'+
										'<li><a menu="orderby" orderby="y" href="javascript:;"><i class="fa fa-fw fa-check"></i>纵向排列</a></li>'+
									'</ul></div>'+
								'</li>'+
								'<li>'+
									'<a href="javascript:;"><i class="fa fa-fw fa-square-o"></i>尺寸<i class="fa fa-caret-right arrow"></i></a>'+
									'<div class="popup-menu"><ul>'+
										'<li><a menu="size" size="32" href="javascript:;"><i class="fa fa-fw fa-check"></i>小图标</a></li>'+
										'<li><a menu="size" size="48" href="javascript:;"><i class="fa fa-fw fa-check"></i>常规图标</a></li>'+
										'<li><a menu="size" size="64" href="javascript:;"><i class="fa fa-fw fa-check"></i>大图标</a></li>'+
									'</ul></div>'+
								'</li>'+
							'</ul></div>'+
						'</li>'+
						'<li class="separator"></li>'+
						'<li><a menu="lock" href="javascript:;"><i class="fa fa-fw fa-lock"></i>锁定</a></li>'+
						'<li><a menu="logout" href="javascript:;"><i class="fa fa-fw fa-sign-out"></i>注销</a></li>'+
					'</ul></div>'
				);
				$('body').append(HROS.popupMenuCache.desk);
				if(!HROS.base.checkLogin()){
					$('body .desk-menu li a[menu="logout"]').parent().remove();
				}
				//绑定事件
				$('.desk-menu a[menu="orderby"]').on('click', function(){
					HROS.app.updateXY($(this).attr('orderby'));
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="size"]').on('click', function(){
					HROS.app.updateSize($(this).attr('size'));
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="hideall"]').on('click', function(){
					HROS.window.hideAll();
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="closeall"]').on('click', function(){
					HROS.window.closeAll();
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="addfolder"]').on('click', function(){
					if(HROS.base.checkLogin()){
						swal({
							type: 'input',
							title: '新建文件夹',
							showCancelButton: true,
							closeOnConfirm: false,
							confirmButtonText: '创建',
							cancelButtonText: '取消',
							animation: 'slide-from-top',
							inputPlaceholder: '请输入文件夹名称'
						}, function(inputValue){
							if(inputValue === false){
								return false;
							}
							if(inputValue === ''){
								swal.showInputError('文件夹名称不能为空');
								return false;
							}
							$.ajax({
								data: {
									ac: 'addFolder',
									name: inputValue,
									desk: HROS.CONFIG.desk
								}
							}).done(function(responseText){
								HROS.app.get();
								swal({
									type: 'success',
									title: '创建成功',
									timer: 2000,
									showConfirmButton: false
								});
							});
						});
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="addpapp"]').on('click', function(){
					if(HROS.base.checkLogin()){
						dialog({
							id: 'editdialog',
							title: '新建私人应用',
							url: 'sysapp/dialog/papp.php?desk=' + HROS.CONFIG.desk,
							padding: 0,
							width: 770,
							height: 450
						}).showModal();
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="uploadfile"]').on('click', function(){
					HROS.window.createTemp({
						appid: 'hoorayos-scwj',
						title: '上传文件',
						url: 'sysapp/upload/index.php',
						width: 750,
						height: 600,
						isflash: false
					});
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="themes"]').on('click', function(){
					if(HROS.base.checkLogin()){
						HROS.window.createTemp({
							appid: 'hoorayos-ztsz',
							title: '主题设置',
							url: 'sysapp/wallpaper/index.php',
							width: 580,
							height: 520,
							isflash: false
						});
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="setting"]').on('click', function(){
					if(HROS.base.checkLogin()){
						HROS.window.createTemp({
							appid: 'hoorayos-zmsz',
							title: '桌面设置',
							url: 'sysapp/desksetting/index.php',
							width: 800,
							height: 500,
							isflash: false
						});
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="lock"]').on('click', function(){
					HROS.lock.show();
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="logout"]').on('click', function(){
					HROS.base.logout();
					$('.popup-menu').hide();
				});
			}
			$('.desk-menu a[menu="orderby"]').each(function(){
				$(this).children('.fa-check').hide();
				if($(this).attr('orderby') == HROS.CONFIG.appXY){
					$(this).children('.fa-check').show();
				}
				$('.popup-menu').hide();
			});
			$('.desk-menu a[menu="size"]').each(function(){
				$(this).children('.fa-check').hide();
				if($(this).attr('size') == HROS.CONFIG.appSize){
					$(this).children('.fa-check').show();
				}
				$('.popup-menu').hide();
			});
			return HROS.popupMenuCache.desk;
		},
		hide: function(){
			$('.popup-menu').hide();
		}
	}
})();
