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
		**  计算右键菜单位置
		*/
		calcPosition: function(e, obj){
			var l = ($(window).width() - e.clientX) < obj.width() ? (e.clientX - obj.width()): e.clientX;
			var t = ($(window).height() - e.clientY) < obj.height() ? (e.clientY - obj.height()): e.clientY;
			obj.css({
				left: l,
				top: t
			}).show();
		},
		/*
		**  应用右键
		*/
		app: function(e, obj){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuApp.art')){
				//绑定事件
				$('body').on('click', '.app-menu a[menu="moveto"]:not(.disabled)', function(){
					var data = $(this).parents('.app-menu').data();
					var id = data.obj.attr('appid'),
					from = data.obj.index(),
					to = 99999,
					todesk = $(this).attr('desk'),
					fromdesk = HROS.CONFIG.desk,
					fromfolderid = data.obj.parents('.folder-window').attr('appid') || data.obj.parents('.quick_view_container').attr('appid');
					if(HROS.base.checkLogin()){
						if(!HROS.app.checkIsMoving()){
							var rtn = false;
							if(data.obj.parent().hasClass('dock-applist')){
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
							}else if(data.obj.parent().hasClass('desktop-apps-container')){
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
							}else if(data.obj.parent().hasClass('folder_body')){
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
						if(data.obj.parent().hasClass('dock-applist')){
							HROS.app.dataDockToOtherdesk(id, from, todesk);
						}else if(data.obj.parent().hasClass('desktop-apps-container')){
							HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk);
						}else if(data.obj.parent().hasClass('folder_body')){
							HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid);
						}
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.app-menu a[menu="open"]', function(){
					var data = $(this).parents('.app-menu').data();
					switch(data.obj.attr('type')){
						case 'window':
							HROS.window.create(data.obj.attr('appid'), data.obj.attr('type'));
							break;
						case 'widget':
							HROS.widget.create(data.obj.attr('appid'), data.obj.attr('type'));
							break;
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.app-menu a[menu="edit"]', function(){
					var data = $(this).parents('.app-menu').data();
					if(HROS.base.checkLogin()){
						dialog({
							id: 'editdialog',
							title: '编辑应用“' + data.obj.attr('title') + '”',
							url: 'sysapp/dialog/app.php?id=' + data.obj.attr('appid'),
							padding: 0,
							width: 770,
							height: 450
						}).showModal();
					}else{
						HROS.base.login();
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.app-menu a[menu="del"]', function(){
					var data = $(this).parents('.app-menu').data();
					HROS.app.dataDeleteByAppid(data.obj.attr('appid'));
					HROS.widget.removeCookie(data.obj.attr('realappid'), data.obj.attr('type'));
					HROS.app.remove(data.obj.attr('appid'), function(){
						data.obj.find('img, span').show().animate({
							opacity: 'toggle',
							width: 0,
							height: 0
						}, 500, function(){
							data.obj.remove();
							HROS.deskTop.resize();
						});
					});
					HROS.popupMenu.hide();
				});
			}
			var data = {
				obj: obj,
				desk: HROS.CONFIG.desk
			};
			$('body').append(HROS.template.renderFile('popupMenuApp.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		papp: function(e, obj){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuPapp.art')){
				//绑定事件
				$('body').on('click', '.papp-menu a[menu="moveto"]:not(.disabled)', function(){
					var data = $(this).parents('.papp-menu').data();
					var id = data.obj.attr('appid'),
					from = data.obj.index(),
					to = 99999,
					todesk = $(this).attr('desk'),
					fromdesk = HROS.CONFIG.desk,
					fromfolderid = data.obj.parents('.folder-window').attr('appid') || data.obj.parents('.quick_view_container').attr('appid');
					if(HROS.base.checkLogin()){
						var rtn = false;
						if(data.obj.parent().hasClass('dock-applist')){
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
						}else if(data.obj.parent().hasClass('desktop-apps-container')){
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
						}else if(data.obj.parent().hasClass('folder_body')){
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
						if(data.obj.parent().hasClass('dock-applist')){
							HROS.app.dataDockToOtherdesk(id, from, todesk);
						}else if(data.obj.parent().hasClass('desktop-apps-container')){
							HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk);
						}else if(data.obj.parent().hasClass('folder_body')){
							HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid);
						}
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.papp-menu a[menu="open"]', function(){
					var data = $(this).parents('.papp-menu').data();
					switch(data.obj.attr('type')){
						case 'pwindow':
							HROS.window.create(data.obj.attr('appid'), data.obj.attr('type'));
							break;
						case 'pwidget':
							HROS.widget.create(data.obj.attr('appid'), data.obj.attr('type'));
							break;
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.papp-menu a[menu="edit"]', function(){
					var data = $(this).parents('.papp-menu').data();
					if(HROS.base.checkLogin()){
						dialog({
							id: 'editdialog',
							title: '编辑私人应用“' + data.obj.attr('title') + '”',
							url: 'sysapp/dialog/papp.php?id=' + data.obj.attr('appid'),
							padding: 0,
							width: 770,
							height: 450
						}).showModal();
					}else{
						HROS.base.login();
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.papp-menu a[menu="del"]', function(){
					var data = $(this).parents('.papp-menu').data();
					HROS.app.dataDeleteByAppid(data.obj.attr('appid'));
					HROS.widget.removeCookie(data.obj.attr('realappid'), data.obj.attr('type'));
					HROS.app.remove(data.obj.attr('appid'), function(){
						data.obj.find('img, span').show().animate({
							opacity: 'toggle',
							width: 0,
							height: 0
						}, 500, function(){
							data.obj.remove();
							HROS.deskTop.resize();
						});
					});
					HROS.popupMenu.hide();
				});
			}
			var data = {
				obj: obj,
				desk: HROS.CONFIG.desk
			};
			$('body').append(HROS.template.renderFile('popupMenuPapp.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		/*
		**  文件夹右键
		*/
		folder: function(e, obj){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuFolder.art')){
				//绑定事件
				$('body').on('click', '.folder-menu a[menu="view"]', function(){
					var data = $(this).parents('.folder-menu').data();
					HROS.folderView.get(data.obj);
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.folder-menu a[menu="open"]', function(){
					var data = $(this).parents('.folder-menu').data();
					HROS.window.create(data.obj.attr('appid'), data.obj.attr('type'));
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.folder-menu a[menu="moveto"]:not(.disabled)', function(){
					var data = $(this).parents('.folder-menu').data();
					var id = data.obj.attr('appid'),
					from = data.obj.index(),
					to = 99999,
					todesk = $(this).attr('desk'),
					fromdesk = HROS.CONFIG.desk,
					fromfolderid = data.obj.parents('.folder-window').attr('appid') || data.obj.parents('.quick_view_container').attr('appid');
					if(HROS.base.checkLogin()){
						var rtn = false;
						if(data.obj.parent().hasClass('dock-applist')){
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
						}else if(data.obj.parent().hasClass('desktop-apps-container')){
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
						if(data.obj.parent().hasClass('dock-applist')){
							HROS.app.dataDockToOtherdesk(id, from, todesk);
						}else if(data.obj.parent().hasClass('desktop-apps-container')){
							HROS.app.dataDeskToOtherdesk(id, from, to, 'a', todesk, fromdesk);
						}else{
							HROS.app.dataFolderToOtherdesk(id, from, todesk, fromfolderid);
						}
					}
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.folder-menu a[menu="rename"]', function(){
					var data = $(this).parents('.folder-menu').data();
					if(HROS.base.checkLogin()){
						swal({
							type: 'input',
							title: '重命名“' + data.obj.attr('title') + '”文件夹',
							showCancelButton: true,
							closeOnConfirm: false,
							confirmButtonText: '修改',
							cancelButtonText: '取消',
							animation: 'slide-from-top',
							inputPlaceholder: '请输入文件夹名称',
							inputValue: data.obj.attr('title')
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
									id: data.obj.attr('appid')
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
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.folder-menu a[menu="del"]', function(){
					var data = $(this).parents('.folder-menu').data();
					swal({
						type: 'warning',
						title: '删除“' + data.obj.attr('title') + '”文件夹',
						text: '删除文件夹的同时会删除文件夹内所有应用，确认要删除么？',
						showCancelButton: true,
						confirmButtonText: '确认删除',
						cancelButtonText: '我点错了'
					}, function(){
						HROS.app.remove(data.obj.attr('appid'), function(){
							HROS.app.dataDeleteByAppid(data.obj.attr('appid'));
							data.obj.find('img, span').show().animate({
								opacity: 'toggle',
								width: 0,
								height: 0
							}, 500, function(){
								data.obj.remove();
								HROS.deskTop.resize();
							});
						});
					});
					HROS.popupMenu.hide();
				});
			}
			var data = {
				obj: obj,
				desk: HROS.CONFIG.desk
			};
			$('body').append(HROS.template.renderFile('popupMenuFolder.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		/*
		**  文件右键
		*/
		file: function(e, obj){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuFile.art')){
				//绑定事件
				$('body').on('click', '.file-menu a[menu="download"]', function(){
					var data = $(this).parents('.file-menu').data();
					$('body').append(HROS.template.fileDownload({
						appid: data.obj.attr('appid')
					}));
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.file-menu a[menu="del"]', function(){
					var data = $(this).parents('.file-menu').data();
					HROS.app.dataDeleteByAppid(data.obj.attr('appid'));
					HROS.app.remove(data.obj.attr('appid'), function(){
						data.obj.find('img, span').show().animate({
							opacity: 'toggle',
							width: 0,
							height: 0
						}, 500, function(){
							data.obj.remove();
							HROS.deskTop.resize();
						});
					});
					HROS.popupMenu.hide();
				});
			}
			var data = {
				obj: obj
			};
			$('body').append(HROS.template.renderFile('popupMenuFile.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		/*
		**  应用码头右键
		*/
		dock: function(e){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuDock.art')){
				//绑定事件
				$('body').on('click', '.dock-menu a[menu="dockPos"]:not(.disabled)', function(){
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
					HROS.popupMenu.hide();
				});
			}
			var data = {
				pos: HROS.CONFIG.dockPos
			};
			$('body').append(HROS.template.renderFile('popupMenuDock.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		/*
		**  任务栏右键
		*/
		task: function(e, obj){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuTask.art')){
				//绑定事件
				$('body').on('click', '.task-menu a[menu="show"]:not(.disabled)', function(){
					var data = $(this).parents('.task-menu').data();
					HROS.window.show2top(data.obj.attr('appid'));
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.task-menu a[menu="hide"]:not(.disabled)', function(){
					var data = $(this).parents('.task-menu').data();
					HROS.window.hide(data.obj.attr('appid'));
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.task-menu a[menu="close"]', function(){
					var data = $(this).parents('.task-menu').data();
					HROS.window.close(data.obj.attr('appid'));
					HROS.popupMenu.hide();
				});
			}
			var data = {
				obj: obj,
				state: $('#w_' + obj.attr('appid')).attr('state')
			};
			$('body').append(HROS.template.renderFile('popupMenuTask.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		/*
		**  桌面右键
		*/
		desk: function(e){
			HROS.window.show2under();
			if(!HROS.template.checkCache('popupMenuDesk.art')){
				//绑定事件
				$('body').on('click', '.desk-menu a[menu="orderby"]:not(.disabled)', function(){
					HROS.app.updateXY($(this).attr('orderby'));
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="size"]:not(.disabled)', function(){
					HROS.app.updateSize($(this).attr('size'));
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="hideall"]', function(){
					HROS.window.hideAll();
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="closeall"]', function(){
					HROS.window.closeAll();
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="addfolder"]', function(){
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
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="addpapp"]', function(){
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
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="uploadfile"]', function(){
					HROS.window.createTemp({
						appid: 'hoorayos-scwj',
						title: '上传文件',
						url: 'sysapp/upload/index.php',
						width: 750,
						height: 600,
						isflash: false
					});
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="themes"]', function(){
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
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="setting"]', function(){
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
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="lock"]', function(){
					HROS.lock.show();
					HROS.popupMenu.hide();
				});
				$('body').on('click', '.desk-menu a[menu="logout"]', function(){
					HROS.base.logout();
					HROS.popupMenu.hide();
				});
			}
			var data = {
				xy: HROS.CONFIG.appXY,
				size: HROS.CONFIG.appSize
			};
			$('body').append(HROS.template.renderFile('popupMenuDesk.art', data));
			var popupMenu = $('body > .popup-menu:last-child');
			popupMenu.data(data);
			HROS.popupMenu.calcPosition(e, popupMenu);
		},
		hide: function(){
			$('.popup-menu').remove();
		}
	}
})();