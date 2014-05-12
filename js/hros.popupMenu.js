/*
**  右键菜单
*/
HROS.popupMenu = (function(){
	return {
		init : function(){
			$('.popup-menu').on('contextmenu', function(){
				return false;
			});
			//动态控制多级菜单的显示位置
			$('body').on('mouseenter', '.popup-menu li', function(){
				if($(this).children('.popup-menu').length == 1){
					$(this).children('a').addClass('focus');
					$(this).children('.popup-menu').show();
					if($(this).parents('.popup-menu').offset().left + $(this).parents('.popup-menu').width() * 2 + 10 < $(window).width()){
						$(this).children('.popup-menu').css({
							left : $(this).parents('.popup-menu').width() - 5,
							top : 0
						});
					}else{
						$(this).children('.popup-menu').css({
							left : -1 * $(this).parents('.popup-menu').width(),
							top : 0
						});
					}
					if($(this).children('.popup-menu').offset().top + $(this).children('.popup-menu').height() + 10 > $(window).height()){
						$(this).children('.popup-menu').css({
							top : $(window).height() - $(this).children('.popup-menu').height() - $(this).children('.popup-menu').offset().top - 10
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
		app : function(obj){
			HROS.window.show2under();
			if(!TEMP.popupMenuApp){
				TEMP.popupMenuApp = $(
					'<div class="popup-menu app-menu"><ul>'+
						'<li style="border-bottom:1px solid #F0F0F0"><a menu="open" href="javascript:;">打开</a></li>'+
						'<li>'+
							'<a menu="move" href="javascript:;">移动到<b class="arrow">»</b></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="moveto" desk="1" href="javascript:;">桌面1</a></li>'+
								'<li><a menu="moveto" desk="2" href="javascript:;">桌面2</a></li>'+
								'<li><a menu="moveto" desk="3" href="javascript:;">桌面3</a></li>'+
								'<li><a menu="moveto" desk="4" href="javascript:;">桌面4</a></li>'+
								'<li><a menu="moveto" desk="5" href="javascript:;">桌面5</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="edit" href="javascript:;"><b class="edit"></b>编辑</a></li>'+
						'<li><a menu="del" href="javascript:;"><b class="uninstall"></b>卸载</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuApp);
			}
			$('.app-menu a[menu="moveto"]').removeClass('disabled');
			if(obj.parent().hasClass('desktop-apps-container')){
				$('.app-menu a[menu="moveto"]').each(function(){
					if($(this).attr('desk') == HROS.CONFIG.desk){
						$(this).addClass('disabled');
					}
				});
			}
			//绑定事件
			$('.app-menu a[menu="moveto"]').off('click').on('click', function(){
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
									type : 'POST',
									url : ajaxUrl,
									data : 'ac=moveMyApp&movetype=dock-otherdesk&id=' + id + '&from=' + from + '&todesk=' + todesk
								}).done(function(responseText){
									HROS.VAR.isAppMoving = false;
								});
							}
						}else if(obj.parent().hasClass('desktop-apps-container')){
							if(HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk)){
								$.ajax({
									type : 'POST',
									url : ajaxUrl,
									data : 'ac=moveMyApp&movetype=desk-otherdesk&id=' + id + '&from=' + from + '&to=' + to + '&todesk=' + todesk + '&fromdesk=' + fromdesk
								}).done(function(responseText){
									HROS.VAR.isAppMoving = false;
								});
							}
						}else{
							if(HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid)){
								$.ajax({
									type : 'POST',
									url : ajaxUrl,
									data : 'ac=moveMyApp&movetype=folder-otherdesk&id=' + id + '&from=' + from + '&todesk=' + todesk + '&fromfolderid=' + fromfolderid
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
				HROS.window.create(obj.attr('realappid'), obj.attr('type'));
				$('.popup-menu').hide();
			});
			$('.app-menu a[menu="edit"]').off('click').on('click', function(){
				if(HROS.base.checkLogin()){
					$.dialog.open('sysapp/dialog/app.php?id=' + obj.attr('appid'), {
						id : 'editdialog',
						title : '编辑应用“' + obj.children('span').text() + '”',
						width : 600,
						height : 350
					});
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
						opacity : 'toggle',
						width : 0,
						height : 0
					}, 500, function(){
						obj.remove();
						HROS.deskTop.resize();
					});
				});
				$('.popup-menu').hide();
			});
			return TEMP.popupMenuApp;
		},
		papp : function(obj){
			HROS.window.show2under();
			if(!TEMP.popupMenuPapp){
				TEMP.popupMenuPapp = $(
					'<div class="popup-menu papp-menu"><ul>'+
						'<li style="border-bottom:1px solid #F0F0F0"><a menu="open" href="javascript:;">打开</a></li>'+
						'<li>'+
							'<a menu="move" href="javascript:;">移动到<b class="arrow">»</b></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="moveto" desk="1" href="javascript:;">桌面1</a></li>'+
								'<li><a menu="moveto" desk="2" href="javascript:;">桌面2</a></li>'+
								'<li><a menu="moveto" desk="3" href="javascript:;">桌面3</a></li>'+
								'<li><a menu="moveto" desk="4" href="javascript:;">桌面4</a></li>'+
								'<li><a menu="moveto" desk="5" href="javascript:;">桌面5</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="edit" href="javascript:;"><b class="edit"></b>编辑</a></li>'+
						'<li><a menu="del" href="javascript:;"><b class="del"></b>删除</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuPapp);
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
			$('.papp-menu a[menu="moveto"]').off('click').on('click', function(){
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
								type : 'POST',
								url : ajaxUrl,
								data : 'ac=moveMyApp&movetype=dock-otherdesk&id=' + id + '&from=' + from + '&todesk=' + todesk
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else if(obj.parent().hasClass('desktop-apps-container')){
						if(HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk)){
							$.ajax({
								type : 'POST',
								url : ajaxUrl,
								data : 'ac=moveMyApp&movetype=desk-otherdesk&id=' + id + '&from=' + from + '&to=' + to + '&todesk=' + todesk + '&fromdesk=' + fromdesk
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else{
						if(HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid)){
							$.ajax({
								type : 'POST',
								url : ajaxUrl,
								data : 'ac=moveMyApp&movetype=folder-otherdesk&id=' + id + '&from=' + from + '&todesk=' + todesk + '&fromfolderid=' + fromfolderid
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
					case 'papp':
						HROS.window.create(obj.attr('realappid'), obj.attr('type'));
						break;
					case 'pwidget':
						HROS.widget.create(obj.attr('realappid'), obj.attr('type'));
						break;
				}
				$('.popup-menu').hide();
			});
			$('.papp-menu a[menu="edit"]').off('click').on('click', function(){
				if(HROS.base.checkLogin()){
					$.dialog.open('sysapp/dialog/papp.php?id=' + obj.attr('appid'), {
						id : 'editdialog',
						title : '编辑私人应用“' + obj.children('span').text() + '”',
						width : 600,
						height : 450
					});
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
						opacity : 'toggle',
						width : 0,
						height : 0
					}, 500, function(){
						obj.remove();
						HROS.deskTop.resize();
					});
				});
				$('.popup-menu').hide();
			});
			return TEMP.popupMenuPapp;
		},
		/*
		**  文件夹右键
		*/
		folder : function(obj){
			HROS.window.show2under();
			if(!TEMP.popupMenuFolder){
				TEMP.popupMenuFolder = $(
					'<div class="popup-menu folder-menu"><ul>'+
						'<li><a menu="view" href="javascript:;">预览</a></li>'+
						'<li style="border-bottom:1px solid #F0F0F0"><a menu="open" href="javascript:;">打开</a></li>'+
						'<li>'+
							'<a menu="move" href="javascript:;">移动到<b class="arrow">»</b></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="moveto" desk="1" href="javascript:;">桌面1</a></li>'+
								'<li><a menu="moveto" desk="2" href="javascript:;">桌面2</a></li>'+
								'<li><a menu="moveto" desk="3" href="javascript:;">桌面3</a></li>'+
								'<li><a menu="moveto" desk="4" href="javascript:;">桌面4</a></li>'+
								'<li><a menu="moveto" desk="5" href="javascript:;">桌面5</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="rename" href="javascript:;"><b class="edit"></b>重命名</a></li>'+
						'<li><a menu="del" href="javascript:;"><b class="del"></b>删除</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuFolder);
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
				HROS.window.create(obj.attr('realappid'), obj.attr('type'));
				$('.popup-menu').hide();
			});
			$('.folder-menu a[menu="moveto"]').off('click').on('click', function(){
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
								type : 'POST',
								url : ajaxUrl,
								data : 'ac=moveMyApp&movetype=dock-otherdesk&id=' + id + '&from=' + from + '&todesk=' + todesk
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else if(obj.parent().hasClass('desktop-apps-container')){
						if(HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk)){
							$.ajax({
								type : 'POST',
								url : ajaxUrl,
								data : 'ac=moveMyApp&movetype=desk-otherdesk&id=' + id + '&from=' + from + '&to=' + to + '&todesk=' + todesk + '&fromdesk=' + fromdesk
							}).done(function(responseText){
								HROS.VAR.isAppMoving = false;
							});
						}
					}else{
						if(HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid)){
							$.ajax({
								type : 'POST',
								url : ajaxUrl,
								data : 'ac=moveMyApp&movetype=folder-otherdesk&id=' + id + '&from=' + from + '&todesk=' + todesk + '&fromfolderid=' + fromfolderid
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
					$.dialog({
						id : 'addfolder',
						title : '重命名“' + obj.find('span').text() + '”文件夹',
						padding : 0,
						content : editFolderDialogTemp({
							'name' : obj.find('span').text(),
							'src' : obj.find('img').attr('src')
						}),
						ok : function(){
							if($('#folderName').val() != ''){
								$.ajax({
									type : 'POST',
									url : ajaxUrl,
									data : 'ac=updateFolder&name=' + $('#folderName').val() + '&icon=' + $('.folderSelector img').attr('src') + '&id=' + obj.attr('appid')
								}).done(function(responseText){
									HROS.app.get();
								});
							}else{
								$('.folderNameError').show();
								return false;
							}
						},
						cancel : true
					});
					$('.folderSelector').off('click').on('click', function(){
						$('.fcDropdown').show();
					});
					$('.fcDropdown_item').off('click').on('click', function(){
						$('.folderSelector img').attr('src', $(this).children('img').attr('src')).attr('idx', $(this).children('img').attr('idx'));
						$('.fcDropdown').hide();
					});
				}else{
					HROS.base.login();
				}
				$('.popup-menu').hide();
			});
			$('.folder-menu a[menu="del"]').off('click').on('click', function(){
				$.dialog({
					id : 'delfolder',
					title : '删除“' + obj.find('span').text() + '”文件夹',
					content : '删除文件夹的同时会删除文件夹内所有应用',
					icon : 'warning',
					ok : function(){
						HROS.app.remove(obj.attr('appid'), function(){
							HROS.app.dataDeleteByAppid(obj.attr('appid'));
							obj.find('img, span').show().animate({
								opacity : 'toggle',
								width : 0,
								height : 0
							}, 500, function(){
								obj.remove();
								HROS.deskTop.resize();
							});
						});
					},
					cancel : true
				});
				$('.popup-menu').hide();
			});
			return TEMP.popupMenuFolder;
		},
		/*
		**  文件右键
		*/
		file : function(obj){
			HROS.window.show2under();
			if(!TEMP.popupMenuFile){
				TEMP.popupMenuFile = $(
					'<div class="popup-menu file-menu"><ul>'+
						'<li style="border-bottom:1px solid #F0F0F0"><a menu="download" href="javascript:;">下载</a></li>'+
						'<li><a menu="del" href="javascript:;"><b class="del"></b>删除</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuFile);
			}
			//绑定事件
			$('.file-menu a[menu="download"]').off('click').on('click', function(){
				$('body').append(fileDownloadTemp({
					appid : obj.attr('appid')
				}));
				$('.popup-menu').hide();
			});
			$('.file-menu a[menu="del"]').off('click').on('click', function(){
				HROS.app.dataDeleteByAppid(obj.attr('appid'));
				HROS.app.remove(obj.attr('appid'), function(){
					obj.find('img, span').show().animate({
						opacity : 'toggle',
						width : 0,
						height : 0
					}, 500, function(){
						obj.remove();
						HROS.deskTop.resize();
					});
				});
				$('.popup-menu').hide();
			});
			return TEMP.popupMenuFile;
		},
		/*
		**  应用码头右键
		*/
		dock : function(){
			HROS.window.show2under();
			if(!TEMP.popupMenuDock){
				TEMP.popupMenuDock = $(
					'<div class="popup-menu dock-menu"><ul>'+
						'<li><a menu="dockPos" pos="top" href="javascript:;"><b class="hook"></b>向上停靠</a></li>'+
						'<li><a menu="dockPos" pos="left" href="javascript:;"><b class="hook"></b>向左停靠</a></li>'+
						'<li><a menu="dockPos" pos="right" href="javascript:;"><b class="hook"></b>向右停靠</a></li>'+
						'<li><a menu="dockPos" pos="none" href="javascript:;">隐藏</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuDock);
				//绑定事件
				$('.dock-menu a[menu="dockPos"]').on('click', function(){
					if($(this).attr('pos') == 'none'){
						$.dialog({
							title : '温馨提示',
							icon : 'warning',
							content : '<p>如果应用码头存在应用，隐藏后会将应用转移到当前桌面。</p><p>若需要再次开启，可在桌面空白处点击右键，进入「 桌面设置 」里开启。</p>',
							ok : function(){
								HROS.dock.updatePos('none');
							},
							cancel : true
						});
					}else{
						HROS.dock.updatePos($(this).attr('pos'));
					}
					$('.popup-menu').hide();
				});
			}
			$('.dock-menu a[menu="dockPos"]').each(function(){
				$(this).children('.hook').hide();
				if($(this).attr('pos') == HROS.CONFIG.dockPos){
					$(this).children('.hook').show();
				}
				$('.popup-menu').hide();
			});
			return TEMP.popupMenuDock;
		},
		/*
		**  任务栏右键
		*/
		task : function(obj){
			HROS.window.show2under();
			if(!TEMP.popupMenuTask){
				TEMP.popupMenuTask = $(
					'<div class="popup-menu task-menu"><ul>'+
						'<li><a menu="show" href="javascript:;">还原</a></li>'+
						'<li style="border-bottom:1px solid #F0F0F0"><a menu="hide" href="javascript:;">最小化</a></li>'+
						'<li><a menu="close" href="javascript:;">关闭</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuTask);
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
			return TEMP.popupMenuTask;
		},
		/*
		**  桌面右键
		*/
		desk : function(){
			HROS.window.show2under();
			if(!TEMP.popupMenuDesk){
				TEMP.popupMenuDesk = $(
					'<div class="popup-menu desk-menu"><ul>'+
						'<li><a menu="hideall" href="javascript:;">显示桌面</a></li>'+
						'<li style="border-bottom:1px solid #F0F0F0"><a menu="closeall" href="javascript:;">关闭所有窗口</a></li>'+
						'<li>'+
							'<a href="javascript:;">新建<b class="arrow">»</b></a>'+
							'<div class="popup-menu"><ul>'+
								'<li><a menu="addfolder" href="javascript:;"><b class="folder"></b>新建文件夹</a></li>'+
								'<li><a menu="addpapp" href="javascript:;"><b class="customapp"></b>新建私人应用</a></li>'+
							'</ul></div>'+
						'</li>'+
						'<li style="border-bottom:1px solid #F0F0F0"><b class="upload"></b><a menu="uploadfile" href="javascript:;">上传文件</a></li>'+
						'<li><a menu="themes" href="javascript:;"><b class="themes"></b>主题设置</a></li>'+
						'<li><a menu="setting" href="javascript:;"><b class="setting"></b>桌面设置</a></li>'+
						'<li style="border-bottom:1px solid #F0F0F0">'+
							'<a href="javascript:;">图标设置<b class="arrow">»</b></a>'+
							'<div class="popup-menu"><ul>'+
								'<li>'+
									'<a href="javascript:;">排列<b class="arrow">»</b></a>'+
									'<div class="popup-menu"><ul>'+
										'<li><a menu="orderby" orderby="x" href="javascript:;"><b class="hook"></b>横向排列</a></li>'+
										'<li><a menu="orderby" orderby="y" href="javascript:;"><b class="hook"></b>纵向排列</a></li>'+
									'</ul></div>'+
								'</li>'+
								'<li>'+
									'<a href="javascript:;">尺寸<b class="arrow">»</b></a>'+
									'<div class="popup-menu"><ul>'+
										'<li><a menu="size" size="s" href="javascript:;"><b class="hook"></b>小图标</a></li>'+
										'<li><a menu="size" size="m" href="javascript:;"><b class="hook"></b>大图标</a></li>'+
									'</ul></div>'+
								'</li>'+
							'</ul></div>'+
						'</li>'+
						'<li><a menu="lock" href="javascript:;">锁定</a></li>'+
						'<li><a menu="logout" href="javascript:;">注销</a></li>'+
					'</ul></div>'
				);
				$('body').append(TEMP.popupMenuDesk);
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
						$.dialog({
							id : 'addfolder',
							title : '新建文件夹',
							padding : 0,
							content : editFolderDialogTemp({
								'name' : '新建文件夹',
								'src' : 'img/ui/folder_default.png'
							}),
							ok : function(){
								if($('#folderName').val() != ''){
									$.ajax({
										type : 'POST',
										url : ajaxUrl,
										data : 'ac=addFolder&name=' + $('#folderName').val() + '&icon=' + $('.folderSelector img').attr('src') + '&desk=' + HROS.CONFIG.desk
									}).done(function(responseText){
										HROS.app.get();
									});
								}else{
									$('.folderNameError').show();
									return false;
								}
							},
							cancel : true
						});
						$('.folderSelector').on('click', function(){
							$('#addfolder .fcDropdown').show();
							return false;
						});
						$(document).click(function(){
							$('#addfolder .fcDropdown').hide();
						});
						$('.fcDropdown_item').on('click', function(){
							$('.folderSelector img').attr('src', $(this).children('img').attr('src')).attr('idx', $(this).children('img').attr('idx'));
							$('#addfolder .fcDropdown').hide();
						});
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="addpapp"]').on('click', function(){
					if(HROS.base.checkLogin()){
						$.dialog.open('sysapp/dialog/papp.php?desk=' + HROS.CONFIG.desk, {
							id : 'editdialog',
							title : '新建私人应用',
							width : 600,
							height : 450
						});
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="uploadfile"]').on('click', function(){
					HROS.window.createTemp({
						appid : 'hoorayos-scwj',
						title : '上传文件',
						url : 'sysapp/upload/index.php',
						width : 750,
						height : 600,
						isflash : false
					});
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="themes"]').on('click', function(){
					if(HROS.base.checkLogin()){
						HROS.window.createTemp({
							appid : 'hoorayos-ztsz',
							title : '主题设置',
							url : 'sysapp/wallpaper/index.php',
							width : 580,
							height : 520,
							isflash : false
						});
					}else{
						HROS.base.login();
					}
					$('.popup-menu').hide();
				});
				$('.desk-menu a[menu="setting"]').on('click', function(){
					if(HROS.base.checkLogin()){
						HROS.window.createTemp({
							appid : 'hoorayos-zmsz',
							title : '桌面设置',
							url : 'sysapp/desksetting/index.php',
							width : 750,
							height : 450,
							isflash : false
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
				$(this).children('.hook').hide();
				if($(this).attr('orderby') == HROS.CONFIG.appXY){
					$(this).children('.hook').show();
				}
				$('.popup-menu').hide();
			});
			$('.desk-menu a[menu="size"]').each(function(){
				$(this).children('.hook').hide();
				if($(this).attr('size') == HROS.CONFIG.appSize){
					$(this).children('.hook').show();
				}
				$('.popup-menu').hide();
			});
			return TEMP.popupMenuDesk;
		},
		hide : function(){
			$('.popup-menu').hide();
		}
	}
})();