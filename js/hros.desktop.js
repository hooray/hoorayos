/*
**  桌面
*/
HROS.deskTop = (function(){
	return {
		init : function(){
			//绑定浏览器resize事件
			$(window).on('resize', function(){
				HROS.deskTop.resize();
			});
			$('body').on('click', '#desktop', function(){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchbar.hide();
				HROS.startmenu.hide();
			}).on('contextmenu', '#desktop', function(e){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchbar.hide();
				HROS.startmenu.hide();
				var popupmenu = HROS.popupMenu.desk();
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
		**  处理浏览器改变大小后的事件
		*/
		resize : function(){
			if($('#desktop').is(':visible')){
				HROS.dock.setPos();
				//更新应用定位
				HROS.deskTop.appresize();
				//更新窗口定位
				HROS.deskTop.windowresize();
			}else{
				HROS.appmanage.resize();
			}
			HROS.wallpaper.set(false);
		},
		/*
		**  重新排列应用
		*/
		appresize : function(){
			switch(HROS.CONFIG.appSize){
				case 's':
					$('#desk').removeClass('smallIcon').addClass('smallIcon');
					break;
				case 'm':
					$('#desk').removeClass('smallIcon');
					break;
			}
			var grid = HROS.grid.getAppGrid(), dockGrid = HROS.grid.getDockAppGrid();
			$('#dock-bar .dock-applist li').each(function(i){
				$(this).css({
					'top' : HROS.CONFIG.dockPos == 'top' ? dockGrid[i]['startY'] : dockGrid[i]['startY'] + 5,
					'left' : HROS.CONFIG.dockPos == 'top' ? dockGrid[i]['startX'] + 5 : dockGrid[i]['startX']
				});
			});
			for(var j = 1; j <= 5; j++){
				$('#desk-' + j + ' li').each(function(i){
					var offsetTop = 7;
					var offsetLeft = 16;
					if(HROS.CONFIG.appSize == 's'){
						offsetTop = 11;
						offsetLeft = 21;
					}
					$(this).stop(true, false).animate({
						'top' : grid[i]['startY'] + offsetTop,
						'left' : grid[i]['startX'] + offsetLeft
					}, 500);
				});
			}
			//更新滚动条
			HROS.app.getScrollbar();
		},
		/*
		**  重新定位窗口位置
		*/
		windowresize : function(){
			$('#desk div.window-container').each(function(){
				var windowdata = $(this).data('info');
				currentW = $(window).width() - $(this).width();
				currentH = $(window).height() - $(this).height();
				var _l = windowdata['left'] / windowdata['emptyW'] * currentW >= currentW ? currentW : windowdata['left'] / windowdata['emptyW'] * currentW;
				_l = _l <= 0 ? 0 : _l;
				var _t = windowdata['top'] / windowdata['emptyH'] * currentH >= currentH ? currentH : windowdata['top'] / windowdata['emptyH'] * currentH;
				_t = _t <= 0 ? 0 : _t;
				if($(this).attr('state') != 'hide'){
					$(this).stop(true, false).animate({
						'left' : _l,
						'top' : _t
					}, 500, function(){
						windowdata['left'] = _l;
						windowdata['top'] = _t;
						windowdata['emptyW'] = $(window).width() - $(this).width();
						windowdata['emptyH'] = $(window).height() - $(this).height();
					});
				}else{
					windowdata['left'] = _l;
					windowdata['top'] = _t;
					windowdata['emptyW'] = $(window).width() - $(this).width();
					windowdata['emptyH'] = $(window).height() - $(this).height();
				}
			});
		},
		updateDefaultDesk : function(i){
			if(HROS.base.checkLogin()){
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=setDesk&desk=' + i
				});
			}
		}
	}
})();