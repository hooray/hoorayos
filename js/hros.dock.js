/*
**  应用码头
*/
HROS.dock = (function(){
	return {
		/*
		**	初始化
		*/
		init : function(){
			HROS.dock.setPos();
			//绑定应用码头拖动事件
			HROS.dock.move();
			//绑定应用码头3个按钮的点击事件
			$('.dock-tool-setting').on('mousedown', function(){
				return false;
			}).on('click',function(){
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
			});
			$('.dock-tool-style').on('mousedown', function(){
				return false;
			}).on('click', function(){
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
			});
			$('.dock-tool-start').on('mousedown', function(){
				return false;
			}).on('click', function(){
				HROS.startmenu.show();
				return false;
			});
		},
		setPos : function(){
			HROS.navbar.switchDesk(HROS.CONFIG.desk);
			var desktop = $('#desk-' + HROS.CONFIG.desk), desktops = $('#desk .desktop-container');
			var desk_w = desktop.css('width', '100%').width(), desk_h = desktop.css('height', '100%').height();
			//清除dock位置样式
			$('#dock-container').removeClass('dock-top dock-left dock-right');
			$('#dock-bar').removeClass('top-bar left-bar right-bar').hide();
			if(HROS.CONFIG.dockPos == 'top'){
				$('#dock-bar').addClass('top-bar').children('#dock-container').addClass('dock-top');
				desktops.css({
					'width' : desk_w,
					'height' : desk_h - 143,
					'left' : desk_w,
					'top' : 73
				});
				desktop.css({
					'left' : 0
				});
				$('#dock-bar').show();
			}else if(HROS.CONFIG.dockPos == 'left'){
				$('#dock-bar').addClass('left-bar').children('#dock-container').addClass('dock-left');
				desktops.css({
					'width' : desk_w - 73,
					'height' : desk_h - 70,
					'left' : desk_w + 73,
					'top' : 0
				});
				desktop.css({
					'left' : 73
				});
				$('#dock-bar').show();
			}else if(HROS.CONFIG.dockPos == 'right'){
				$('#dock-bar').addClass('right-bar').children('#dock-container').addClass('dock-right');
				desktops.css({
					'width' : desk_w - 73,
					'height' : desk_h - 70,
					'left' : desk_w,
					'top' : 0
				});
				desktop.css({
					'left' : 0
				});
				$('#dock-bar').show();
			}else if(HROS.CONFIG.dockPos == 'none'){
				desktops.css({
					'width' : desk_w,
					'height' : desk_h - 70,
					'left' : desk_w,
					'top' : 0
				});
				desktop.css({
					'left' : 0
				});
			}
			HROS.taskbar.resize();
			HROS.folderView.resize();
		},
		updatePos : function(pos){
			if(pos != HROS.CONFIG.dockPos && typeof(pos) != 'undefined'){
				HROS.CONFIG.dockPos = pos;
				if(pos == 'none'){
					HROS.app.dataAllDockToDesk(HROS.CONFIG.desk);
				}
				//更新码头位置
				HROS.dock.setPos();
				//更新桌面应用
				HROS.app.set();
				if(HROS.base.checkLogin()){
					$.ajax({
						type : 'POST',
						url : ajaxUrl,
						data : 'ac=setDockPos&dock=' + pos + '&desk=' + HROS.CONFIG.desk
					});
				}
			}
		},
		move : function(){
			$('#dock-container').on('mousedown',function(e){
				if(e.button == 0 || e.button == 1){
					var lay = HROS.maskBox.dock(), location;
					$(document).on('mousemove', function(e){
						lay.show();
						if(e.clientY < lay.height() * 0.2){
							location = 'top';		
						}else if(e.clientX < lay.width() * 0.5){
							location = 'left';
						}else{				
							location = 'right';
						}
						$('.dock_drap_effect').removeClass('hover');
						$('.dock_drap_effect_' + location).addClass('hover');
					}).on('mouseup', function(){
						$(document).off('mousemove').off('mouseup');
						lay.hide();
						HROS.dock.updatePos(location);
					});
				}
			});
		}
	}
})();