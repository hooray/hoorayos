/*
**  应用布局格子
**  这篇文章里有简单说明格子的作用
**  http://www.cnblogs.com/hooray/archive/2012/03/23/2414410.html
*/
HROS.grid = (function(){
	return {
		getAppGrid : function(){
			var width, height;
			width = $('#desk-' + HROS.CONFIG.desk).width() - HROS.CONFIG.appButtonLeft;
			height = $('#desk-' + HROS.CONFIG.desk).height() - HROS.CONFIG.appButtonTop;
			var appGrid = [], top = HROS.CONFIG.appButtonTop, left = HROS.CONFIG.appButtonLeft;
			var offsetTop = 100, offsetLeft = 120;
			if(HROS.CONFIG.appSize == 's'){
				offsetTop = 80;
				offsetLeft = 100;
			}
			for(var i = 0; i < 10000; i++){
				appGrid.push({
					startY : top,
					endY : top + offsetTop,
					startX : left,
					endX : left + offsetLeft
				});
				if(HROS.CONFIG.appXY == 'x'){
					left += offsetLeft;
					if(left + 100 > width){
						top += offsetTop;
						left = HROS.CONFIG.appButtonLeft;
					}
				}else{
					top += offsetTop;
					if(top + 70 > height){
						top = HROS.CONFIG.appButtonTop;
						left += offsetLeft;
					}
				}
			}
			return appGrid;
		},
		searchAppGrid : function(x, y){
			var grid = HROS.grid.getAppGrid(), j = grid.length;
			var flags = 0, appLength = $('#desk-' + HROS.CONFIG.desk + ' li.appbtn:not(.add)').length - 1;
			for(var i = 0; i < j; i++){
				if(x >= grid[i].startX && x <= grid[i].endX){
					flags += 1;
				}
				if(y >= grid[i].startY && y <= grid[i].endY){
					flags += 1;
				}
				if(flags === 2){
					return i > appLength ? appLength : i;
				}else{
					flags = 0;
				}
			}
			return null;
		},
		getDockAppGrid : function(){
			var height = $('#dock-bar .dock-applist').height();
			var dockAppGrid = [], left = 0, top = 0;
			for(var i = 0; i < 7; i++){
				dockAppGrid.push({
					startY : top,
					endY : top + 62,
					startX : left,
					endX : left + 62
				});
				top += 62;
				if(top + 62 > height){
					top = 0;
					left += 62;
				}
			}
			return dockAppGrid;
		},
		searchDockAppGrid : function(x, y){
			var grid = HROS.grid.getDockAppGrid(), j = grid.length, flags = 0,
				appLength = $('#dock-bar .dock-applist li').length - 1;
			for(var i = 0; i < j; i++){
				if(x >= grid[i].startX && x <= grid[i].endX){
					flags += 1;
				}
				if(y >= grid[i].startY && y <= grid[i].endY){
					flags += 1;
				}
				if(flags === 2){
					return i > appLength ? appLength : i;
				}else{
					flags = 0;
				}
			}
			return null;
		},
		getFolderGrid : function(){
			var folderGrid = [];
			$('.quick_view_container, .folder-window:visible').each(function(){
				folderGrid.push({
					zIndex : $(this).css('z-index'),
					id : $(this).attr('appid'),
					startY : $(this).offset().top,
					endY : $(this).offset().top + $(this).height(),
					startX :  $(this).offset().left,
					endX :  $(this).offset().left +  $(this).width()
				});
			});
			folderGrid.sort(function(x, y){
				return y['zIndex'] - x['zIndex'];
			});
			return folderGrid;
		},
		searchFolderGrid : function(x, y){
			var folderGrid = HROS.grid.getFolderGrid(), j = folderGrid.length, flags = 0;
			for(var i = 0; i < j; i++){
				if(x >= folderGrid[i].startX && x <= folderGrid[i].endX){
					flags += 1;
				}
				if(y >= folderGrid[i].startY && y <= folderGrid[i].endY){
					flags += 1;
				}
				if(flags === 2){
					return folderGrid[i]['id'];
				}else{
					flags = 0;
				}
			}
			return null;
		},
		getManageDockAppGrid : function(){
			var manageDockAppGrid = [], _left = 20;
			for(var i = 0; i < 10000; i++){
				manageDockAppGrid.push({
					startX : _left,
					endX : _left + 72
				});
				_left += 72;
			}
			return manageDockAppGrid;
		},
		getManageDockAppGridOnMove : function(){
			var manageDockAppGrid = [], _left = 20;
			for(var i = 0; i < 10000; i++){
				manageDockAppGrid.push({
					startX : _left,
					endX : _left + (i == 0 ? 29 : 72)
				});
				_left += (i == 0 ? 29 : 72);
			}
			//调试代码
			//for(var i = 0; i < 100; i++){$('body').append('<div style="position:absolute;width:1px;height:80px;border-left:1px solid #000;top:0;left:'+manageDockAppGrid[i].startX+'px"></div>');}
			return manageDockAppGrid;
		},
		searchManageDockAppGrid : function(x){
			var grid = HROS.grid.getManageDockAppGridOnMove(), flags = 0;
			var returnInfo = 0;
			for(var i = 0; i < grid.length; i++){
				if(x >= grid[i].startX && x <= grid[i].endX){
					flags += 1;
				}
				if(flags === 1){
					returnInfo = i;
					break;
				}else{
					flags = 0;
				}
			}
			return returnInfo;
		},
		getManageAppGrid : function(){
			var manageAppGrid = [], _top = 0;
			for(var i = 0; i < 10000; i++){
				manageAppGrid.push({
					startY : _top,
					endY : _top + 40
				});
				_top += 40;
			}
			return manageAppGrid;
		},
		getManageAppGridOnMove : function(){
			var manageAppGrid = [], _top = 0;
			for(var i = 0; i < 10000; i++){
				manageAppGrid.push({
					startY : _top,
					endY : _top + (i == 0 ? 20 : 40)
				});
				_top += (i == 0 ? 20 : 40);
			}
			return manageAppGrid;
		},
		searchManageAppGrid : function(y){
			var grid = HROS.grid.getManageAppGridOnMove(), flags = 0;
			var returnInfo = 0;
			for(var i = 0; i < grid.length; i++){
				if(y >= grid[i].startY && y <= grid[i].endY){
					flags += 1;
				}
				if(flags === 1){
					returnInfo = i;
					break;
				}else{
					flags = 0;
				}
			}
			return returnInfo;
		}
	}
})();