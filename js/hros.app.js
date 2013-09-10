/*
**  应用
*/
HROS.app = (function(){
	return {
		/*
		**  初始化桌面应用
		*/
		init : function(){
			//绑定'应用市场'点击事件
			$('#desk').on('click', 'li.add', function(){
				HROS.window.createTemp({
					appid : 'hoorayos-yysc',
					title : '应用市场',
					url : 'sysapp/appmarket/index.php',
					width : 800,
					height : 484,
					isflash : false
				});
			});
			//绑定应用拖动事件
			HROS.app.move();
			//绑定滚动条拖动事件
			HROS.app.moveScrollbar();
			//绑定应用右击事件
			$('body').on('contextmenu', '.appbtn:not(.add)', function(e){
				HROS.popupMenu.hide();
				var popupmenu;
				switch($(this).attr('type')){
					case 'app':
					case 'widget':
						popupmenu = HROS.popupMenu.app($(this));
						break;
					case 'papp':
					case 'pwidget':
						popupmenu = HROS.popupMenu.papp($(this));
						break;
					case 'folder':
						popupmenu = HROS.popupMenu.folder($(this));
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
			HROS.app.get();
		},
		/*
		**  更新应用排列方式
		*/
		updateXY : function(i){
			if(HROS.CONFIG.appXY != i){
				HROS.CONFIG.appXY = i;
				HROS.deskTop.appresize();
				if(HROS.base.checkLogin()){
					$.ajax({
						type : 'POST',
						url : ajaxUrl,
						data : 'ac=setAppXY&appxy=' + i
					});
				}
			}
		},
		updateSize : function(i){
			if(HROS.CONFIG.appSize != i){
				HROS.CONFIG.appSize = i;
				HROS.deskTop.appresize();
				if(HROS.base.checkLogin()){
					$.ajax({
						type : 'POST',
						url : ajaxUrl,
						data : 'ac=setAppSize&appsize=' + i
					});
				}
			}
		},
		get : function(){
			//获取json数组并循环输出每个应用
			$.ajax({
				type : 'POST',
				url : ajaxUrl,
				data : 'ac=getMyApp',
				beforeSend : function(){
					HROS.VAR.isAppMoving = true;
				}
			}).done(function(sc){
				HROS.VAR.isAppMoving = false;
				sc = $.parseJSON(sc);
				HROS.VAR.dock = sc['dock'];
				HROS.VAR.desk1 = sc['desk1'];
				HROS.VAR.desk2 = sc['desk2'];
				HROS.VAR.desk3 = sc['desk3'];
				HROS.VAR.desk4 = sc['desk4'];
				HROS.VAR.desk5 = sc['desk5'];
				HROS.VAR.folder = sc['folder'];
				//输出桌面应用
				HROS.app.set();
			});
		},
		/*
		**  输出应用
		*/
		set : function(){
			if($('#desktop').css('display') !== 'none'){
				switch(HROS.CONFIG.appSize){
					case 's':
						$('#desk').removeClass('smallIcon').addClass('smallIcon');
						break;
					case 'm':
						$('#desk').removeClass('smallIcon');
						break;
				}
				//绘制应用表格
				var grid = HROS.grid.getAppGrid(), dockGrid = HROS.grid.getDockAppGrid();
				//加载应用码头应用
				if(HROS.VAR.dock != ''){
					var dock_append = '';
					$(HROS.VAR.dock).each(function(i){
						dock_append += appbtnTemp({
							'top' : dockGrid[i]['startY'],
							'left' : dockGrid[i]['startX'],
							'title' : this.name,
							'type' : this.type,
							'id' : 'd_' + this.appid,
							'appid' : this.appid,
							'realappid' : this.realappid == 0 ? this.appid : this.realappid,
							'imgsrc' : this.icon
						});
					});
					$('#dock-bar .dock-applist').html('').append(dock_append);
				}else{
					$('#dock-bar .dock-applist').html('');
				}
				//加载桌面应用
				for(var j = 1; j <= 5; j++){
					var desk_append = '', desk = eval('HROS.VAR.desk' + j);
					if(desk != ''){
						$(desk).each(function(i){
							desk_append += appbtnTemp({
								'top' : grid[i]['startY'] + 7,
								'left' : grid[i]['startX'] + 16,
								'title' : this.name,
								'type' : this.type,
								'id' : 'd_' + this.appid,
								'appid' : this.appid,
								'realappid' : this.realappid == 0 ? this.appid : this.realappid,
								'imgsrc' : this.icon
							});
						});
					}
					desk_append += addbtnTemp({
						'top' : grid[desk.length]['startY'] + 7,
						'left' : grid[desk.length]['startX'] + 16
					});
					$('#desk-' + j + ' li').remove();
					$('#desk-' + j).append(desk_append);
				}
				HROS.deskTop.appresize();
				//如果文件夹预览面板为显示状态，则进行更新
				HROS.folderView.resize();
				//如果文件夹窗口为显示状态，则进行更新
				$('#desk .folder-window').each(function(){
					HROS.window.updateFolder($(this).attr('appid'));
				});
				//加载滚动条
				HROS.app.getScrollbar();
			}else{
				HROS.appmanage.init();
			}
		},
		/*
		**  添加应用
		*/
		add : function(id, callback){
			function done(){
				callback && callback();
			}
			if(HROS.base.checkLogin()){
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=addMyApp&id=' + id + '&desk=' + HROS.CONFIG.desk
				}).done(function(responseText){
					done();
				});
			}else{
				done();
			}
		},
		/*
		**  删除应用
		*/
		remove : function(id, callback){
			function done(){
				HROS.widget.removeCookie(id);
				callback && callback();
			}
			if(HROS.base.checkLogin()){
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=delMyApp&id=' + id
				}).done(function(responseText){
					done();
				});
			}else{
				done();
			}
		},
		/*
		**  应用拖动、打开
		**  这块代码略多，主要处理了9种情况下的拖动，分别是：
		**  桌面拖动到应用码头、桌面拖动到文件夹内、当前桌面上拖动(排序)
		**  应用码头拖动到桌面、应用码头拖动到文件夹内、应用码头上拖动(排序)
		**  文件夹内拖动到桌面、文件夹内拖动到应用码头、不同文件夹之间拖动
		*/
		move : function(){
			//应用码头应用拖动
			$('#dock-bar .dock-applist').on('mousedown', 'li', function(e){
				e.preventDefault();
				e.stopPropagation();
				if(e.button == 0 || e.button == 1){
					var oldobj = $(this), x, y, cx, cy, dx, dy, lay, obj = $('<li id="shortcut_shadow">' + oldobj.html() + '</li>');
					dx = cx = e.clientX;
					dy = cy = e.clientY;
					x = dx - oldobj.offset().left;
					y = dy - oldobj.offset().top;
					//绑定鼠标移动事件
					$(document).on('mousemove', function(e){
						$('body').append(obj);
						lay = HROS.maskBox.desk();
						lay.show();
						cx = e.clientX <= 0 ? 0 : e.clientX >= $(window).width() ? $(window).width() : e.clientX;
						cy = e.clientY <= 0 ? 0 : e.clientY >= $(window).height() ? $(window).height() : e.clientY;
						_l = cx - x;
						_t = cy - y;
						if(dx != cx || dy != cy){
							obj.css({
								left : _l,
								top : _t
							}).show();
						}
					}).on('mouseup', function(){
						$(document).off('mousemove').off('mouseup');
						obj.remove();
						if(typeof(lay) !== 'undefined'){
							lay.hide();
						}
						//判断是否移动应用，如果没有则判断为click事件
						if(dx == cx && dy == cy){
							switch(oldobj.attr('type')){
								case 'app':
								case 'papp':
									HROS.window.create(oldobj.attr('realappid'), oldobj.attr('type'));
									break;
								case 'widget':
								case 'pwidget':
									HROS.widget.create(oldobj.attr('realappid'), oldobj.attr('type'));
									break;
								case 'folder':
									HROS.folderView.get(oldobj);
									break;
							}
							return false;
						}
						var folderId = HROS.grid.searchFolderGrid(cx, cy);
						if(folderId != null){
							if(oldobj.hasClass('folder') == false){
								var id = oldobj.attr('appid'),
									from = oldobj.index(),
									to = folderId;
								if(HROS.base.checkLogin()){
									if(!HROS.app.checkIsMoving()){
										if(HROS.app.dataDockToFolder(id, from, to)){
											$.ajax({
												type : 'POST',
												url : ajaxUrl,
												data : 'ac=moveMyApp&movetype=dock-folder&id=' + id + '&from=' + from + '&to=' + to
											}).done(function(responseText){
												HROS.VAR.isAppMoving = false;
											});
										}
									}
								}else{
									HROS.app.dataDockToFolder(id, from, to);
								}
							}
						}else{
							var icon, icon2;
							var iconIndex = $('#desk-' + HROS.CONFIG.desk + ' li.appbtn:not(.add)').length == 0 ? -1 : $('#desk-' + HROS.CONFIG.desk + ' li').index(oldobj);
							var iconIndex2 = $('#dock-bar .dock-applist').html() == '' ? -1 : $('#dock-bar .dock-applist li').index(oldobj);
							
							var dock_w2 = HROS.CONFIG.dockPos == 'left' ? 0 : HROS.CONFIG.dockPos == 'top' ? ($(window).width() - $('#dock-bar .dock-applist').width() - 20) / 2 : $(window).width() - $('#dock-bar .dock-applist').width();
							var dock_h2 = HROS.CONFIG.dockPos == 'top' ? 0 : ($(window).height() - $('#dock-bar .dock-applist').height() - 20) / 2;
							icon2 = HROS.grid.searchDockAppGrid(cx - dock_w2, cy - dock_h2);
							if(icon2 != null){
								if(icon2 != oldobj.index()){
									var id = oldobj.attr('appid'),
										from = oldobj.index(),
										to = icon2;
									if(HROS.base.checkLogin()){
										if(!HROS.app.checkIsMoving()){
											if(HROS.app.dataDockToDock(id, from, to)){
												$.ajax({
													type : 'POST',
													url : ajaxUrl,
													data : 'ac=moveMyApp&movetype=dock-dock&id=' + id + '&from=' + from + '&to=' + to
												}).done(function(responseText){
													HROS.VAR.isAppMoving = false;
												});
											}
										}
									}else{
										HROS.app.dataDockToDock(id, from, to);
									}
								}
							}else{
								var dock_w = HROS.CONFIG.dockPos == 'left' ? 73 : 0;
								var dock_h = HROS.CONFIG.dockPos == 'top' ? 73 : 0;
								icon = HROS.grid.searchAppGrid(cx - dock_w, cy - dock_h);
								if(icon != null){
									var id = oldobj.attr('appid'),
										from = oldobj.index(),
										to = icon + 1,
										desk = HROS.CONFIG.desk;
									if(HROS.base.checkLogin()){
										if(!HROS.app.checkIsMoving()){
											if(HROS.app.dataDockToDesk(id, from, to, desk)){
												$.ajax({
													type : 'POST',
													url : ajaxUrl,
													data : 'ac=moveMyApp&movetype=dock-desk&id=' + id + '&from=' + from + '&to=' + to + '&desk=' + desk
												}).done(function(responseText){
													HROS.VAR.isAppMoving = false;
												});
											}
										}
									}else{
										HROS.app.dataDockToDesk(id, from, to, desk);
									}
								}
							}
						}
					});
				}
			});
			//桌面应用拖动
			$('#desktop .desktop-container').on('mousedown', 'li:not(.add)', function(e){
				e.preventDefault();
				e.stopPropagation();
				if(e.button == 0 || e.button == 1){
					var oldobj = $(this), x, y, cx, cy, dx, dy, lay, obj = $('<li id="shortcut_shadow">' + oldobj.html() + '</li>');
					dx = cx = e.clientX;
					dy = cy = e.clientY;
					x = dx - oldobj.offset().left;
					y = dy - oldobj.offset().top;
					//绑定鼠标移动事件
					$(document).on('mousemove', function(e){
						$('body').append(obj);
						lay = HROS.maskBox.desk();
						lay.show();
						cx = e.clientX <= 0 ? 0 : e.clientX >= $(window).width() ? $(window).width() : e.clientX;
						cy = e.clientY <= 0 ? 0 : e.clientY >= $(window).height() ? $(window).height() : e.clientY;
						_l = cx - x;
						_t = cy - y;
						if(dx != cx || dy != cy){
							obj.css({
								left : _l,
								top : _t
							}).show();
						}
					}).on('mouseup', function(){
						$(document).off('mousemove').off('mouseup');
						obj.remove();
						if(typeof(lay) !== 'undefined'){
							lay.hide();
						}
						//判断是否移动应用，如果没有则判断为click事件
						if(dx == cx && dy == cy){
							switch(oldobj.attr('type')){
								case 'app':
								case 'papp':
									HROS.window.create(oldobj.attr('realappid'), oldobj.attr('type'));
									break;
								case 'widget':
								case 'pwidget':
									HROS.widget.create(oldobj.attr('realappid'), oldobj.attr('type'));
									break;
								case 'folder':
									HROS.folderView.get(oldobj);
									break;
							}
							return false;
						}
						var folderId = HROS.grid.searchFolderGrid(cx, cy);
						if(folderId != null){
							if(oldobj.attr('type') != 'folder'){
								var id = oldobj.attr('appid'),
									from = oldobj.index() - 2,
									to = folderId,
									desk = HROS.CONFIG.desk;
								if(HROS.base.checkLogin()){
									if(!HROS.app.checkIsMoving()){
										if(HROS.app.dataDeskToFolder(id, from, to, desk)){
											$.ajax({
												type : 'POST',
												url : ajaxUrl,
												data : 'ac=moveMyApp&movetype=desk-folder&id=' + id + '&from=' + from + '&to=' + to + '&desk=' + desk
											}).done(function(responseText){
												HROS.VAR.isAppMoving = false;
											});
										}
									}
								}else{
									HROS.app.dataDeskToFolder(id, from, to, desk);
								}
							}
						}else{
							var icon, icon2;
							var iconIndex = $('#desk-' + HROS.CONFIG.desk + ' li.appbtn:not(.add)').length == 0 ? -1 : $('#desk-' + HROS.CONFIG.desk + ' li').index(oldobj);
							var iconIndex2 = $('#dock-bar .dock-applist').html() == '' ? -1 : $('#dock-bar .dock-applist li').index(oldobj);
							
							var dock_w2 = HROS.CONFIG.dockPos == 'left' ? 0 : HROS.CONFIG.dockPos == 'top' ? ($(window).width() - $('#dock-bar .dock-applist').width() - 20) / 2 : $(window).width() - $('#dock-bar .dock-applist').width();
							var dock_h2 = HROS.CONFIG.dockPos == 'top' ? 0 : ($(window).height() - $('#dock-bar .dock-applist').height() - 20) / 2;
							icon2 = HROS.grid.searchDockAppGrid(cx - dock_w2, cy - dock_h2);
							if(icon2 != null){
								var id = oldobj.attr('appid'),
									from = oldobj.index() - 2,
									to = icon2 + 1,
									desk = HROS.CONFIG.desk;
								if(HROS.base.checkLogin()){
									if(!HROS.app.checkIsMoving()){
										if(HROS.app.dataDeskToDock(id, from, to, desk)){
											$.ajax({
												type : 'POST',
												url : ajaxUrl,
												data : 'ac=moveMyApp&movetype=desk-dock&id=' + id + '&from=' + from + '&to=' + to + '&desk=' + desk
											}).done(function(responseText){
												HROS.VAR.isAppMoving = false;
											});
										}
									}
								}else{
									HROS.app.dataDeskToDock(id, from, to, desk);
								}
							}else{
								var dock_w = HROS.CONFIG.dockPos == 'left' ? 73 : 0;
								var dock_h = HROS.CONFIG.dockPos == 'top' ? 73 : 0;
								icon = HROS.grid.searchAppGrid(cx - dock_w, cy - dock_h);
								if(icon != null && icon != (oldobj.index() - 2)){
									var id = oldobj.attr('appid'),
										from = oldobj.index() - 2,
										to = icon,
										desk = HROS.CONFIG.desk;
									if(HROS.base.checkLogin()){
										if(!HROS.app.checkIsMoving()){
											if(HROS.app.dataDeskToDesk(id, from, to, desk)){
												$.ajax({
													type : 'POST',
													url : ajaxUrl,
													data : 'ac=moveMyApp&movetype=desk-desk&id=' + id + '&from=' + from + '&to=' + to + '&desk=' + desk
												}).done(function(responseText){
													HROS.VAR.isAppMoving = false;
												});
											}
										}
									}else{
										HROS.app.dataDeskToDesk(id, from, to, desk);
									}
								}
							}
						}
					});
				}
			});
			//文件夹内应用拖动
			$('body').on('mousedown', '.folder_body li, .quick_view_container li', function(e){
				e.preventDefault();
				e.stopPropagation();
				if(e.button == 0 || e.button == 1){
					var oldobj = $(this), x, y, cx, cy, dx, dy, lay, obj = $('<li id="shortcut_shadow">' + oldobj.html() + '</li>');
					dx = cx = e.clientX;
					dy = cy = e.clientY;
					x = dx - oldobj.offset().left;
					y = dy - oldobj.offset().top;
					//绑定鼠标移动事件
					$(document).on('mousemove', function(e){
						$('body').append(obj);
						lay = HROS.maskBox.desk();
						lay.show();
						cx = e.clientX <= 0 ? 0 : e.clientX >= $(window).width() ? $(window).width() : e.clientX;
						cy = e.clientY <= 0 ? 0 : e.clientY >= $(window).height() ? $(window).height() : e.clientY;
						_l = cx - x;
						_t = cy - y;
						if(dx != cx || dy != cy){
							obj.css({
								left : _l,
								top : _t
							}).show();
						}
					}).on('mouseup', function(){
						$(document).off('mousemove').off('mouseup');
						obj.remove();
						if(typeof(lay) !== 'undefined'){
							lay.hide();
						}
						//判断是否移动应用，如果没有则判断为click事件
						if(dx == cx && dy == cy){
							switch(oldobj.attr('type')){
								case 'app':
								case 'papp':
									HROS.window.create(oldobj.attr('realappid'), oldobj.attr('type'));
									break;
								case 'widget':
								case 'pwidget':
									HROS.widget.create(oldobj.attr('realappid'), oldobj.attr('type'));
									break;
							}
							return false;
						}
						var folderId = HROS.grid.searchFolderGrid(cx, cy);
						if(folderId != null){
							if(oldobj.parents('.folder-window').attr('appid') != folderId){
								var id = oldobj.attr('appid'),
									from = oldobj.index(),
									to = folderId,
									fromFolderId = oldobj.parents('.folder-window').attr('appid') || oldobj.parents('.quick_view_container').attr('appid');
								if(HROS.base.checkLogin()){
									if(!HROS.app.checkIsMoving()){
										if(HROS.app.dataFolderToFolder(id, from, to, fromFolderId)){
											$.ajax({
												type : 'POST',
												url : ajaxUrl,
												data : 'ac=moveMyApp&movetype=folder-folder&id=' + id + '&from=' + from + '&to=' + to
											}).done(function(responseText){
												HROS.VAR.isAppMoving = false;
											});
										}
									}
								}else{
									HROS.app.dataFolderToFolder(id, from, to, fromFolderId);
								}
							}
						}else{
							var icon, icon2;
							var iconIndex = $('#desk-' + HROS.CONFIG.desk + ' li.appbtn:not(.add)').length == 0 ? -1 : $('#desk-' + HROS.CONFIG.desk + ' li').index(oldobj);
							var iconIndex2 = $('#dock-bar .dock-applist').html() == '' ? -1 : $('#dock-bar .dock-applist li').index(oldobj);
							
							var dock_w2 = HROS.CONFIG.dockPos == 'left' ? 0 : HROS.CONFIG.dockPos == 'top' ? ($(window).width() - $('#dock-bar .dock-applist').width() - 20) / 2 : $(window).width() - $('#dock-bar .dock-applist').width();
							var dock_h2 = HROS.CONFIG.dockPos == 'top' ? 0 : ($(window).height() - $('#dock-bar .dock-applist').height() - 20) / 2;
							icon2 = HROS.grid.searchDockAppGrid(cx - dock_w2, cy - dock_h2);
							if(icon2 != null){
								var id = oldobj.attr('appid'),
									from = oldobj.index(),
									to = icon2 + 1,
									fromFolderId = oldobj.parents('.folder-window').attr('appid') || oldobj.parents('.quick_view_container').attr('appid'),
									desk = HROS.CONFIG.desk;
								if(HROS.base.checkLogin()){
									if(!HROS.app.checkIsMoving()){
										if(HROS.app.dataFolderToDock(id, from, to, fromFolderId, desk)){
											$.ajax({
												type : 'POST',
												url : ajaxUrl,
												data : 'ac=moveMyApp&movetype=folder-dock&id=' + id + '&to=' + from + '&desk=' + desk
											}).done(function(responseText){
												HROS.VAR.isAppMoving = false;
											});
										}
									}
								}else{
									HROS.app.dataFolderToDock(id, from, to, fromFolderId, desk);
								}
							}else{
								var dock_w = HROS.CONFIG.dockPos == 'left' ? 73 : 0;
								var dock_h = HROS.CONFIG.dockPos == 'top' ? 73 : 0;
								icon = HROS.grid.searchAppGrid(cx - dock_w, cy - dock_h);
								if(icon != null){
									var id = oldobj.attr('appid'),
										from = oldobj.index(),
										to = icon + 1,
										fromFolderId = oldobj.parents('.folder-window').attr('appid') || oldobj.parents('.quick_view_container').attr('appid'),
										desk = HROS.CONFIG.desk;
									if(HROS.base.checkLogin()){
										if(!HROS.app.checkIsMoving()){
											if(HROS.app.dataFolderToDesk(id, from, to, fromFolderId, desk)){
												$.ajax({
													type : 'POST',
													url : ajaxUrl,
													data : 'ac=moveMyApp&movetype=folder-desk&id=' + id + '&to=' + to + '&desk=' + desk
												}).done(function(responseText){
													HROS.VAR.isAppMoving = false;
												});
											}
										}
									}else{
										HROS.app.dataFolderToDesk(id, from, to, fromFolderId, desk);
									}
								}
							}
						}
					});
				}
			});
		},
		/*
		**  加载滚动条
		*/
		getScrollbar : function(){
			setTimeout(function(){
				$('#desk .desktop-container').each(function(){
					var desk = $(this), scrollbar = desk.children('.scrollbar');
					//先清空所有附加样式
					scrollbar.hide();
					desk.scrollLeft(0).scrollTop(0);
					/*
					**  判断应用排列方式
					**  横向排列超出屏幕则出现纵向滚动条，纵向排列超出屏幕则出现横向滚动条
					*/
					if(HROS.CONFIG.appXY == 'x'){
						/*
						**  获得桌面应用定位好后的实际高度
						**  因为显示的高度是固定的，而实际的高度是根据应用个数会变化
						*/
						var deskH = parseInt(desk.children('.add').css('top')) + 108;
						/*
						**  计算滚动条高度
						**  高度公式（应用纵向排列计算滚动条宽度以此类推）：
						**  滚动条实际高度 = 桌面显示高度 / 桌面实际高度 * 滚动条总高度(桌面显示高度)
						**  如果“桌面显示高度 / 桌面实际高度 >= 1”说明应用个数未能超出桌面，则不需要出现滚动条
						*/
						if(desk.height() / deskH < 1){
							desk.children('.scrollbar-y').height(desk.height() / deskH * desk.height()).css('top',0).show();
						}
					}else{
						var deskW = parseInt(desk.children('.add').css('left')) + 106;
						if(desk.width() / deskW < 1){
							desk.children('.scrollbar-x').width(desk.width() / deskW * desk.width()).css('left',0).show();
						}
					}
				});
			}, 500);
		},
		/*
		**  移动滚动条
		*/
		moveScrollbar : function(){
			/*
			**  手动拖动
			*/
			$('#desk .scrollbar').on('mousedown', function(e){
				var x, y, cx, cy, deskrealw, deskrealh, movew, moveh;
				var scrollbar = $(this), desk = scrollbar.parent('.desktop-container');
				deskrealw = parseInt(desk.children('.add').css('left')) + 106;
				deskrealh = parseInt(desk.children('.add').css('top')) + 108;
				movew = desk.width() - scrollbar.width();
				moveh = desk.height() - scrollbar.height();
				if(scrollbar.hasClass('scrollbar-x')){
					x = e.clientX - scrollbar.offset().left;
				}else{
					y = e.clientY - scrollbar.offset().top;
				}
				$(document).on('mousemove', function(e){
					if(scrollbar.hasClass('scrollbar-x')){
						if(HROS.CONFIG.dockPos == 'left'){
							cx = e.clientX - x - 73 < 0 ? 0 : e.clientX - x - 73 > movew ? movew : e.clientX - x - 73;
						}else{
							cx = e.clientX - x < 0 ? 0 : e.clientX - x > movew ? movew : e.clientX - x;
						}
						scrollbar.css('left', cx / desk.width() * deskrealw + cx);
						desk.scrollLeft(cx / desk.width() * deskrealw);
					}else{
						if(HROS.CONFIG.dockPos == 'top'){
							cy = e.clientY - y - 73 < 0 ? 0 : e.clientY - y - 73 > moveh ? moveh : e.clientY - y - 73;
						}else{
							cy = e.clientY - y < 0 ? 0 : e.clientY - y > moveh ? moveh : e.clientY - y;
						}
						scrollbar.css('top', cy / desk.height() * deskrealh + cy);
						desk.scrollTop(cy / desk.height() * deskrealh);
					}
				}).on('mouseup', function(){
					$(this).off('mousemove').off('mouseup');
				});
			});
			/*
			**  鼠标滚动
			*/
			$('#desk .desktop-container').each(function(i){
				$('#desk-' + (i + 1)).on('mousewheel', function(event, delta){
					var desk = $(this);
					if(HROS.CONFIG.appXY == 'x'){
						var deskrealh = parseInt(desk.children('.add').css('top')) + 108, scrollupdown;
						/*
						**  delta == -1   往下
						**  delta == 1    往上
						**  200px 是鼠标滚轮每滚一次的距离
						*/
						if(delta < 0){
							scrollupdown = desk.scrollTop() + 200 > deskrealh - desk.height() ? deskrealh - desk.height() : desk.scrollTop() + 200;
						}else{
							scrollupdown = desk.scrollTop() - 200 < 0 ? 0 : desk.scrollTop() - 200;
						}
						desk.stop(false, true).animate({scrollTop : scrollupdown}, 300);
						desk.children('.scrollbar-y').stop(false, true).animate({
							top : scrollupdown / deskrealh * desk.height() + scrollupdown
						}, 300);
					}else{
						var deskrealw = parseInt(desk.children('.add').css('left')) + 106, scrollleftright;
						if(delta < 0){
							scrollleftright = desk.scrollLeft() + 200 > deskrealw - desk.width() ? deskrealw - desk.width() : desk.scrollLeft() + 200;
						}else{
							scrollleftright = desk.scrollLeft() - 200 < 0 ? 0 : desk.scrollLeft() - 200;
						}
						desk.stop(false, true).animate({scrollLeft : scrollleftright}, 300);
						desk.children('.scrollbar-x').stop(false, true).animate({
							left : scrollleftright / deskrealw * desk.width() + scrollleftright
						}, 300);
					}
				});
			});
		},
		checkIsMoving : function(){
			var rtn = false;
			if(HROS.VAR.isAppMoving){
				$.dialog({
					title : '温馨提示',
					icon : 'warning',
					content : '数据正在处理中，请稍后。',
					ok : true
				});
				rtn = true;
			}else{
				HROS.VAR.isAppMoving = true;
			}
			return rtn;
		},
		dataWarning : function(){
			$.dialog({
				title : '温馨提示',
				icon : 'warning',
				content : '数据错误，请刷新后重试。',
				ok : true
			});
		},
		dataDockToFolder : function(id, from, to){
			var rtn = false;
			$(HROS.VAR.dock).each(function(i){
				if(this.appid == id){
					$(HROS.VAR.folder).each(function(j){
						if(this.appid == to){
							HROS.VAR.folder[j].apps.push(HROS.VAR.dock[i]);
							HROS.VAR.folder[j].apps = HROS.VAR.folder[j].apps.sortBy(function(n){
								return n.appid;
							}, true);
							HROS.VAR.dock.splice(i, 1);
							rtn = true;
							return false;
						}
					});
					return false;
				}
			});
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDockToDock : function(id, from, to){
			var rtn = false;
			if(from > to){
				if(HROS.VAR.dock[from] != null){
					HROS.VAR.dock.splice(to, 0, HROS.VAR.dock[from]);
					HROS.VAR.dock.splice(from + 1, 1);
					rtn = true;
				}
			}else if(from < to){
				if(HROS.VAR.dock[to] != null){
					HROS.VAR.dock.splice(to + 1, 0, HROS.VAR.dock[from]);
					HROS.VAR.dock.splice(from, 1);
					rtn = true;
				}
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDockToDesk : function(id, from, to, desk){
			var rtn = false;
			desk = eval('HROS.VAR.desk' + desk);
			if(HROS.VAR.dock[from] != null){
				desk.splice(to, 0, HROS.VAR.dock[from]);
				HROS.VAR.dock.splice(from, 1);
				rtn = true;
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDockToOtherdesk : function(id, from, todesk){
			var rtn = false;
			todesk = eval('HROS.VAR.desk' + todesk);
			if(HROS.VAR.dock[from] != null){
				todesk.push(HROS.VAR.dock[from]);
				HROS.VAR.dock.splice(from, 1);
				rtn = true;
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDockToDelete : function(id, from){
			var rtn = false;
			if(HROS.VAR.dock[from] != null){
				HROS.VAR.dock.splice(from, 1);
				rtn = true;
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDeskToFolder : function(id, from, to, desk){
			var rtn = false;
			desk = eval('HROS.VAR.desk' + desk);
			$(HROS.VAR.folder).each(function(i){
				if(this.appid == to && desk[from] != null){
					HROS.VAR.folder[i].apps.push(desk[from]);
					HROS.VAR.folder[i].apps = HROS.VAR.folder[i].apps.sortBy(function(n){
						return n.appid;
					}, true);
					desk.splice(from, 1);
					rtn = true;
					return false;
				}
			});
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDeskToDock : function(id, from, to, desk){
			var rtn = false;
			desk = eval('HROS.VAR.desk' + desk);
			if(desk[from] != null){
				HROS.VAR.dock.splice(to, 0, desk[from]);
				desk.splice(from, 1);
				if(HROS.VAR.dock.length > 7){
					desk.push(HROS.VAR.dock[7]);
					HROS.VAR.dock.splice(7, 1);
				}
				rtn = true;
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDeskToDesk : function(id, from, to, desk){
			var rtn = false;
			desk = eval('HROS.VAR.desk' + desk);
			if(from > to){
				if(desk[from] != null){
					desk.splice(to, 0, desk[from]);
					desk.splice(from + 1, 1);
					rtn = true;
				}
			}else if(from < to){
				if(desk[to] != null){
					desk.splice(to + 1, 0, desk[from]);
					desk.splice(from, 1);
					rtn = true;
				}
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataDeskToOtherdesk : function(id, from, to, todesk, fromdesk){
			var rtn = false;
			fromdesk = eval('HROS.VAR.desk' + fromdesk);
			todesk = eval('HROS.VAR.desk' + todesk);
			if(fromdesk[from] != null){
				if(to != -1){
					todesk.splice(to, 0, fromdesk[from]);
				}else{
					todesk.push(fromdesk[from]);
				}
				fromdesk.splice(from, 1);
				rtn = true;
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataFolderToFolder : function(id, from, to, fromFolderId){
			var rtn = false, flags = 0, fromKey, toKey;
			$(HROS.VAR.folder).each(function(i){
				if(this.appid == fromFolderId && HROS.VAR.folder[i].apps[from] != null){
					fromKey = i;
					flags += 1;
				}
				if(this.appid == to){
					toKey = i;
					flags += 1;
				}
			});
			if(flags== 2){
				HROS.VAR.folder[toKey].apps.push(HROS.VAR.folder[fromKey].apps[from]);
				HROS.VAR.folder[toKey].apps = HROS.VAR.folder[toKey].apps.sortBy(function(n){
					return n.appid;
				}, true);
				HROS.VAR.folder[fromKey].apps.splice(from, 1);
				rtn = true;
			}
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataFolderToDock : function(id, from, to, fromFolderId, desk){
			var rtn = false;
			desk = eval('HROS.VAR.desk' + desk);
			$(HROS.VAR.folder).each(function(i){
				if(this.appid == fromFolderId && HROS.VAR.folder[i].apps[from] != null){
					HROS.VAR.dock.splice(to, 0, HROS.VAR.folder[i].apps[from]);
					HROS.VAR.folder[i].apps.splice(from, 1);
					if(HROS.VAR.dock.length > 7){
						desk.push(HROS.VAR.dock[7]);
						HROS.VAR.dock(7, 1);
					}
					rtn = true;
					return false;
				}
			});
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataFolderToDesk : function(id, from, to, fromFolderId, desk){
			var rtn = false;
			desk = eval('HROS.VAR.desk' + desk);
			$(HROS.VAR.folder).each(function(i){
				if(this.appid == fromFolderId && HROS.VAR.folder[i].apps[from] != null){
					desk.splice(to, 0, HROS.VAR.folder[i].apps[from]);
					HROS.VAR.folder[i].apps.splice(from, 1);
					rtn = true;
					return false;
				}
			});
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataFolderToOtherdesk : function(id, from, todesk, fromFolderId){
			var rtn = false;
			todesk = eval('HROS.VAR.desk' + todesk);
			$(HROS.VAR.folder).each(function(i){
				if(this.appid == fromFolderId && HROS.VAR.folder[i].apps[from] != null){
					todesk.push(HROS.VAR.folder[i].apps[from]);
					HROS.VAR.folder[i].apps.splice(from, 1);
					rtn = true;
					return false;
				}
			});
			if(rtn){
				if($('#desktop').is(':visible')){
					HROS.app.set();
				}else{
					HROS.appmanage.set();
				}
			}else{
				HROS.app.dataWarning();
			}
			return rtn;
		},
		dataAllDockToDesk : function(desk){
			desk = eval('HROS.VAR.desk' + desk);
			$(HROS.VAR.dock).each(function(i){
				desk.push(HROS.VAR.dock[i]);
			});
			HROS.VAR.dock.splice(0, HROS.VAR.dock.length);
		},
		dataDeleteByAppid : function(appid){
			$(HROS.VAR.dock).each(function(i){
				if(this.appid == appid){
					HROS.VAR.dock.splice(i, 1);
					return false;
				}
			});
			for(var i = 1; i <= 5; i++){
				var desk = eval('HROS.VAR.desk' + i);
				$(desk).each(function(j){
					if(this.appid == appid){
						desk.splice(j, 1);
						if(this.type == 'folder'){
							$(HROS.VAR.folder).each(function(k){
								if(this.appid == appid){
									HROS.VAR.folder.splice(k, 1);
									return false;
								}
							});
						}
						return false;
					}
				});
			}
			$(HROS.VAR.folder).each(function(i){
				$(this.apps).each(function(j){
					if(this.appid == appid){
						HROS.VAR.folder[i].apps.splice(j, 1);
						return false;
					}
				});
			});
		}
	}
})();